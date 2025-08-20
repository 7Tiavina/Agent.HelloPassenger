<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon-hellopassenger.png') }}">
    <title>Réserver une consigne - HelloPassenger</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'yellow-custom': '#FFC107',
                        'yellow-hover': '#FFB300',
                        'gray-dark': '#1f2937'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #ffffff;
        }

        .baggage-option {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }

        .baggage-option:hover {
            border-color: #d1d5db;
        }

        .baggage-option.selected {
            border-color: #FFC107;
            background-color: #fef9e7;
        }

        .btn-hover {
            transition: all 0.3s ease;
        }

        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .input-style {
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .input-style:focus {
            border-color: #FFC107;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
        }

        .checkbox-custom {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #9ca3af;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .checkbox-custom.checked {
            background-color: #FFC107;
            border-color: #FFC107;
        }

        .checkbox-custom.checked::after {
            content: "✓";
            color: white;
            font-size: 0.875rem;
        }

        .custom-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }
        
        .add-baggage-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #FFC107;
            font-weight: 500;
            cursor: pointer;
        }
        
        .add-baggage-btn:hover {
            color: #FFB300;
        }
        
        .remove-baggage-btn {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .remove-baggage-btn:hover {
            background: #e9ecef;
        }

        /* Styles pour le panier dynamique */
        #cart-summary {
            display: none;
        }
    </style>
</head>
<body class="min-h-screen bg-white">

@include('Front.header-front')

<div class="max-w-6xl mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Réserver une consigne</h1>
    <p class="text-gray-600 mb-8">
        Sélectionnez le type de consigne et suivez les étapes du formulaire. Nous vous indiquerons les informations à fournir.
    </p>

    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-8">
        <span>Accueil</span>
        <span>→</span>
        <span class="text-gray-800 font-medium">Réserver une consigne</span>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <p class="text-sm text-red-500 mb-4">* Tous les champs sont obligatoires</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            DANS QUEL AÉROPORT SOUHAITEZ-VOUS LAISSER VOS BAGAGES ? *
                        </label>
                        <select id="airport-select" class="input-style custom-select w-full">
                            <option value="" selected disabled>Sélectionner un aéroport</option>
                            <option value="cdg">Paris-Charles-de-Gaulle</option>
                            <option value="orly">Paris-Orly</option>
                            <option value="beauvais">Paris-Beauvais</option>
                        </select>
                    </div>

                    <div class="baggage-block">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-medium text-gray-700">
                                QUEL EST LE TYPE DE BAGAGE CONSIGNÉ ? *
                            </label>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-3">
                            <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="cabin">
                                <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600">
                                        <rect x="6" y="8" width="12" height="10" rx="1" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/>
                                        <circle cx="10" cy="18" r="1" fill="currentColor"/>
                                        <circle cx="14" cy="18" r="1" fill="currentColor"/>
                                        <path d="M10 10v4M14 10v4" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Bagage en cabine</span>
                            </div>

                            <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="hold">
                                <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600">
                                        <rect x="5" y="6" width="14" height="12" rx="1" stroke="currentColor" stroke-width="2"/>
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M5 10h14" stroke="currentColor" stroke-width="1.5"/>
                                        <circle cx="9" cy="15" r="1" fill="currentColor"/>
                                        <circle cx="15" cy="15" r="1" fill="currentColor"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Bagage en soute</span>
                            </div>

                            <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="cloakroom">
                                <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600">
                                        <path d="M16 10V8a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 10h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 10v-2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M12 14v2" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Vestiaire</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="block text-sm text-gray-600 mb-2">COMBIEN ? *</label>
                            <div class="flex items-center space-x-2">
                                <button type="button" class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-hover btn-minus">−</button>
                                <input type="text" class="input-style w-16 text-center" value="1" readonly />
                                <button type="button" class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-hover btn-plus">+</button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="additional-baggages-container" class="space-y-6 mt-6"></div>
                    
                    <div class="mt-4">
                        <div class="add-baggage-btn" id="add-baggage-type">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <span>AJOUTER UN TYPE DE BAGAGE SUPPLÉMENTAIRE</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">DATE DE DÉPÔT DES BAGAGES *</h3>
                    <input type="date" id="date-depot" class="input-style w-full mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">HEURE DE DÉPÔT *</label>
                    <input type="time" id="heure-depot" class="input-style w-full">
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">DATE DE RÉCUPÉRATION DES BAGAGES *</h3>
                    <input type="date" id="date-recuperation" class="input-style w-full mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">HEURE DE RÉCUPÉRATION *</label>
                    <input type="time" id="heure-recuperation" class="input-style w-full">
                </div>
            </div>

            <div class="mt-8 text-center">
                <button id="get-quote-btn" class="bg-yellow-custom text-gray-dark font-bold py-3 px-8 rounded-full btn-hover">
                    INTERROGER LES TARIFS
                </button>
            </div>

            <div class="bg-yellow-custom rounded-lg p-6">
                <h3 class="font-bold text-black mb-2">ATTENTION !</h3>
                <p class="text-sm text-black leading-relaxed">
                    Les trajets pour la livraison ou la récupération des bagages peuvent inclure les gares : Gare du Nord, Châtelet Les Halles, Gare de Lyon, ou Saint-Michel Notre-Dame.
                </p>
            </div>

            <div class="bg-gray-800 rounded-lg p-4 flex items-center justify-between">
                <p class="text-white text-sm">
                    Vous êtes un professionnel du tourisme ? Facilitez le voyage de vos clients !
                </p>
                <button class="bg-transparent border border-white text-white px-4 py-2 rounded-full text-sm hover:bg-white hover:text-gray-800 transition-colors">
                    DEVENIR PARTENAIRE →
                </button>
            </div>
        </div>

        <div class="w-full lg:w-full relative" id="sticky-wrapper">
            <div id="sticky-summary" class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm text-center">
                    <p class="text-sm text-gray-600 mb-2">Notre tarif :</p>
                    <p class="text-xs text-gray-500 mb-4">Pour la durée sélectionnée (TVA incluse)</p>
                    <div id="summary-price" class="text-4xl font-bold text-gray-800">0 €</div>
                </div>
                <div id="empty-cart" class="bg-white border-2 border-yellow-400 rounded-lg p-6 shadow-sm text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" class="text-gray-400">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L9 11m-2 2v6a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-6" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-black mb-2">Votre panier est vide :(</h3>
                    <div class="bg-gray-100 rounded p-3 mt-4">
                        <p class="text-sm text-gray-600 mb-2">Total:</p>
                        <p class="text-2xl font-bold text-black total-panier">0€</p>
                    </div>
                </div>
                <div id="cart-summary" class="bg-white border-2 border-yellow-400 rounded-lg p-6 shadow-sm">
                    <h3 class="font-bold text-lg text-black mb-4">Votre panier</h3>
                    <div class="panier-content">
                        </div>
                    <div class="bg-gray-100 rounded p-3 mt-4 flex justify-between items-center summary-total-container">
                        <p class="text-lg font-bold text-black">Total:</p>
                        <p class="text-2xl font-bold text-black total-panier">0€</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Front.footer-front')

<footer id="footer"></footer>

<script>
    // Variables globales
    let airportId;
    const serviceId = 'dfb8ac1b-8bb1-4957-afb4-1faedaf641b7';
    const airportIds = {
        'cdg': '88bb89e0-b966-4420-9ed3-7a6745e4d947',
        'orly': '64f00ace-31b6-45b0-bcb2-b562b1ac08d9'
    };
    
    // Événement pour la sélection de l'aéroport
    document.getElementById('airport-select').addEventListener('change', function() {
        airportId = this.value;
    });

    // Événement pour la sélection du type de bagage
    document.addEventListener('click', function(e) {
        if (e.target.closest('.baggage-option')) {
            const block = e.target.closest('.baggage-block');
            block.querySelectorAll('.baggage-option').forEach(el => el.classList.remove('selected'));
            e.target.closest('.baggage-option').classList.add('selected');
        }
    });

    // Incrémentation / décrémentation et suppression
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-plus')) {
            const input = e.target.closest('.flex').querySelector('input');
            let value = parseInt(input.value) || 1;
            input.value = Math.min(10, value + 1);
        }
        if (e.target.classList.contains('btn-minus')) {
            const input = e.target.closest('.flex').querySelector('input');
            let value = parseInt(input.value) || 1;
            input.value = Math.max(1, value - 1);
        }
        if (e.target.closest('.remove-baggage-btn')) {
            e.target.closest('.baggage-block').remove();
        }
    });

    // Ajout d'un nouveau type de bagage
    document.getElementById('add-baggage-type').addEventListener('click', function() {
        const container = document.getElementById('additional-baggages-container');
        const blocks = document.querySelectorAll('.baggage-block');
        
        if (blocks.length >= 5) return;
        
        const newBlock = document.createElement('div');
        newBlock.className = 'baggage-block relative border-t border-gray-200 pt-6 mt-6';
        newBlock.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-medium text-gray-700">
                    TYPE DE BAGAGE SUPPLÉMENTAIRE
                </label>
                <button type="button" class="remove-baggage-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-3">
                <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="cabin">
                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600">
                            <rect x="6" y="8" width="12" height="10" rx="1" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/>
                            <circle cx="10" cy="18" r="1" fill="currentColor"/>
                            <circle cx="14" cy="18" r="1" fill="currentColor"/>
                            <path d="M10 10v4M14 10v4" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Bagage en cabine</span>
                </div>

                <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="hold">
                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600">
                            <rect x="5" y="6" width="14" height="12" rx="1" stroke="currentColor" stroke-width="2"/>
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/>
                            <path d="M5 10h14" stroke="currentColor" stroke-width="1.5"/>
                            <circle cx="9" cy="15" r="1" fill="currentColor"/>
                            <circle cx="15" cy="15" r="1" fill="currentColor"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Bagage en soute</span>
                </div>

                <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="cloakroom">
                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600">
                            <path d="M16 10V8a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 10h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 10v-2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 14v2" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Vestiaire</span>
                </div>
            </div>

            <div class="mt-3">
                <label class="block text-sm text-gray-600 mb-2">COMBIEN ? *</label>
                <div class="flex items-center space-x-2">
                    <button type="button" class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-hover btn-minus">−</button>
                    <input type="text" class="input-style w-16 text-center" value="1" readonly />
                    <button type="button" class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-hover btn-plus">+</button>
                </div>
            </div>
        `;
        container.appendChild(newBlock);
    });

    // Fonction principale pour interroger les tarifs
    document.getElementById('get-quote-btn').addEventListener('click', async function() {
        const dateDepot = document.getElementById('date-depot').value;
        const heureDepot = document.getElementById('heure-depot').value;
        const dateRecuperation = document.getElementById('date-recuperation').value;
        const heureRecuperation = document.getElementById('heure-recuperation').value;

        // Validation des champs
        if (!airportId || !dateDepot || !heureDepot || !dateRecuperation || !heureRecuperation) {
            alert('Veuillez remplir tous les champs obligatoires.');
            return;
        }

        // Formatage de la date pour la vérification de disponibilité
        const dateToVerify = `${new Date(dateDepot).toLocaleDateString('fr-FR').replace(/\//g, '-')}`;
        
        // 1. Appel à la route proxy pour vérifier la disponibilité
        try {
            const availabilityResponse = await fetch('/api/check-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    idPlateforme: airportIds[airportId],
                    dateToCheck: dateToVerify
                })
            });

            const availabilityResult = await availabilityResponse.json();

            if (!availabilityResult.success || !availabilityResult.isAvailable) {
                alert('La plateforme est fermée à la date de dépôt sélectionnée.');
                return;
            }

            // 2. Si la plateforme est disponible, on calcule la durée et on appelle l'API pour les tarifs
            const debut = new Date(`${dateDepot}T${heureDepot}:00`);
            const fin = new Date(`${dateRecuperation}T${heureRecuperation}:00`);
            const dureeEnSecondes = Math.abs(fin - debut) / 1000;

            if (dureeEnSecondes <= 0) {
                alert('La date de récupération doit être postérieure à la date de dépôt.');
                return;
            }

            const productsResponse = await fetch('/api/get-quote', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    idPlateforme: airportIds[airportId],
                    idService: serviceId,
                    duree: dureeEnSecondes
                })
            });

            const productsResult = await productsResponse.json();

            if (productsResult.success) {
                // Stockage des produits et des prix
                const products = productsResult.data.reduce((acc, curr) => {
                    acc[curr.libelle] = { id: curr.id, prix: curr.prixUnitaire };
                    return acc;
                }, {});

                // Mise à jour du panier
                updateCart(products);
            } else {
                alert('Erreur lors de la récupération des tarifs : ' + productsResult.message);
            }

        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la vérification de la disponibilité ou de la récupération des tarifs.');
        }
    });

    // Fonction de mise à jour du panier
    function updateCart(products) {
        const selectedBaggages = document.querySelectorAll('.baggage-block');
        let total = 0;
        let cartContent = '';

        selectedBaggages.forEach(block => {
            const selectedOption = block.querySelector('.baggage-option.selected');
            const quantity = parseInt(block.querySelector('input').value) || 0;
            if (selectedOption) {
                const type = selectedOption.dataset.type;
                const baggageLabel = selectedOption.querySelector('span').textContent;
                let price = 0;
                if (type === 'cabin' && products['Bagage cabine']) {
                    price = products['Bagage cabine'].prix;
                } else if (type === 'hold' && products['Bagage soute']) {
                    price = products['Bagage soute'].prix;
                } else if (type === 'cloakroom' && products['Vestiaire']) {
                    price = products['Vestiaire'].prix;
                }
                
                total += price * quantity;
                
                cartContent += `
                    <div class="flex justify-between items-center mb-2">
                        <span>${quantity} x ${baggageLabel}</span>
                        <span>${(price * quantity).toFixed(2)} €</span>
                    </div>
                `;
            }
        });

        const cartElement = document.getElementById('cart-summary');
        const emptyCartElement = document.getElementById('empty-cart');
        const summaryPriceElement = document.getElementById('summary-price');
        
        if (total > 0) {
            cartElement.style.display = 'block';
            emptyCartElement.style.display = 'none';
            document.querySelector('#cart-summary .panier-content').innerHTML = cartContent;
            document.querySelector('#cart-summary .total-panier').textContent = `${total.toFixed(2)}€`;
            summaryPriceElement.textContent = `${total.toFixed(2)} €`;

            const totalContainer = document.querySelector('.summary-total-container');
            totalContainer.classList.add('cursor-pointer');
            totalContainer.onclick = () => {
                alert('Panier total cliqué ! Logique de paiement à implémenter.');
            };

        } else {
            cartElement.style.display = 'none';
            emptyCartElement.style.display = 'block';
            summaryPriceElement.textContent = `0 €`;
        }
    }
</script>

</body>
</html>