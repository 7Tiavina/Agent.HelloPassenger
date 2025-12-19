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
                
                // Check required fields
                formContainer.querySelectorAll('[data-required="true"]').forEach(input => {
                    if (!input.value.trim()) {
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
                            <span class="ml-3 text-gray-700 font-medium">Récupération de vos bagages</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer flex-1 has-[:checked]:bg-yellow-50 has-[:checked]:border-yellow-custom transition-all">
                            <input type="radio" name="premium_direction" value="agence_to_terminal" class="form-radio h-5 w-5 text-yellow-custom focus:ring-yellow-hover">
                            <span class="ml-3 text-gray-700 font-medium">Restitution de vos bagages</span>
                        </label>
                    </div>
                </div>

                <!-- Formulaire pour Terminal -> Agence -->
                <div id="premium_fields_terminal_to_agence" class="hidden mt-4 space-y-3">
                    <h4 class="font-semibold text-gray-800 border-t pt-3 mt-3">Communiquez-nous les informations utiles à l’organisation de la prise en charge personnalisée de vos bagages.</h4>
                    <div><label class="block text-sm font-medium text-gray-700">Numéro de vol *</label><input type="text" name="flight_number_arrival" class="input-style w-full" data-required="true"></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700">Date d’arrivée</label><input type="date" name="date_arrival" class="premium-disabled-date input-style w-full bg-gray-200 cursor-not-allowed" value="${document.getElementById('date-depot').value}" readonly disabled></div>
                        <div><label class="block text-sm font-medium text-gray-700">Heure d’arrivée</label><input type="time" name="time_arrival" class="premium-disabled-date input-style w-full bg-gray-200 cursor-not-allowed" value="${document.getElementById('heure-depot').value}" readonly disabled></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                         <div>
                             <label class="block text-sm font-medium text-gray-700">Lieu de prise en charge *</label>
                             <select name="pickup_location_arrival" class="input-style custom-select w-full" data-required="true">
                                <option value="" selected disabled>Select</option>
                                ${lieuxOptionsHTML}
                             </select>
                         </div>
                        <div><label class="block text-sm font-medium text-gray-700">Heure de prise en charge*</label><input type="time" name="pickup_time_arrival" class="input-style w-full" data-required="true" min="${(() => { const dt = new Date(`${document.getElementById('date-depot').value}T${document.getElementById('heure-depot').value}`); dt.setMinutes(dt.getMinutes() + 45); return dt.toTimeString().substring(0,5); })()}" value="${(() => { const dt = new Date(`${document.getElementById('date-depot').value}T${document.getElementById('heure-depot').value}`); dt.setMinutes(dt.getMinutes() + 45); return dt.toTimeString().substring(0,5); })()}">
</div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700">Informations complémentaires</label><textarea name="instructions_arrival" class="input-style w-full" rows="2"></textarea></div>
                    <div class="mt-4 text-sm text-gray-500 bg-gray-50 p-3 rounded-lg">
                        <p><strong>Info :</strong> La récupération de vos bagages se fait en moyenne <strong>45 minutes</strong> après l’heure d’arrivée de votre vol.</p>
                    </div>
                </div>

                <!-- Formulaire pour Agence -> Terminal -->
                <div id="premium_fields_agence_to_terminal" class="hidden mt-4 space-y-3">
                    <h4 class="font-semibold text-gray-800 border-t pt-3 mt-3">Communiquez-nous les informations utiles à l’organisation de la restitution personnalisée de vos bagages.</h4>
                    <div><label class="block text-sm font-medium text-gray-700">Numéro de vol *</label><input type="text" name="flight_number_departure" class="input-style w-full" data-required="true"></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700">Date de départ</label><input type="date" name="date_departure" class="premium-disabled-date input-style w-full bg-gray-200 cursor-not-allowed" value="${document.getElementById('date-recuperation').value}" readonly disabled></div>
                        <div><label class="block text-sm font-medium text-gray-700">Heure de départ</label><input type="time" name="time_departure" class="premium-disabled-date input-style w-full bg-gray-200 cursor-not-allowed" value="${document.getElementById('heure-recuperation').value}" readonly disabled></div>
                    </div>
                     <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lieu de restitution *</label>
                            <select name="restitution_location_departure" class="input-style custom-select w-full" data-required="true">
                                <option value="" selected disabled>Select</option>
                                ${lieuxOptionsHTML}
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700">Heure de restitution *</label><input type="time" name="restitution_time_departure" class="input-style w-full" data-required="true" max="${(() => { const dt = new Date(`${document.getElementById('date-recuperation').value}T${document.getElementById('heure-recuperation').value}`); dt.setHours(dt.getHours() - 2); return dt.toTimeString().substring(0,5); })()}" value="${(() => { const dt = new Date(`${document.getElementById('date-recuperation').value}T${document.getElementById('heure-recuperation').value}`); dt.setHours(dt.getHours() - 2); return dt.toTimeString().substring(0,5); })()}">
</div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700">Informations complémentaires</label><textarea name="instructions_departure" class="input-style w-full" rows="2"></textarea></div>
                    <div class="mt-4 text-sm text-gray-500 bg-gray-50 p-3 rounded-lg">
                        <p><strong>Info :</strong> La restitution de vos bagages se fait au plus tard <strong>2 heures</strong> avant le départ de votre vol.</p>
                    </div>
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

            // Add event listeners for time constraint enforcement
            const pickupTimeInput = document.querySelector('[name="pickup_time_arrival"]');
            if (pickupTimeInput) {
                pickupTimeInput.addEventListener('change', (e) => {
                    const minTime = e.target.min;
                    if (e.target.value < minTime) {
                        e.target.value = minTime;
                    }
                });
            }

            const restitutionTimeInput = document.querySelector('[name="restitution_time_departure"]');
            if (restitutionTimeInput) {
                restitutionTimeInput.addEventListener('change', (e) => {
                    const maxTime = e.target.max;
                    // Only check maxTime if it's set, as it might be empty for future dates
                    if (maxTime && e.target.value > maxTime) {
                        e.target.value = maxTime;
                    }
                });
            }

            // Add tooltip listeners for disabled date/time fields
            const tooltip = document.getElementById('baggage-tooltip');
            const disabledInputs = premiumDetailsContainer.querySelectorAll('.premium-disabled-date');

            disabledInputs.forEach(input => {
                input.addEventListener('mouseover', (e) => {
                    if (!tooltip) return;
                    tooltip.textContent = 'Ces dates sont à modifier à l’étape précédente.';
                    tooltip.classList.add('hidden');
                    const rect = e.target.getBoundingClientRect();
                    const tooltipRect = tooltip.getBoundingClientRect();
                    tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltipRect.width / 2) + window.scrollX}px`;
                    tooltip.style.top = `${rect.top + window.scrollY - tooltip.offsetHeight - 5}px`;
                });
                input.addEventListener('mouseout', () => {
                    if (tooltip) tooltip.classList.add('hidden');
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


// Initialise les écouteurs dès que le DOM est prêt.
document.addEventListener('DOMContentLoaded', setupGlobalModalListeners);
