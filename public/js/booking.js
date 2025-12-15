// Fichier: public/js/booking.js

// Définitions spécifiques au module de booking
const productMapJs = {
    'Accessoires': { type: 'accessory', description: 'Petits objets comme un sac à main, un ordinateur portable ou un casque.' },
    'Bagage cabine': { type: 'cabin', description: 'Valise de taille cabine, généralement jusqu\'à 55x35x25 cm.' },
    'Bagage soute': { type: 'hold', description: 'Grande valise enregistrée en soute.' },
    'Bagage spécial': { type: 'special', description: 'Objets volumineux ou hors format comme un équipement de sport ou un instrument de musique.' },
    'Vestiaire': { type: 'cloakroom', description: 'Pour les manteaux, vestes ou autres vêtements sur cintre.' }
};

let isPriorityAvailable = false;
let isPremiumAvailable = false;


/**
 * Affiche les dates sélectionnées dans la section de résumé.
 */
function displaySelectedDates() {
    const options = { month: 'short', day: 'numeric' };
    const depotDate = new Date(document.getElementById('date-depot').value).toLocaleDateString('fr-FR', options);
    const recupDate = new Date(document.getElementById('date-recuperation').value).toLocaleDateString('fr-FR', options);
    const depotHeure = document.getElementById('heure-depot').value;
    const recupHeure = document.getElementById('heure-recuperation').value;

    document.getElementById('display-date-depot').textContent = `${depotDate}, ${depotHeure}`;
    document.getElementById('display-date-recuperation').textContent = `${recupDate}, ${recupHeure}`;

    const airportSelect = document.getElementById('airport-select');
    const selectedAirportName = airportSelect.options[airportSelect.selectedIndex].text;
    document.getElementById('display-airport-name').textContent = selectedAirportName;
}


/**
 * Vérifie la disponibilité de l'agence à la date de dépôt.
 * @returns {Promise<boolean>}
 */
async function checkAvailability() {
    const spinner = document.getElementById('loading-spinner-availability');
    const btn = document.getElementById('check-availability-btn');
    spinner.style.display = 'inline-block';
    btn.disabled = true;

    const dateDepot = document.getElementById('date-depot').value;
    const heureDepot = document.getElementById('heure-depot').value;

    if (!airportId || !dateDepot || !heureDepot) {
        await showCustomAlert('Champs incomplets', 'Veuillez remplir tous les champs : aéroport, date et heure de dépôt.');
        spinner.style.display = 'none';
        btn.disabled = false;
        return false;
    }

    try {
        const depotDateTime = new Date(`${dateDepot}T${heureDepot}`);
        const pad = (num) => num.toString().padStart(2, '0');
        const dateToVerify = `${depotDateTime.getFullYear()}${pad(depotDateTime.getMonth() + 1)}${pad(depotDateTime.getDate())}T${pad(depotDateTime.getHours())}${pad(depotDateTime.getMinutes())}`;

        const response = await fetch('/api/check-availability', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: JSON.stringify({ idPlateforme: airportId, dateToCheck: dateToVerify })
        });

        const result = await response.json();
        if (result.statut === 1 && result.content === true) {
            return true;
        } else {
            await showCustomAlert('Agence fermée', "Notre agence est ouverte de 07h00 à 21h00 7/7. Pour toutes demandes hors horaire merci de nous contacter au +33 <strong>1 34 38 58 98</strong>.");
            return false;
        }
    } catch (error) {
        console.error('Erreur lors de la vérification de disponibilité:', error);
        await showCustomAlert('Erreur', 'Une erreur technique est survenue lors de la vérification de la disponibilité.');
        return false;
    } finally {
        spinner.style.display = 'none';
        btn.disabled = false;
    }
}


/**
 * Récupère le devis depuis l'API et met à jour l'affichage.
 */
async function getQuoteAndDisplay() {
    const cartSpinner = document.getElementById('loading-spinner-cart');
    if (cartSpinner) cartSpinner.style.display = 'inline-block';

    const dateDepot = document.getElementById('date-depot').value;
    const heureDepot = document.getElementById('heure-depot').value;
    const dateRecuperation = document.getElementById('date-recuperation').value;
    const heureRecuperation = document.getElementById('heure-recuperation').value;

    if (!dateDepot || !heureDepot || !dateRecuperation || !heureRecuperation) {
        await showCustomAlert('Attention', 'Veuillez vérifier les dates et heures de dépôt et de récupération.');
        if (cartSpinner) cartSpinner.style.display = 'none';
        return;
    }

    const debut = new Date(`${dateDepot}T${heureDepot}:00`);
    const fin = new Date(`${dateRecuperation}T${heureRecuperation}:00`);
    const dureeEnMinutes = Math.ceil(Math.abs(fin - debut) / (1000 * 60));

    if (dureeEnMinutes <= 0) {
        await showCustomAlert('Attention', 'La date de récupération doit être postérieure à la date de dépôt.');
        if (cartSpinner) cartSpinner.style.display = 'none';
        return;
    }

    try {
        const response = await fetch('/api/get-quote', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: JSON.stringify({ idPlateforme: airportId, idService: serviceId, duree: dureeEnMinutes })
        });

        const result = await response.json();
        if (result.statut === 1 && result.content) {
            globalProductsData = result.content.products || [];
            globalLieuxData = result.content.lieux || [];
            displayOptions(dureeEnMinutes);
            updateCartDisplay();
        } else {
            await showCustomAlert('Erreur de tarification', 'Erreur lors de la récupération des tarifs : ' + (result.message || 'Réponse invalide'));
        }
    } catch (error) {
        console.error('Erreur lors de la récupération des tarifs et lieux:', error);
        await showCustomAlert('Erreur', 'Une erreur technique est survenue lors de la récupération des tarifs.');
    } finally {
        if (cartSpinner) cartSpinner.style.display = 'none';
    }
}

/**
 * Gère le clic sur le bouton de paiement.
 */
async function handleTotalClick() {
    const loader = document.getElementById('loader');
    if (loader) loader.classList.remove('hidden');

    try {
        if (cartItems.length === 0) {
            await showCustomAlert('Panier vide', "Votre panier est vide.");
            if (loader) loader.classList.add('hidden');
            return;
        }
        
        // --- NOUVEAU : Appel API pour obtenir les prix dynamiques des options ---
        if (isPriorityAvailable || isPremiumAvailable) {
            const dateDepot = document.getElementById('date-depot').value;
            const heureDepot = document.getElementById('heure-depot').value;
            const dateRecuperation = document.getElementById('date-recuperation').value;
            const heureRecuperation = document.getElementById('heure-recuperation').value;

            // Assurez-vous que les baggages ont toutes les infos nécessaires pour l'API BDM
            const baggagesForOptionsQuote = cartItems.filter(i => i.itemCategory === 'baggage').map(item => {
                const product = globalProductsData.find(p => p.id === item.productId);
                return {
                    productId: item.productId,
                    serviceId: product ? product.idService : serviceId, // Utilise le serviceId du produit si dispo
                    dateDebut: `${dateDepot}T${heureDepot}:00Z`,
                    dateFin: `${dateRecuperation}T${heureRecuperation}:00Z`,
                    quantity: item.quantity
                };
            });

            try {
                const optionsQuoteResponse = await fetch('/api/commande/options-quote', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify({
                        idPlateforme: airportId,
                        cartItems: baggagesForOptionsQuote,
                        guestEmail: guestEmail,
                        dateDepot: dateDepot,
                        heureDepot: heureDepot,
                        dateRecuperation: dateRecuperation,
                        heureRecuperation: heureRecuperation,
                        globalProductsData: globalProductsData // Pour que le backend puisse mapper les produits
                    })
                });

                const optionsQuoteResult = await optionsQuoteResponse.json();

                if (optionsQuoteResult.statut === 1 && optionsQuoteResult.content) {
                    staticOptions.priority.prixUnitaire = optionsQuoteResult.content.priority.price;
                    staticOptions.premium.prixUnitaire = optionsQuoteResult.content.premium.price;
                } else {
                    await showCustomAlert('Erreur de tarification options', optionsQuoteResult.message || 'Impossible de récupérer les prix des options.');
                    if (loader) loader.classList.add('hidden');
                    return;
                }
            } catch (error) {
                console.error('Erreur lors de la récupération des prix des options:', error);
                await showCustomAlert('Erreur', 'Une erreur technique est survenue lors de la récupération des prix des options.');
                if (loader) loader.classList.add('hidden');
                return;
            }

            // Afficher la modale des options seulement après avoir récupéré les prix dynamiques
            const result = await showOptionsAdvertisementModal();
            if (result === 'cancelled') {
                if (loader) loader.classList.add('hidden');
                return;
            }
        }
        // --- FIN NOUVEAU ---

        const authResponse = await fetch('/check-auth-status');
        const authData = await authResponse.json();

        if (!authData.authenticated) {
            if (!guestEmail) {
                if (loader) loader.classList.add('hidden');
                await sleep(300);

                const choice = await showLoginOrGuestPrompt();

                if (choice === 'login') {
                    if (window.openLoginModal) {
                        window.openLoginModal();
                    } else {
                        await showCustomAlert('Erreur', 'Impossible d\'ouvrir la fenêtre de connexion.');
                    }
                    if (loader) loader.classList.add('hidden');
                    return;
                } else if (choice === 'guest') {
                    const email = await showCustomPrompt('Comment pouvons-nous vous joindre ?', 'C’est sur ce mail que vous recevrez la confirmation de réservation.', 'Adresse e-mail');
                    if (email) {
                        guestEmail = email;
                        saveStateToSession();
                        if (loader) loader.classList.remove('hidden');
                    } else {
                        if (loader) loader.classList.add('hidden');
                        return;
                    }
                } else {
                    if (loader) loader.classList.add('hidden');
                    return;
                }
            }
        } else {
            guestEmail = null;
            saveStateToSession();
        }

        const baggages = cartItems.filter(i => i.itemCategory === 'baggage').map(item => ({ type: item.type, quantity: item.quantity }));
        const options = cartItems.filter(i => i.itemCategory === 'option').map(item => ({ id: item.id, details: item.details || null }));

        const airportSelect = document.getElementById('airport-select');
        const airportName = airportSelect.options[airportSelect.selectedIndex].text;

        const formData = {
            airportId: airportId,
            airportName: airportName,
            dateDepot: document.getElementById('date-depot').value,
            heureDepot: document.getElementById('heure-depot').value,
            dateRecuperation: document.getElementById('date-recuperation').value,
            heureRecuperation: document.getElementById('heure-recuperation').value,
            baggages: baggages,
            products: globalProductsData,
            options: options,
            guest_email: guestEmail
        };

        const prepareResponse = await fetch('/prepare-payment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: JSON.stringify(formData)
        });

        const resultData = await prepareResponse.json();
        if (prepareResponse.ok) {
            await sleep(1900);
            window.location.href = resultData.redirect_url;
        } else {
            let errorMessage = resultData.message || 'Une erreur inconnue est survenue.';
            if (resultData.errors) {
                errorMessage += '<br><br>' + Object.values(resultData.errors).flat().join('<br>');
            }
            await showCustomAlert('Erreur de validation', errorMessage);
            if (loader) loader.classList.add('hidden');
        }
    } catch (error) {
        console.error('Erreur critique dans handleTotalClick:', error);
        await showCustomAlert('Erreur', 'Une erreur technique est survenue.');
        if (loader) loader.classList.add('hidden');
    }
}

/**
 * Applique les contraintes sur les champs de date et heure.
 */
function applyDateInputConstraints() {
    const dateDepotInput = document.getElementById('date-depot');
    const dateRecuperationInput = document.getElementById('date-recuperation');
    const heureDepotInput = document.getElementById('heure-depot');
    const heureRecuperationInput = document.getElementById('heure-recuperation');

    const today = new Date();
    const pad = (num) => num.toString().padStart(2, '0');
    const todayFormatted = `${today.getFullYear()}-${pad(today.getMonth() + 1)}-${pad(today.getDate())}`;

    dateDepotInput.min = todayFormatted;
    dateDepotInput.max = dateRecuperationInput.value || '';

    heureDepotInput.min = '07:01';
    heureDepotInput.max = '21:00';

    if (dateDepotInput.value === todayFormatted) {
        const nextHour = new Date().getHours() + 1;
        heureDepotInput.min = `${pad(Math.max(7, nextHour))}:00`;
    }

    if (dateDepotInput.value) {
        dateRecuperationInput.min = dateDepotInput.value;
    } else {
        dateRecuperationInput.min = todayFormatted;
    }

    heureRecuperationInput.min = '07:01';
    heureRecuperationInput.max = '21:00';

    if (dateDepotInput.value === dateRecuperationInput.value && heureDepotInput.value) {
        const [depotHour, depotMinute] = heureDepotInput.value.split(':').map(Number);
        let minRecuperationHour = depotHour + 3;
        if(minRecuperationHour < 21) {
             heureRecuperationInput.min = `${pad(minRecuperationHour)}:${pad(depotMinute)}`;
        } else {
             // Si le dépôt + 3h dépasse 21h, on ne peut pas récupérer le même jour
             // Il faudrait une logique plus complexe ici, pour l'instant on se contente de ça
        }
    }
    saveStateToSession();
}

/**
 * Point d'entrée principal, initialise tous les écouteurs d'événements.
 */
document.addEventListener('DOMContentLoaded', function () {
    // Chargement de l'état initial
    loadStateFromSession();
    
    // Initialisation des listeners pour les modales
    if(typeof setupQdmListeners !== 'undefined') setupQdmListeners();
    // Le setup des listeners de la modale custom est déjà dans modal.js

    // Initialisation des contraintes de date
    applyDateInputConstraints();

    // --- ÉCOUTEURS D'ÉVÉNEMENTS ---

    document.getElementById('back-to-step-1-btn').addEventListener('click', function () {
        document.getElementById('baggage-selection-step').style.display = 'none';
        document.getElementById('step-1').style.display = 'block';
        this.classList.add('hidden');
        saveStateToSession();
    });

    document.getElementById('airport-select').addEventListener('change', function () {
        airportId = this.value;
        saveStateToSession();
    });

    document.getElementById('check-availability-btn').addEventListener('click', async () => {
        saveStateToSession();
        const isAvailable = await checkAvailability();
        if (isAvailable) {
            document.getElementById('step-1').style.display = 'none';
            document.getElementById('baggage-selection-step').style.display = 'block';
            document.getElementById('back-to-step-1-btn').classList.remove('hidden');
            displaySelectedDates();
            getQuoteAndDisplay();
            saveStateToSession();
        }
    });

    // Listeners pour le panier (ajout/suppression d'articles)
    document.getElementById('baggage-grid-container').addEventListener('click', handleQuantityChange);
    document.getElementById('cart-items-container').addEventListener('click', (e) => {
        const target = e.target.closest('.delete-item-btn');
        if (target) {
            const index = parseInt(target.dataset.index, 10);
            cartItems.splice(index, 1);
            updateCartDisplay(); // Met à jour l'affichage et sauvegarde la session
        }
    });

    // Listeners pour les inputs de date/heure
    const dateInputs = ['date-depot', 'date-recuperation', 'heure-depot', 'heure-recuperation'];
    dateInputs.forEach(id => {
        const input = document.getElementById(id);
        input.addEventListener('change', applyDateInputConstraints); // 'change' est mieux que 'input' pour les contraintes
        input.addEventListener('input', saveStateToSession);
    });

    // Listener pour le tooltip
    const tooltip = document.getElementById('baggage-tooltip');
    const baggageSelectionStep = document.getElementById('baggage-selection-step');
    baggageSelectionStep.addEventListener('mouseover', (e) => {
        const target = e.target.closest('.info-icon');
        if (!target) return;
        const libelle = target.dataset.libelle;
        const productData = productMapJs[libelle];
        if (productData && productData.description) {
            tooltip.textContent = productData.description;
            tooltip.classList.remove('hidden');
            const rect = target.getBoundingClientRect();
            tooltip.style.left = `${rect.left + window.scrollX}px`;
            tooltip.style.top = `${rect.top + window.scrollY - tooltip.offsetHeight - 5}px`;
        }
    });
    baggageSelectionStep.addEventListener('mouseout', (e) => {
        if (e.target.closest('.info-icon')) {
            tooltip.classList.add('hidden');
        }
    });
});
