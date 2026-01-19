// =================================================================================
// == Fichier: public/js/modal.js
// == Description: Gère la logique pour toutes les modales du site.
// =================================================================================

let modalResolve;
let wasQuickDateModalOpen = false;

// =================================================================================
// == Fonctions génériques pour les modales (Alert, Confirm, Prompt)
// =================================================================================

/**
 * Cache la modale de date rapide si elle est ouverte, pour éviter les superpositions.
 */
function hideQuickDateModalIfOpen() {
    const quickDateModal = document.getElementById('quick-date-modal');
    if (quickDateModal && !quickDateModal.classList.contains('hidden')) {
        wasQuickDateModalOpen = true;
        quickDateModal.classList.add('hidden');
    } else {
        wasQuickDateModalOpen = false;
    }
}

/**
 * Affiche une modale d'alerte simple avec un titre, un message et un bouton "OK".
 * @param {string} title - Le titre de la modale.
 * @param {string} message - Le message (peut contenir du HTML).
 * @returns {Promise<boolean>} - Une promesse qui se résout quand la modale est fermée.
 */
function showCustomAlert(title, message) {
    hideQuickDateModalIfOpen();
    const modalOverlay = document.getElementById('custom-modal-overlay');
    if (!modalOverlay) return Promise.resolve(false);

    document.getElementById('custom-modal-title').textContent = title;
    document.getElementById('custom-modal-message').innerHTML = message;
    document.getElementById('custom-modal-prompt-container').classList.add('hidden');
    
    const footer = document.getElementById('custom-modal-footer');
    footer.innerHTML = `<button id="custom-modal-confirm-btn" class="bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover">OK</button>`;

    modalOverlay.classList.remove('hidden');

    return new Promise(resolve => {
        modalResolve = resolve;
        document.getElementById('custom-modal-confirm-btn').onclick = () => { 
            closeModal(); 
            modalResolve(true);
        };
    });
}

/**
 * Affiche une modale de confirmation avec un titre, un message et des boutons "Confirmer" et "Annuler".
 * @param {string} title - Le titre de la modale.
 * @param {string} message - Le message de confirmation.
 * @returns {Promise<boolean>} - Une promesse qui se résout à `true` si confirmé, `false` sinon.
 */
function showCustomConfirm(title, message) {
    hideQuickDateModalIfOpen();
    const modalOverlay = document.getElementById('custom-modal-overlay');
    if (!modalOverlay) return Promise.resolve(false);

    document.getElementById('custom-modal-title').textContent = title;
    document.getElementById('custom-modal-message').textContent = message;
    document.getElementById('custom-modal-prompt-container').classList.add('hidden');

    const footer = document.getElementById('custom-modal-footer');
    footer.innerHTML = `
        <button id="modal-btn-cancel-confirm" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-full btn-hover">Annuler</button>
        <button id="modal-btn-confirm-confirm" class="bg-red-600 text-white font-bold py-2 px-4 rounded-full btn-hover">Confirmer</button>
    `;
    
    modalOverlay.classList.remove('hidden');

    return new Promise(resolve => {
        modalResolve = resolve;
        document.getElementById('modal-btn-confirm-confirm').onclick = () => { closeModal(); modalResolve(true); };
        document.getElementById('modal-btn-cancel-confirm').onclick = () => { closeModal(); modalResolve(false); };
    });
}

/**
 * Affiche une modale avec un champ de saisie.
 * @param {string} title - Le titre de la modale.
 * @param {string} message - Le message d'instruction.
 * @param {string} label - Le label pour le champ de saisie.
 * @returns {Promise<string|null>} - Une promesse qui se résout avec la valeur saisie, ou null si annulé.
 */
function showCustomPrompt(title, message, label) {
    hideQuickDateModalIfOpen();
    const modalOverlay = document.getElementById('custom-modal-overlay');
    if (!modalOverlay) return Promise.resolve(null);
    
    document.getElementById('custom-modal-title').textContent = title;
    document.getElementById('custom-modal-message').textContent = message;
    
    const promptContainer = document.getElementById('custom-modal-prompt-container');
    promptContainer.classList.remove('hidden');
    document.getElementById('custom-modal-prompt-label').textContent = label;
    document.getElementById('custom-modal-input').value = '';
    document.getElementById('custom-modal-error').classList.add('hidden');

    const footer = document.getElementById('custom-modal-footer');
    footer.innerHTML = `
        <button id="custom-modal-cancel-btn" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-full btn-hover">Annuler</button>
        <button id="custom-modal-confirm-btn" class="bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover">Confirmer</button>
    `;

    modalOverlay.classList.remove('hidden');

    return new Promise(resolve => {
        modalResolve = resolve;
        const confirmBtn = document.getElementById('custom-modal-confirm-btn');
        const cancelBtn = document.getElementById('custom-modal-cancel-btn');
        const input = document.getElementById('custom-modal-input');

        confirmBtn.onclick = () => {
            const value = input.value;
            if (label.toLowerCase().includes('email') && (value.trim() === '' || !/^\S+@\S+\.\S+$/.test(value))) {
                document.getElementById('custom-modal-error').textContent = 'Veuillez entrer une adresse e-mail valide.';
                document.getElementById('custom-modal-error').classList.remove('hidden');
                return;
            }
            closeModal(); 
            modalResolve(value);
        };
        cancelBtn.onclick = () => { closeModal(); modalResolve(null); };
    });
}


/**
 * Affiche une modale pour choisir entre la connexion et continuer en tant qu'invité.
 * @returns {Promise<'login'|'guest'|null>}
 */
function showLoginOrGuestPrompt() {
    hideQuickDateModalIfOpen();
    const modalOverlay = document.getElementById('custom-modal-overlay');
    if (!modalOverlay) return Promise.resolve(null);

    document.getElementById('custom-modal-title').textContent = 'Comment souhaitez-vous procéder ?';
    document.getElementById('custom-modal-message').textContent = 'Connectez-vous pour utiliser vos informations enregistrées ou continuez en tant qu\'invité.';
    document.getElementById('custom-modal-prompt-container').classList.add('hidden');
    
    const footer = document.getElementById('custom-modal-footer');
    footer.innerHTML = `
        <button id="btn-continue-guest" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-full btn-hover">Continuer en invité</button>
        <button id="btn-login-modal" class="bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover">Se connecter</button>
    `;
    modalOverlay.classList.remove('hidden');

    return new Promise(resolve => {
        modalResolve = resolve;
        document.getElementById('btn-login-modal').onclick = () => { closeModal(); resolve('login'); };
        document.getElementById('btn-continue-guest').onclick = () => { closeModal(); resolve('guest'); };
    });
}

/**
 * Ferme la modale principale et nettoie l'état.
 */
function closeModal() {
    const modalOverlay = document.getElementById('custom-modal-overlay');
    if(modalOverlay) {
        modalOverlay.classList.add('hidden');
    }
    
    // Réinitialise le footer à son état par défaut pour les simples alertes
    const footer = document.getElementById('custom-modal-footer');
    if (footer) {
        footer.innerHTML = `<button id="custom-modal-confirm-btn" class="bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover">OK</button>`;
    }

    if (wasQuickDateModalOpen) {
        const quickDateModal = document.getElementById('quick-date-modal');
        if(quickDateModal) {
            quickDateModal.classList.remove('hidden');
        }
        wasQuickDateModalOpen = false;
    }
}

/**
 * Initialise les écouteurs d'événements globaux pour la modale principale.
 */
function setupGlobalModalListeners() {
    const modalOverlay = document.getElementById('custom-modal-overlay');
    const modalCloseBtn = document.getElementById('custom-modal-close');

    if (modalOverlay) {
        // Clic sur le fond pour fermer
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                closeModal();
                if (modalResolve) modalResolve(null); // Annulation
            }
        });
    }

    if (modalCloseBtn) {
        // Clic sur le bouton de fermeture (croix)
        modalCloseBtn.addEventListener('click', () => {
            closeModal();
            if (modalResolve) modalResolve(null); // Annulation
        });
    }
}

// =================================================================================
// == Fonctions spécifiques à la modale de publicité des options (Priority/Premium)
// =================================================================================

function displayOptions(dureeEnMinutes) {
    // Nouvelle condition pour Premium
    const dateDepot = document.getElementById('date-depot').value;
    const heureDepot = document.getElementById('heure-depot').value;
    const depotDateTime = new Date(`${dateDepot}T${heureDepot}`);
    const now = new Date();

    const diffInMs = depotDateTime - now;
    const diffInHours = diffInMs / (1000 * 60 * 60);

    const isDepotInFuture = diffInHours >= 72;
    const hasLieux = globalLieuxData.length > 0;

    isPremiumAvailable = hasLieux && isDepotInFuture;
}

function updateAdvertModalButtons() {
    ['priority', 'premium'].forEach(optionKey => {
        const addButton = document.getElementById(`add-${optionKey}-from-modal`);
        if (!addButton) return;

        const isInCart = cartItems.some(item => item.key === optionKey);
        addButton.disabled = false; // Always enable the button for toggling

        if (isInCart) {
            addButton.textContent = 'Enlever du panier';
            addButton.classList.remove('bg-transparent', 'border', 'border-gray-400', 'text-gray-700', 'hover:bg-gray-100');
            addButton.classList.add('bg-red-600', 'text-white');
        } else {
            addButton.textContent = 'Ajouter au panier';
            addButton.classList.remove('bg-red-600', 'text-white');
            addButton.classList.add('bg-transparent', 'border', 'border-gray-400', 'text-gray-700', 'hover:bg-gray-100');
        }
    });
}

function toggleOptionFromModal(optionKey) {
    const itemIndex = cartItems.findIndex(item => item.key === optionKey);

    if (itemIndex > -1) {
        // Item exists, so remove it
        cartItems.splice(itemIndex, 1);
    } else {
        // Item does not exist, so add it
        const option = staticOptions[optionKey];
        let premiumDetails = {};

        if (optionKey === 'premium') {
            const direction = document.querySelector('input[name="premium_direction"]:checked')?.value;
            if (!direction) {
                showCustomAlert('Sélection requise', 'Veuillez choisir un sens pour la prise en charge premium.');
                return; // Stop if no direction is selected
            }
            
            premiumDetails.direction = direction;
            const formId = `premium_fields_${direction}`;
            const formContainer = document.getElementById(formId);
            let isFormValid = true;

            if (formContainer) {
                // Reset borders first
                formContainer.querySelectorAll('[data-required="true"]').forEach(input => {
                    input.classList.remove('border-red-500');
                });
                
                // Check required fields that are visible
                formContainer.querySelectorAll('[data-required="true"]').forEach(input => {
                    // An element is visible if its offsetParent is not null
                    const isVisible = !!input.offsetParent;
                    if (isVisible && !input.value.trim()) {
                        isFormValid = false;
                        input.classList.add('border-red-500');
                    }
                });

                if(!isFormValid) {
                    showCustomAlert('Champs requis', 'Veuillez remplir tous les champs obligatoires (en rouge).');
                    return; 
                }

                // Gather all data from the active form
                formContainer.querySelectorAll('input, textarea, select').forEach(input => {
                    premiumDetails[input.name] = input.value;
                    if (input.tagName === 'SELECT' && (input.name === 'pickup_location_arrival' || input.name === 'restitution_location_departure')) {
                        const selectedOption = input.options[input.selectedIndex];
                        if (selectedOption) {
                            premiumDetails[input.name + '_libelle'] = selectedOption.text;
                        }
                    }
                });
            }

        }

        cartItems.push({
            itemCategory: 'option',
            id: option.id,
            key: optionKey,
            libelle: option.libelle,
            prix: option.prixUnitaire,
            details: premiumDetails
        });
    }
    
    updateCartDisplay();
    updateAdvertModalButtons();
}

function showOptionsAdvertisementModal() {
    return new Promise(resolve => {
        const modal = document.getElementById('options-advert-modal');
        const closeBtn = document.getElementById('close-options-advert-modal');
        const addPriorityBtn = document.getElementById('add-priority-from-modal');
        const addPremiumBtn = document.getElementById('add-premium-from-modal');
        const continueBtn = document.getElementById('continue-from-options-modal');

        const prioritySection = document.getElementById('advert-option-priority');
        const premiumSection = document.getElementById('advert-option-premium');
        
        // --- NOUVELLE LOGIQUE D'AFFICHAGE ---
        
        // Masquer les sections par défaut
        prioritySection.classList.add('hidden');
        premiumSection.classList.add('hidden');

        // Gérer l'affichage de l'option Priority
        if (staticOptions.priority && staticOptions.priority.id && staticOptions.priority.prixUnitaire > 0) {
            const priorityPriceEl = document.getElementById('advert-priority-price');
            priorityPriceEl.textContent = `+${staticOptions.priority.prixUnitaire.toFixed(2)} €`;
            prioritySection.classList.remove('hidden');
        }

        // Gérer l'affichage de l'option Premium
        const premiumAvailableContent = document.getElementById('premium-available-content');
        const premiumUnavailableMessage = document.getElementById('premium-unavailable-message');

        if (isPremiumAvailable) { // isPremiumAvailable est calculé dans displayOptions
            if (staticOptions.premium && staticOptions.premium.id && staticOptions.premium.prixUnitaire > 0) {
                // Premium est disponible ET a un prix
                const premiumPriceEl = document.getElementById('advert-premium-price');
                premiumPriceEl.textContent = `+${staticOptions.premium.prixUnitaire.toFixed(2)} €`;

                premiumAvailableContent.classList.remove('hidden');
                premiumUnavailableMessage.classList.add('hidden');

                const premiumDetailsContainer = document.getElementById('premium-details-modal');
                const lieuxOptionsHTML = globalLieuxData.map(lieu => `<option value="${lieu.id}">${lieu.libelle}</option>`).join('');

                const orlyAirportId = '64f00ace-31b6-45b0-bcb2-b562b1ac08d9';
                const isOrly = airportId === orlyAirportId;

                const transportOptions = {
                    orly: `
                        <option value="" selected disabled>Sélectionner...</option>
                        <option value="car">Voiture</option>
                        <option value="taxi">Taxi</option>
                        <option value="vtc">VTC</option>
                        <option value="bus">Bus</option>
                        <option value="metro">Métro</option>
                        <option value="flight">Avion</option>
                    `,
                    cdg: `
                        <option value="" selected disabled>Sélectionner...</option>
                        <option value="tgv">TGV</option>
                        <option value="rer_metro">RER/Métro</option>
                        <option value="car">Voiture</option>
                        <option value="taxi">Taxi</option>
                        <option value="vtc">VTC</option>
                        <option value="bus">Bus</option>
                        <option value="flight">Avion</option>
                    `
                };
                
                const transportSpecificFields = (direction) => {
                    const dir = direction.toLowerCase(); // 'arrival' or 'departure'
                    return `
                        <div id="transport_details_${dir}_flight" class="hidden mt-2">
                            <label class="block text-sm font-medium text-gray-700">Numéro de vol *</label>
                            <input type="text" name="flight_number_${dir}" class="input-style w-full" data-required="true">
                        </div>
                        <div id="transport_details_${dir}_tgv" class="hidden mt-2">
                            <label class="block text-sm font-medium text-gray-700">Numéro du TGV *</label>
                            <input type="text" name="tgv_number_${dir}" class="input-style w-full" data-required="true">
                        </div>
                    `;
                };

                premiumDetailsContainer.innerHTML = `
                <div class="space-y-4">
                    <p class="font-medium text-gray-700">Sens de la prise en charge :</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer flex-1 has-[:checked]:bg-yellow-50 has-[:checked]:border-yellow-custom transition-all">
                            <input type="radio" name="premium_direction" value="terminal_to_agence" class="form-radio h-5 w-5 text-yellow-custom focus:ring-yellow-hover">
                            <span class="ml-3 text-gray-700 font-medium">Récupération de vos bagages</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer flex-1 has-[:checked]:bg-yellow-50 has-[:checked]:border-yellow-custom transition-all">
                            <input type="radio" name="premium_direction" value="agence_to_terminal" class="form-radio h-5 w-5 text-yellow-custom focus:ring-yellow-hover">
                            <span class="ml-3 text-gray-700 font-medium">Restitution de vos bagages</span>
                        </label>
                    </div>
                </div>
                <div id="premium_fields_terminal_to_agence" class="hidden mt-4 space-y-3">
                    <h4 class="font-semibold text-gray-800 border-t pt-3 mt-3">Communiquez-nous les informations utiles à l’organisation de la prise en charge personnalisée de vos bagages.</h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Moyen de transport *</label>
                        <select name="transport_type_arrival" class="input-style custom-select w-full" data-required="true">
                            ${isOrly ? `
                                <option value="" selected disabled>Sélectionner...</option>
                                <option value="car">Voiture</option>
                                <option value="taxi">Taxi</option>
                                <option value="vtc">VTC</option>
                                <option value="bus">Bus</option>
                                <option value="metro">Métro</option>
                                <option value="flight">Avion</option>
                            ` : `
                                <option value="" selected disabled>Sélectionner...</option>
                                <option value="tgv">TGV</option>
                                <option value="rer_metro">RER/Métro</n>
                                <option value="car_taxi_vtc_bus">Voiture/Taxi/VTC/Bus</option>
                                <option value="flight">Avion</n>
                            `}
                        </select>
                    </div>
                    ${transportSpecificFields('arrival')}
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700">Date d’arrivée</label><input type="date" id="flight_date_arrival" name="date_arrival" class="input-style w-full"></div>
                        <div>
                             <label class="block text-sm font-medium text-gray-700">Lieu de prise en charge *</label>
                             <select name="pickup_location_arrival" class="input-style custom-select w-full" data-required="true"><option value="" selected disabled>Select</option>${lieuxOptionsHTML}</select>
                         </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700">Heure de prise en charge*</label><input type="time" id="pickup_time_arrival" name="pickup_time_arrival" class="input-style w-full" data-required="true"></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700">Informations complémentaires</label><textarea name="instructions_arrival" class="input-style w-full" rows="2"></textarea></div>
                </div>
                <div id="premium_fields_agence_to_terminal" class="hidden mt-4 space-y-3">
                    <h4 class="font-semibold text-gray-800 border-t pt-3 mt-3">Communiquez-nous les informations utiles à l’organisation de la restitution personnalisée de vos bagages.</h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Moyen de transport *</label>
                        <select name="transport_type_departure" class="input-style custom-select w-full" data-required="true">
                            ${isOrly ? `
                                <option value="" selected disabled>Sélectionner...</option>
                                <option value="car">Voiture</option>
                                <option value="taxi">Taxi</option>
                                <option value="vtc">VTC</option>
                                <option value="bus">Bus</option>
                                <option value="metro">Métro</option>
                                <option value="flight">Avion</option>
                            ` : `
                                <option value="" selected disabled>Sélectionner...</option>
                                <option value="tgv">TGV</option>
                                <option value="rer_metro">RER/Métro</n>
                                <option value="car_taxi_vtc_bus">Voiture/Taxi/VTC/Bus</option>
                                <option value="flight">Avion</n>
                            `}
                        </select>
                    </div>
                    ${transportSpecificFields('departure')}
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700">Date de départ</label><input type="date" id="flight_date_departure" name="date_departure" class="input-style w-full"></div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lieu de restitution *</label>
                            <select name="restitution_location_departure" class="input-style custom-select w-full" data-required="true"><option value="" selected disabled>Select</option>${lieuxOptionsHTML}</select>
                        </div>
                    </div>
                     <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700">Heure de restitution *</label><input type="time" id="restitution_time_departure" name="restitution_time_departure" class="input-style w-full" data-required="true"></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700">Informations complémentaires</label><textarea name="instructions_departure" class="input-style w-full" rows="2"></textarea></div>
                </div>`;
                
                // --- START DYNAMIC PREMIUM LOGIC ---
                
                // Get elements
                const flightDateArrival = document.getElementById('flight_date_arrival');
                const pickupTimeArrival = document.getElementById('pickup_time_arrival');
                const flightDateDeparture = document.getElementById('flight_date_departure');
                const restitutionTimeDeparture = document.getElementById('restitution_time_departure');

                // Pre-fill dates from main form
                flightDateArrival.value = document.getElementById('date-depot').value;
                pickupTimeArrival.value = document.getElementById('heure-depot').value;
                flightDateDeparture.value = document.getElementById('date-recuperation').value;
                restitutionTimeDeparture.value = document.getElementById('heure-recuperation').value;

                // Transport type change handler
                const setupTransportTypeHandler = (direction) => {
                    const transportSelect = document.querySelector(`select[name="transport_type_${direction}"]`);
                    // Update detailsContainer to include the new types
                    const detailsContainer = {
                        flight: document.getElementById(`transport_details_${direction}_flight`),
                        tgv: document.getElementById(`transport_details_${direction}_tgv`),
                        // Note: car_taxi_vtc, bus, metro, rer_metro, car_taxi_vtc_bus do not have specific fields
                    };

                    transportSelect.addEventListener('change', (e) => {
                        // Hide all containers that might have been shown
                        Object.values(detailsContainer).forEach(container => {
                            if(container) container.classList.add('hidden')
                        });
                        
                        // Show the selected one if it requires details
                        const selectedType = e.target.value;
                        if (detailsContainer[selectedType]) {
                            detailsContainer[selectedType].classList.remove('hidden');
                        }
                    });
                };

                setupTransportTypeHandler('arrival');
                setupTransportTypeHandler('departure');

                // --- END DYNAMIC PREMIUM LOGIC ---

                // Attacher les listeners nécessaires
                const directionRadios = premiumDetailsContainer.querySelectorAll('input[name="premium_direction"]');
                directionRadios.forEach(radio => {
                    radio.addEventListener('change', (e) => {
                        document.getElementById('premium_fields_terminal_to_agence').classList.toggle('hidden', e.target.value !== 'terminal_to_agence');
                        document.getElementById('premium_fields_agence_to_terminal').classList.toggle('hidden', e.target.value !== 'agence_to_terminal');
                    });
                });
            } else {
                // Premium est éligible mais l'API ne renvoie pas de prix/id
                premiumAvailableContent.classList.add('hidden');
                premiumUnavailableMessage.classList.remove('hidden');
            }
            premiumSection.classList.remove('hidden');
        }
        // --- FIN DE LA NOUVELLE LOGIQUE ---
        
        updateAdvertModalButtons(); // Met à jour l'état des boutons (Ajouter/Enlever)

        const closeModalAndResolve = (resolutionValue = 'continued') => {
            modal.classList.add('hidden');
            continueBtn.onclick = null;
            closeBtn.onclick = null;
            modal.onclick = null;
            addPriorityBtn.onclick = null;
            addPremiumBtn.onclick = null;
            resolve(resolutionValue);
        };

        addPriorityBtn.onclick = () => toggleOptionFromModal('priority');
        addPremiumBtn.onclick = () => toggleOptionFromModal('premium');
        continueBtn.onclick = () => closeModalAndResolve('continued');
        closeBtn.onclick = () => closeModalAndResolve('cancelled');
        modal.onclick = (e) => {
            if (e.target === modal) {
                closeModalAndResolve('cancelled');
            }
        };
        
        modal.classList.remove('hidden');
    });
}


// Initialise les écouteurs dès que le DOM est prêt.
document.addEventListener('DOMContentLoaded', setupGlobalModalListeners);

// Exposer les fonctions au scope global pour qu'elles soient accessibles
// par les scripts inline dans les fichiers Blade.
window.showCustomAlert = showCustomAlert;
window.showCustomConfirm = showCustomConfirm;
window.showCustomPrompt = showCustomPrompt;
window.showLoginOrGuestPrompt = showLoginOrGuestPrompt;
window.closeModal = closeModal;
