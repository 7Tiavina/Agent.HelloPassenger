let modalResolve;
let wasQuickDateModalOpen = false;

function hideQuickDateModalIfOpen() {
    const quickDateModal = document.getElementById('quick-date-modal');
    if (quickDateModal && !quickDateModal.classList.contains('hidden')) {
        wasQuickDateModalOpen = true;
        quickDateModal.classList.add('hidden');
    } else {
        wasQuickDateModalOpen = false;
    }
}

function showCustomAlert(title, message) {
    hideQuickDateModalIfOpen();
    const modalOverlay = document.getElementById('custom-modal-overlay');
    document.getElementById('custom-modal-title').textContent = title;
    document.getElementById('custom-modal-message').innerHTML = message;
    document.getElementById('custom-modal-prompt-container').classList.add('hidden');
    document.getElementById('custom-modal-cancel-btn').classList.add('hidden');
    document.getElementById('custom-modal-confirm-btn').textContent = 'OK';
    modalOverlay.classList.remove('hidden');
    return new Promise(resolve => { modalResolve = resolve; });
}

function showCustomPrompt(title, message, label) {
    hideQuickDateModalIfOpen();
    const modalOverlay = document.getElementById('custom-modal-overlay');
    document.getElementById('custom-modal-title').textContent = title;
    document.getElementById('custom-modal-message').textContent = message;
    document.getElementById('custom-modal-prompt-label').textContent = label;
    document.getElementById('custom-modal-input').value = '';
    document.getElementById('custom-modal-error').classList.add('hidden');
    document.getElementById('custom-modal-prompt-container').classList.remove('hidden');
    document.getElementById('custom-modal-cancel-btn').classList.remove('hidden');
    document.getElementById('custom-modal-confirm-btn').textContent = 'Confirmer';
    modalOverlay.classList.remove('hidden');
    return new Promise(resolve => { modalResolve = resolve; });
}

function showLoginOrGuestPrompt() {
    hideQuickDateModalIfOpen();
    const modalOverlay = document.getElementById('custom-modal-overlay');
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

function displayOptions(dureeEnMinutes) {
    // Condition pour Priority (ne change pas)
    isPriorityAvailable = true;

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
            console.log('DEBUG (modal.js): Option object from staticOptions:', option); // NEW DEBUG LOG
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

            if (formContainer) {
                let isFormValid = true;
                formContainer.querySelectorAll('input, textarea, select').forEach(input => {
                    if (!input.value.trim()) {
                        isFormValid = false;
                    }
                    premiumDetails[input.name] = input.value;
                });
                
                if(!isFormValid) {
                    showCustomAlert('Champs requis', 'Veuillez remplir tous les champs pour le service premium.');
                    return; // Stop if form is not valid
                }
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
        const prioritySection = document.getElementById('advert-option-priority');
        const premiumSection = document.getElementById('advert-option-premium');
        const premiumAvailableContent = document.getElementById('premium-available-content');
        const premiumUnavailableMessage = document.getElementById('premium-unavailable-message');
        const addPriorityBtn = document.getElementById('add-priority-from-modal');
        const addPremiumBtn = document.getElementById('add-premium-from-modal');
        const continueBtn = document.getElementById('continue-from-options-modal');

        const priorityPriceEl = document.getElementById('advert-priority-price');
        const premiumPriceEl = document.getElementById('advert-premium-price');

        if (priorityPriceEl && staticOptions.priority.prixUnitaire > 0) {
            priorityPriceEl.textContent = `+${staticOptions.priority.prixUnitaire.toFixed(2)} €`;
        }

        if (premiumPriceEl && staticOptions.premium.prixUnitaire > 0) {
            premiumPriceEl.textContent = `+${staticOptions.premium.prixUnitaire.toFixed(2)} €`;
        }
        

        // Reset visibility
        prioritySection.classList.add('hidden');
        premiumSection.classList.add('hidden');
        
        if (isPriorityAvailable) {
            prioritySection.classList.remove('hidden');
        }

        if (isPremiumAvailable) {
            premiumSection.classList.remove('hidden');
            premiumAvailableContent.classList.remove('hidden');
            premiumUnavailableMessage.classList.add('hidden');

            const premiumDetailsContainer = document.getElementById('premium-details-modal');
            const lieuxOptionsHTML = globalLieuxData.map(lieu => `<option value="${lieu.id}">${lieu.libelle}</option>`).join('');

            premiumDetailsContainer.innerHTML = `
                <div class="space-y-4">
                    <p class="font-medium text-gray-700">Sens de la prise en charge :</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer flex-1 has-[:checked]:bg-yellow-50 has-[:checked]:border-yellow-custom transition-all">
                            <input type="radio" name="premium_direction" value="terminal_to_agence" class="form-radio h-5 w-5 text-yellow-custom focus:ring-yellow-hover">
                            <span class="ml-3 text-gray-700 font-medium">Terminal → Agence BDM</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer flex-1 has-[:checked]:bg-yellow-50 has-[:checked]:border-yellow-custom transition-all">
                            <input type="radio" name="premium_direction" value="agence_to_terminal" class="form-radio h-5 w-5 text-yellow-custom focus:ring-yellow-hover">
                            <span class="ml-3 text-gray-700 font-medium">Agence BDM → Terminal</span>
                        </label>
                    </div>
                </div>

                <!-- Formulaire pour Terminal -> Agence -->
                <div id="premium_fields_terminal_to_agence" class="hidden mt-4 space-y-3">
                    <h4 class="font-semibold text-gray-800 border-t pt-3 mt-3">Détails pour : Terminal → Agence BDM</h4>
                    <div><label class="block text-sm font-medium text-gray-700">Numéro de vol</label><input type="text" name="flight_number_arrival" class="input-style w-full"></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700">Date d’arrivée</label><input type="date" name="date_arrival" class="input-style w-full"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Heure d’arrivée</label><input type="time" name="time_arrival" class="input-style w-full"></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700">Terminal d’arrivée</label><input type="text" name="terminal_arrival" class="input-style w-full"></div>
                    <div><label class="block text-sm font-medium text-gray-700">Nombre de bagages</label><input type="number" name="baggage_count_arrival" class="input-style w-full" min="1"></div>
                    <div class="grid grid-cols-2 gap-3">
                         <div>
                             <label class="block text-sm font-medium text-gray-700">Lieu de prise en charge</label>
                             <select name="pickup_location_arrival" class="input-style custom-select w-full">
                                <option value="" selected disabled>Select</option>
                                ${lieuxOptionsHTML}
                             </select>
                         </div>
                        <div><label class="block text-sm font-medium text-gray-700">Heure de prise en charge</label><input type="time" name="pickup_time_arrival" class="input-style w-full"></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700">Instructions</label><textarea name="instructions_arrival" class="input-style w-full" rows="2"></textarea></div>
                </div>

                <!-- Formulaire pour Agence -> Terminal -->
                <div id="premium_fields_agence_to_terminal" class="hidden mt-4 space-y-3">
                    <h4 class="font-semibold text-gray-800 border-t pt-3 mt-3">Détails pour : Agence BDM → Terminal</h4>
                    <div><label class="block text-sm font-medium text-gray-700">Numéro de vol</label><input type="text" name="flight_number_departure" class="input-style w-full"></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700">Date de départ</label><input type="date" name="date_departure" class="input-style w-full"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Heure de départ</label><input type="time" name="time_departure" class="input-style w-full"></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700">Terminal de départ</label><input type="text" name="terminal_departure" class="input-style w-full"></div>
                    <div><label class="block text-sm font-medium text-gray-700">Nombre de bagages</label><input type="number" name="baggage_count_departure" class="input-style w-full" min="1"></div>
                     <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lieu de restitution</label>
                            <select name="restitution_location_departure" class="input-style custom-select w-full">
                                <option value="" selected disabled>Sélectionnez un lieu</option>
                                ${lieuxOptionsHTML}
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700">Heure de restitution</label><input type="time" name="restitution_time_departure" class="input-style w-full"></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700">Instructions</label><textarea name="instructions_departure" class="input-style w-full" rows="2"></textarea></div>
                </div>
            `;

            // Add listeners for radio buttons
            const directionRadios = premiumDetailsContainer.querySelectorAll('input[name="premium_direction"]');
            const formTerminalToAgence = document.getElementById('premium_fields_terminal_to_agence');
            const formAgenceToTerminal = document.getElementById('premium_fields_agence_to_terminal');
            
            directionRadios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    if (e.target.value === 'terminal_to_agence') {
                        formTerminalToAgence.classList.remove('hidden');
                        formAgenceToTerminal.classList.add('hidden');
                    } else {
                        formTerminalToAgence.classList.add('hidden');
                        formAgenceToTerminal.classList.remove('hidden');
                    }
                });
            });

        } else {
            premiumSection.classList.remove('hidden'); // Ensure the premium section container is visible
            premiumAvailableContent.classList.add('hidden');
            premiumUnavailableMessage.classList.remove('hidden');
        }

        updateAdvertModalButtons(); // Set initial button states

        const closeModalAndResolve = (resolutionValue = 'continued') => {
            modal.classList.add('hidden');
            // Clean up event listeners
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

function closeModal() {
    document.getElementById('custom-modal-overlay').classList.add('hidden');
    const passwordField = document.getElementById('custom-modal-password');
    if (passwordField) {
        passwordField.previousElementSibling?.remove();
        passwordField.remove();
    }
    const footer = document.getElementById('custom-modal-footer');
    footer.innerHTML = `
        <button id="custom-modal-cancel-btn" class="hidden bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-full btn-hover">Annuler</button>
        <button id="custom-modal-confirm-btn" class="bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover">OK</button>
    `;
    document.getElementById('custom-modal-cancel-btn').onclick = () => { closeModal(); if (modalResolve) modalResolve(null); };
    document.getElementById('custom-modal-confirm-btn').onclick = () => {
        const promptContainer = document.getElementById('custom-modal-prompt-container');
        const isPrompt = !promptContainer.classList.contains('hidden');
        if (isPrompt) {
            const value = document.getElementById('custom-modal-input').value;
            if (value.trim() === '' || !/^\S+@\S+\.\S+$/.test(value)) {
                document.getElementById('custom-modal-error').textContent = 'Veuillez entrer une adresse e-mail valide.';
                document.getElementById('custom-modal-error').classList.remove('hidden');
                return;
            }
            closeModal(); if (modalResolve) modalResolve(value);
        } else { closeModal(); if (modalResolve) modalResolve(true); }
    };
    if (wasQuickDateModalOpen) {
        document.getElementById('quick-date-modal').classList.remove('hidden');
        wasQuickDateModalOpen = false;
    }
}

document.getElementById('custom-modal-close').onclick = () => { closeModal(); if (modalResolve) modalResolve(null); };
document.getElementById('custom-modal-overlay').onclick = (e) => { if (e.target === e.currentTarget) { closeModal(); if (modalResolve) modalResolve(null); } };