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
    </style>
</head>
<body class="min-h-screen bg-white">

    @include('Front.header-front')

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-6 py-8">
        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Réserver une consigne</h1>
        <p class="text-gray-600 mb-8">
            Sélectionnez le type de consigne et suivez-nous à pas les étapes du formulaire et nous indiquerons les informations demandées.
        </p>

        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-8">
            <span>Accueil</span>
            <span>→</span>
            <span class="text-gray-800 font-medium">Réserver une consigne</span>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Left Column - Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Location Selection -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <p class="text-sm text-red-500 mb-4">* Tous les points sont obligatoires</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                DANS QUELLE AÉROPORT SOUHAITEZ-VOUS LAISSER VOS BAGAGES ? *
                            </label>
                            <div class="relative">
                                <select class="input-style custom-select w-full">
                                    <option value="" selected disabled>Sélectionner Paris-Charles-de-Gaulle</option>
                                    <option value="cdg">Paris-Charles-de-Gaulle</option>
                                    <option value="orly">Paris-Orly</option>
                                    <option value="beauvais">Paris-Beauvais</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                QUEL EST LE TYPE DE BAGAGE CONSIGNÉ ?
                            </label>
                            <div class="grid grid-cols-2 gap-4 mt-3">
                                <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="suitcase">
                                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-gray-600">
                                            <rect x="6" y="8" width="12" height="10" rx="1" stroke="currentColor" stroke-width="2"/>
                                            <path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/>
                                            <circle cx="10" cy="18" r="1" fill="currentColor"/>
                                            <circle cx="14" cy="18" r="1" fill="currentColor"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium">Valise</span>
                                </div>
                                
                                <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="backpack">
                                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-gray-600">
                                            <path d="M5 12V8a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v4" stroke="currentColor" stroke-width="2"/>
                                            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2"/>
                                            <path d="M10 5V3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium">Sac à dos</span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <label class="block text-sm text-gray-600 mb-2">COMBIEN ?</label>
                                <div class="flex items-center space-x-2">
                                    <button class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-hover">
                                        −
                                    </button>
                                    <input type="text" class="input-style w-16 text-center" value="1" readonly />
                                    <button class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-hover">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Extra Baggage Option -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-start space-x-3">
                        <div class="checkbox-custom" id="extra-baggage-checkbox"></div>
                        <div>
                            <label for="extra-baggage-checkbox" class="text-sm font-medium text-gray-700 cursor-pointer">
                                AJOUTER UN TYPE DE BAGAGE SUPPLÉMENTAIRE
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Date and Time Selection -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            DÉFINIR LA DATE DE DÉPÔT *
                        </label>
                        <div class="space-y-3">
                            <div class="flex space-x-2">
                                <input type="text" placeholder="JJ" class="input-style w-16 text-center" />
                                <span class="flex items-center">/</span>
                                <input type="text" placeholder="MM" class="input-style w-16 text-center" />
                                <span class="flex items-center">/</span>
                                <input type="text" placeholder="AAAA" class="input-style w-20 text-center" />
                            </div>
                            <p class="text-xs text-gray-500">Date minimale: aujourd'hui</p>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            DÉFINIR L'HEURE DE RÉCUPÉRATION *
                        </label>
                        <div class="space-y-3">
                            <div class="flex space-x-2">
                                <input type="text" placeholder="HH" class="input-style w-16 text-center" />
                                <span class="flex items-center">:</span>
                                <input type="text" placeholder="MM" class="input-style w-16 text-center" />
                            </div>
                            <p class="text-xs text-gray-500">Format 24h</p>
                        </div>
                    </div>
                </div>

                <!-- Bottom Banner -->
                <div class="bg-yellow-custom rounded-lg p-6">
                    <h3 class="font-bold text-black mb-2">ATTENTION !</h3>
                    <p class="text-sm text-black leading-relaxed">
                        LES DÉPLACEMENTS EN TRANSPORT EN COMMUN DE CONSIGNE EUROPÉENS, LES 
                        OBJETS QUE VOUS METTE EN CHARGE DE LIVRAISON DES TRANSPORTS EN ÉCHANGES AVEC 
                        NETTOYAGE RER DÉPLACEMENTS CENTRE GARE DU NORD, CHATELET LES HALLES, GARE DE LYON 
                        OU ST MICHEL NOTRE DAME
                    </p>
                </div>

                <!-- CTA Button -->
                <div class="bg-gray-800 rounded-lg p-4 flex items-center justify-between">
                    <p class="text-white text-sm">
                        Vous êtes un professionnel du tourisme ? Facilitez le voyage de vos clients !
                    </p>
                    <button class="bg-transparent border border-white text-white px-4 py-2 rounded-full text-sm hover:bg-white hover:text-gray-800 transition-colors">
                        DEVENIR PARTENAIRE →
                    </button>
                </div>
            </div>

            <!-- Right Column - Summary -->
            <div class="space-y-6">
                <!-- Price Summary -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                    <div class="text-center mb-4">
                        <p class="text-sm text-gray-600 mb-2">Notre tarif :</p>
                        <p class="text-xs text-gray-500 mb-4">Pour 24h (TVA incluse)</p>
                        <div class="text-4xl font-bold text-gray-800">0 €</div>
                    </div>
                </div>

                <!-- Empty Cart -->
                <div class="bg-white border-2 border-yellow-custom rounded-lg p-6 shadow-sm">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" class="text-gray-400">
                                <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L9 11m-2 2v6a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-6" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-black mb-2">Votre panier est vide :(</h3>
                        <div class="bg-gray-100 rounded p-3 mt-4">
                            <p class="text-sm text-gray-600 mb-2">Total:</p>
                            <p class="text-2xl font-bold text-black">0€</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('Front.footer-front')

    <script>
        // Sélection du type de bagage
        document.querySelectorAll('.baggage-option').forEach(option => {
            option.addEventListener('click', function() {
                // Enlever la sélection précédente
                document.querySelectorAll('.baggage-option').forEach(el => {
                    el.classList.remove('selected');
                });
                
                // Ajouter la sélection à l'option cliquée
                this.classList.add('selected');
                
                // Stocker la sélection dans data-type
                const baggageType = this.getAttribute('data-type');
                console.log('Type de bagage sélectionné:', baggageType);
            });
        });
        
        // Case à cocher supplémentaire
        const checkbox = document.getElementById('extra-baggage-checkbox');
        checkbox.addEventListener('click', function() {
            this.classList.toggle('checked');
            console.log('Option supplémentaire:', this.classList.contains('checked'));
        });
        
        // Gestion des boutons d'incrémentation
        document.querySelectorAll('.btn-hover').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentNode.querySelector('input');
                let value = parseInt(input.value) || 0;
                
                if(this.textContent === '+') {
                    value = Math.min(10, value + 1);
                } else {
                    value = Math.max(1, value - 1);
                }
                
                input.value = value;
            });
        });
    </script>

</body>
</html>