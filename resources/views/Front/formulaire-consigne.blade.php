<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .custom-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }
        
    /* Styles pour le spinner */
    .custom-spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-left-color: #FFC107; /* Couleur du spinner, jaune comme le bouton */
        border-radius: 50%;
        width: 1.5em;
        height: 1.5em;
        animation: spin 1s linear infinite;
        display: inline-block; /* Pour qu\'il soit visible */
        vertical-align: middle; /* Alignement vertical */
        margin-left: 0.5em; /* Espacement avec le texte */
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .input-completed {
        border-color: #FFC107 !important; /* Bordure jaune */
        box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2) !important; /* Ombre jaune */
        background-color: #fef9e7 !important; /* Fond légèrement jaune, comme les bagages sélectionnés */
    }

    .delete-item-btn {
        background: none;
        border: none;
        color: #ef4444; /* red-500 */
        cursor: pointer;
    }
    .delete-item-btn:hover {
        color: #dc2626; /* red-600 */
    }

    #baggage-tooltip {
        transition: opacity 0.3s;
        pointer-events: none; /* Allows mouse events to pass through to elements below */
        max-width: 200px; /* Set a max-width for better readability */
        text-align: center;
    }
    .option-header .chevron-icon {
        transition: transform 0.3s ease;
    }
    .option-header.open .chevron-icon {
        transform: rotate(180deg);
    }
    @keyframes pulse-bg {
        0% { background-color: #fef9e7; } /* slightly yellow */
        50% { background-color: #fef2d2; } /* more yellow */
        100% { background-color: #fef9e7; }
    }
    .cart-updated {
        animation: pulse-bg 0.5s ease-in-out;
    }
    </style>
</head>
<body class="min-h-screen bg-white">

<div id="baggage-tooltip" class="hidden absolute z-10 p-2 text-sm font-medium text-white bg-gray-800 rounded-lg shadow-sm" role="tooltip">
    <!-- Tooltip content will be injected here -->
</div>

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
            <!-- Étape 1: Aéroport et Dates -->
            <div id="step-1">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <p class="text-sm text-red-500 mb-4">* Tous les champs sont obligatoires</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                DANS QUEL AÉROPORT SOUHAITEZ-VOUS LAISSER VOS BAGAGES ? *
                            </label>
                            <select id="airport-select" class="input-style custom-select w-full">
                                <option value="" selected disabled>Sélectionner un aéroport</option>
                                @if(isset($plateformes) && count($plateformes) > 0)
                                    @foreach($plateformes as $plateforme)
                                        <option value="{{ $plateforme['id'] }}">{{ $plateforme['libelle'] }}</option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Aucun aéroport disponible pour le moment</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mt-6">
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
                    <button id="check-availability-btn" class="bg-yellow-custom text-gray-dark font-bold py-3 px-8 rounded-full btn-hover">
                        VOIR LA DISPONIBILITÉ
                        <span class="custom-spinner" role="status" aria-hidden="true" id="loading-spinner-availability" style="display: none;"></span>
                    </button>
                </div>
            </div>

            <div id="baggage-selection-step" class="hidden">
                <!-- Section for selecting baggage -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            1. Choisissez un type de bagage
                        </label>
                        <button id="back-to-step-1-btn" class="text-sm text-gray-600 hover:text-gray-900 font-medium flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Retour
                        </button>
                    </div>
                    <div id="baggage-types-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mt-3">
                        @if(isset($products) && count($products) > 0)
                            @php
                                $product_map = [
                                    'Accessoires' => ['type' => 'accessory', 'icon' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path d="M12 14a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="2"/><path d="M17.94 6.06a8 8 0 00-11.88 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'],
                                    'Bagage cabine' => ['type' => 'cabin', 'icon' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="6" y="8" width="12" height="10" rx="1" stroke="currentColor" stroke-width="2"/><path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/><circle cx="10" cy="18" r="1" fill="currentColor"/><circle cx="14" cy="18" r="1" fill="currentColor"/><path d="M10 10v4M14 10v4" stroke="currentColor" stroke-width="1.5"/></svg>'],
                                    'Bagage soute' => ['type' => 'hold', 'icon' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="5" y="6" width="14" height="12" rx="1" stroke="currentColor" stroke-width="2"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/><path d="M5 10h14" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="15" r="1" fill="currentColor"/><circle cx="15" cy="15" r="1" fill="currentColor"/></svg>'],
                                    'Bagage spécial' => ['type' => 'special', 'icon' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="4" y="7" width="16" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M8 17h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'],
                                    'Vestiaire' => ['type' => 'cloakroom', 'icon' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path d="M16 10V8a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2" stroke="currentColor" stroke-width="2"/><path d="M8 10h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="2"/><path d="M8 10v-2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="1.5"/></svg>']
                                ];
                                $default_icon = '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path stroke="currentColor" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /><path stroke="currentColor" stroke-width="2" d="M9.5 9.5h.01v.01h-.01V9.5zm5 0h.01v.01h-.01V9.5zm-2.5 5a2.5 2.5 0 00-5 0h5z" /></svg>';
                            @endphp
                            @foreach($products as $product)
                                @php
                                    $libelle = $product['libelle'];
                                    $map_data = $product_map[$libelle] ?? ['type' => Illuminate\Support\Str::slug($libelle), 'icon' => $default_icon];
                                @endphp
                                <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="{{ $map_data['type'] }}" data-product-id="{{ $product['id'] }}" data-libelle="{{ $product['libelle'] }}">
                                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                        {!! $map_data['icon'] !!}
                                    </div>
                                    <span class="text-sm font-medium text-center">{{ $libelle }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="col-span-full text-center text-gray-500">Aucun type de bagage disponible pour le moment.</p>
                        @endif
                    </div>

                    <div class="mt-6 flex items-center gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">2. Choisissez une quantité</label>
                            <div class="flex items-center space-x-2">
                                <button type="button" id="quantity-minus" class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-hover">−</button>
                                <input type="text" id="quantity-input" class="input-style w-16 text-center" value="1" readonly />
                                <button type="button" id="quantity-plus" class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-hover">+</button>
                            </div>
                        </div>
                        <div class="self-end">
                            <button id="add-to-cart-btn" class="bg-yellow-custom text-gray-dark font-bold py-3 px-6 rounded-full btn-hover">
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section for additional options -->
                <div id="options-step" class="mt-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">3. Souhaitez-vous bénéficier de services additionnels ?</h3>
                        <div id="options-container" class="space-y-4">
                            <!-- Options will be injected here -->
                            <div class="text-center text-gray-500">
                                <span class="custom-spinner" role="status" aria-hidden="true" id="loading-spinner-options"></span>
                                Chargement des options...
                            </div>
                        </div>
                    </div>
                </div>
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
                    <p class="text-lg font-bold text-gray-800 mb-2">Tarif TOTAL</p>
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
                <div id="cart-summary" class="bg-white border-2 border-yellow-400 rounded-lg p-6 shadow-sm" style="display: none;">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-black">Votre panier</h3>
                        <div id="cart-duration" class="text-sm text-gray-600 font-medium"></div>
                        <div class="custom-spinner" role="status" aria-hidden="true" id="loading-spinner-cart" style="display: none;"></div>
                    </div>
                    <div id="cart-items-container" class="panier-content divide-y divide-gray-200">
                        <!-- Cart items will be injected here -->
                    </div>
                    <div class="bg-yellow-custom rounded p-3 mt-4 flex justify-center items-center summary-total-container">
                        <span class="text-lg font-bold text-gray-dark">Procéder au paiement</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Front.footer-front')

<footer id="footer"></footer>
<script>

    let airportId = null;
    const serviceId = 'dfb8ac1b-8bb1-4957-afb4-1faedaf641b7';
    let globalProductsData = [];
    let globalLieuxData = [];
    const initialProducts = @json($products);
    let cartItems = []; // Unified state management for the cart
    let guestEmail = null; // Add this global variable

    const staticOptions = {
        priority: { id: 'opt_priority', libelle: 'Service Priority', prixUnitaire: 15 },
        premium: { id: 'opt_premium', libelle: 'Service Premium', prixUnitaire: 25 }
    };

    const productMapJs = {
        'Accessoires': { type: 'accessory', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path d="M12 14a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="2"/><path d="M17.94 6.06a8 8 0 00-11.88 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>', description: 'Petits objets comme un sac à main, un ordinateur portable ou un casque.' },
        'Bagage cabine': { type: 'cabin', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="6" y="8" width="12" height="10" rx="1" stroke="currentColor" stroke-width="2"/><path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/><circle cx="10" cy="18" r="1" fill="currentColor"/><circle cx="14" cy="18" r="1" fill="currentColor"/><path d="M10 10v4M14 10v4" stroke="currentColor" stroke-width="1.5"/></svg>', description: 'Valise de taille cabine, généralement jusqu\'à 55x35x25 cm.' },
        'Bagage soute': { type: 'hold', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="5" y="6" width="14" height="12" rx="1" stroke="currentColor" stroke-width="2"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/><path d="M5 10h14" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="15" r="1" fill="currentColor"/><circle cx="15" cy="15" r="1" fill="currentColor"/></svg>', description: 'Grande valise enregistrée en soute.' },
        'Bagage spécial': { type: 'special', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="4" y="7" width="16" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M8 17h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>', description: 'Objets volumineux ou hors format comme un équipement de sport ou un instrument de musique.' },
        'Vestiaire': { type: 'cloakroom', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path d="M16 10V8a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2" stroke="currentColor" stroke-width="2"/><path d="M8 10h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="2"/><path d="M8 10v-2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="1.5"/></svg>', description: 'Pour les manteaux, vestes ou autres vêtements sur cintre.' }
    };
    const defaultIconJs = '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path stroke="currentColor" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /><path stroke="currentColor" stroke-width="2" d="M9.5 9.5h.01v.01h-.01V9.5zm5 0h.01v.01h-.01V9.5zm-2.5 5a2.5 2.5 0 00-5 0h5z" /></svg>';

    document.addEventListener('DOMContentLoaded', function() {
        loadStateFromSession(); // Load state on page load

        // --- EVENT LISTENERS ---
        document.getElementById('back-to-step-1-btn').addEventListener('click', function() {
            document.getElementById('baggage-selection-step').style.display = 'none';
            document.getElementById('step-1').style.display = 'block';
            saveStateToSession();
        });

        document.getElementById('airport-select').addEventListener('change', function() { 
            airportId = this.value; 
            saveStateToSession();
        });
        document.getElementById('check-availability-btn').addEventListener('click', checkAvailability);
        
        document.getElementById('add-to-cart-btn').addEventListener('click', handleBaggageAddToCart);
        document.getElementById('quantity-plus').addEventListener('click', () => updateQuantity(1));
        document.getElementById('quantity-minus').addEventListener('click', () => updateQuantity(-1));

        document.getElementById('baggage-types-grid').addEventListener('click', (e) => {
            const target = e.target.closest('.baggage-option');
            if (target) {
                document.querySelectorAll('#baggage-types-grid .baggage-option').forEach(el => el.classList.remove('selected'));
                target.classList.add('selected');
                saveStateToSession();
            }
        });

        document.getElementById('cart-items-container').addEventListener('click', (e) => {
            const target = e.target.closest('.delete-item-btn');
            if (target) {
                const index = parseInt(target.dataset.index, 10);
                cartItems.splice(index, 1);
                updateCartDisplay(); // This will also save the state
            }
        });

        document.getElementById('options-container').addEventListener('click', (e) => {
            const header = e.target.closest('.option-header');
            const addButton = e.target.closest('.add-option-btn');

            if (header) {
                header.classList.toggle('open');
                const details = header.nextElementSibling;
                details.classList.toggle('hidden');
            }

            if(addButton && !addButton.disabled) {
                handleOptionAddToCart(addButton.dataset.optionKey);
            }
        });

        const dateDepotInput = document.getElementById('date-depot');
        const dateRecuperationInput = document.getElementById('date-recuperation');
        const heureDepotInput = document.getElementById('heure-depot');
        const heureRecuperationInput = document.getElementById('heure-recuperation');

        // Set min date for date-depot to today
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
        const dd = String(today.getDate()).padStart(2, '0');
        const todayFormatted = `${yyyy}-${mm}-${dd}`;
        dateDepotInput.min = todayFormatted;

        [dateDepotInput, dateRecuperationInput, heureDepotInput, heureRecuperationInput].forEach(input => {
            input.addEventListener('change', saveStateToSession);
        });

        dateDepotInput.addEventListener('change', function() {
            if (this.value) {
                dateRecuperationInput.min = this.value;
                if (dateRecuperationInput.value < this.value) {
                    dateRecuperationInput.value = '';
                    saveStateToSession();
                }
            }
        });

        // --- TOOLTIP LOGIC ---
        const tooltip = document.getElementById('baggage-tooltip');
        const baggageSelectionStep = document.getElementById('baggage-selection-step');

        baggageSelectionStep.addEventListener('mouseover', (e) => {
            const target = e.target.closest('.baggage-option');
            if (!target) return;

            const baggageLibelle = target.dataset.libelle;
            const productData = productMapJs[baggageLibelle];
            
            if (productData && productData.description) {
                tooltip.textContent = productData.description;
                tooltip.classList.remove('hidden');

                const rect = target.getBoundingClientRect();
                const tooltipRect = tooltip.getBoundingClientRect();

                let left = rect.left + window.scrollX + (rect.width / 2) - (tooltipRect.width / 2);
                let top = rect.top + window.scrollY - tooltipRect.height - 10;

                if (left < 0) left = 5;
                if (top < 0) top = rect.bottom + 10;

                tooltip.style.left = `${left}px`;
                tooltip.style.top = `${top}px`;
            }
        });

        baggageSelectionStep.addEventListener('mouseout', (e) => {
            const target = e.target.closest('.baggage-option');
            if (target) {
                tooltip.classList.add('hidden');
            }
        });
    });

    function saveStateToSession() {
        const selectedBaggageEl = document.querySelector('#baggage-types-grid .baggage-option.selected');
        const state = {
            airportId: document.getElementById('airport-select').value,
            dateDepot: document.getElementById('date-depot').value,
            heureDepot: document.getElementById('heure-depot').value,
            dateRecuperation: document.getElementById('date-recuperation').value,
            heureRecuperation: document.getElementById('heure-recuperation').value,
            isBaggageStepVisible: document.getElementById('baggage-selection-step').style.display === 'block',
            selectedBaggageProductId: selectedBaggageEl ? selectedBaggageEl.dataset.productId : null,
            quantity: document.getElementById('quantity-input').value,
            cartItems: cartItems,
            globalProductsData: globalProductsData,
            globalLieuxData: globalLieuxData,
            guestEmail: guestEmail // Add this line
        };
        sessionStorage.setItem('formState', JSON.stringify(state));
    }

    function loadStateFromSession() {
        const state = JSON.parse(sessionStorage.getItem('formState'));
        if (!state) return;

        document.getElementById('airport-select').value = state.airportId;
        airportId = state.airportId;
        document.getElementById('date-depot').value = state.dateDepot;
        document.getElementById('heure-depot').value = state.heureDepot;
        document.getElementById('date-recuperation').value = state.dateRecuperation;
        document.getElementById('heure-recuperation').value = state.heureRecuperation;
        document.getElementById('quantity-input').value = state.quantity || '1';
        
        globalProductsData = state.globalProductsData || [];
        globalLieuxData = state.globalLieuxData || [];
        cartItems = state.cartItems || [];
        guestEmail = state.guestEmail || null; // Add this line

        if (state.isBaggageStepVisible) {
            document.getElementById('step-1').style.display = 'none';
            document.getElementById('baggage-selection-step').style.display = 'block';
            
            if (state.selectedBaggageProductId) {
                const baggageEl = document.querySelector(`.baggage-option[data-product-id="${state.selectedBaggageProductId}"]`);
                if (baggageEl) {
                    baggageEl.classList.add('selected');
                }
            }
            
            const dateDepot = document.getElementById('date-depot').value;
            const heureDepot = document.getElementById('heure-depot').value;
            const dateRecuperation = document.getElementById('date-recuperation').value;
            const heureRecuperation = document.getElementById('heure-recuperation').value;
            const debut = new Date(`${dateDepot}T${heureDepot}:00`);
            const fin = new Date(`${dateRecuperation}T${heureRecuperation}:00`);
            const dureeEnMinutes = Math.ceil(Math.abs(fin - debut) / (1000 * 60));

            if (dureeEnMinutes > 0) {
                displayOptions(dureeEnMinutes);
            }
        }
        
        updateCartDisplay();
    }

    async function checkAvailability() {
        const spinner = document.getElementById('loading-spinner-availability');
        const btn = this;
        spinner.style.display = 'inline-block';
        btn.disabled = true;

        const dateDepot = document.getElementById('date-depot').value;
        const heureDepot = document.getElementById('heure-depot').value;

        if (!airportId || !dateDepot || !heureDepot) {
            alert('Veuillez remplir tous les champs : aéroport, date et heure de dépôt.');
            spinner.style.display = 'none';
            btn.disabled = false;
            return;
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
                document.getElementById('step-1').style.display = 'none';
                document.getElementById('baggage-selection-step').style.display = 'block';
                getQuoteAndDisplay();
            } else {
                alert(result.message || 'La plateforme est fermée à la date de dépôt sélectionnée.');
            }
        } catch (error) {
            console.error('Erreur lors de la vérification de disponibilité:', error);
            alert('Une erreur technique est survenue.');
        } finally {
            spinner.style.display = 'none';
            btn.disabled = false;
        }
    }

    function updateQuantity(amount) {
        const input = document.getElementById('quantity-input');
        let currentValue = parseInt(input.value, 10);
        currentValue += amount;
        if (currentValue < 1) currentValue = 1;
        input.value = currentValue;
    }

    function handleBaggageAddToCart() {
        const selectedBaggageEl = document.querySelector('#baggage-types-grid .baggage-option.selected');
        if (!selectedBaggageEl) {
            alert('Veuillez sélectionner un type de bagage.');
            return;
        }

        const productId = selectedBaggageEl.dataset.productId;
        const libelle = selectedBaggageEl.dataset.libelle;
        const type = selectedBaggageEl.dataset.type;
        const quantity = parseInt(document.getElementById('quantity-input').value, 10);

        const existingItem = cartItems.find(item => item.itemCategory === 'baggage' && item.productId === productId);
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cartItems.push({ itemCategory: 'baggage', productId, libelle, type, quantity });
        }
        updateCartDisplay();
    }
    
    function handleOptionAddToCart(optionKey) {
        const option = staticOptions[optionKey];
        const detailsDiv = document.getElementById(`details-${option.id}`);
        
        const infoComplementaires = detailsDiv.querySelector('input[name^="option_info_"]').value;
        const commentaire = detailsDiv.querySelector('textarea[name^="option_comment_"]').value;
        let lieuId = null;
        if (optionKey === 'premium') {
            const select = detailsDiv.querySelector('select[name^="option_lieu_"]');
            if(select) lieuId = select.value;
        }

        cartItems.push({
            itemCategory: 'option', 
            id: option.id, 
            key: optionKey,
            libelle: option.libelle,
            prix: option.prixUnitaire,
            lieu_id: lieuId,
            informations_complementaires: infoComplementaires,
            commentaire: commentaire
        });
        updateCartDisplay();
    }

    async function getQuoteAndDisplay() {
        const cartSpinner = document.getElementById('loading-spinner-cart');
        const optionsSpinner = document.getElementById('loading-spinner-options');
        if(cartSpinner) cartSpinner.style.display = 'inline-block';
        if(optionsSpinner) optionsSpinner.style.display = 'inline-block';

        const dateDepot = document.getElementById('date-depot').value;
        const heureDepot = document.getElementById('heure-depot').value;
        const dateRecuperation = document.getElementById('date-recuperation').value;
        const heureRecuperation = document.getElementById('heure-recuperation').value;

        if (!dateDepot || !heureDepot || !dateRecuperation || !heureRecuperation) {
            alert('Veuillez vérifier les dates et heures de dépôt et de récupération.');
            if(cartSpinner) cartSpinner.style.display = 'none';
            if(optionsSpinner) optionsSpinner.style.display = 'none';
            return;
        }

        const debut = new Date(`${dateDepot}T${heureDepot}:00`);
        const fin = new Date(`${dateRecuperation}T${heureRecuperation}:00`);
        const dureeEnMinutes = Math.ceil(Math.abs(fin - debut) / (1000 * 60));

        if (dureeEnMinutes <= 0) {
            alert('La date de récupération doit être postérieure à la date de dépôt.');
            if(cartSpinner) cartSpinner.style.display = 'none';
            if(optionsSpinner) optionsSpinner.style.display = 'none';
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
                alert('Erreur lors de la récupération des tarifs : ' + (result.message || 'Réponse invalide'));
            }
        } catch (error) {
            console.error('Erreur lors de la récupération des tarifs et lieux:', error);
            alert('Une erreur technique est survenue.');
        } finally {
            if(cartSpinner) cartSpinner.style.display = 'none';
            if(optionsSpinner) optionsSpinner.parentElement.style.display = 'none';
        }
    }

    function displayOptions(dureeEnMinutes) {
        const optionsContainer = document.getElementById('options-container');
        optionsContainer.innerHTML = '';
        const dureeEnHeures = dureeEnMinutes / 60;

        const createOptionHTML = (optionKey, isEnabled, lieuxHTML = '') => {
            const option = staticOptions[optionKey];
            const isAlreadyInCart = cartItems.some(item => item.itemCategory === 'option' && item.key === optionKey);
            return `
                <div class="border rounded-2xl p-5 bg-white ${!isEnabled ? 'opacity-50' : ''}">
                    <div class="option-header flex items-center justify-between cursor-pointer">
                        <div>
                            <span class="text-lg font-medium">${option.libelle}</span>
                            <p class="text-gray-500 text-sm mt-1">${optionKey === 'priority' ? 'Bénéficiez d’un traitement prioritaire pour vos bagages.' : 'Accédez à un service complet de gestion de vos bagages de bout en bout.'}</p>
                        </div>
                        <div class="flex items-center">
                            <span class="text-yellow-custom font-semibold text-lg mr-4">+${option.prixUnitaire} €</span>
                            <svg class="chevron-icon w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                    <div id="details-${option.id}" class="hidden mt-4 border-t pt-4 space-y-3">
                        ${lieuxHTML}
                        <label class="block text-sm font-medium">Informations complémentaires</label>
                        <input type="text" name="option_info_${option.id}" class="input-style w-full" placeholder="Ex: N° de vol, provenance...">
                        <label class="block text-sm font-medium">Commentaire</label>
                        <textarea name="option_comment_${option.id}" class="input-style w-full" rows="3" placeholder="Ajoutez un commentaire..."></textarea>
                        <button type="button" data-option-key="${optionKey}" class="add-option-btn bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover w-full mt-3" ${!isEnabled || isAlreadyInCart ? 'disabled' : ''}>
                            ${isAlreadyInCart ? 'Ajouté au panier' : 'Ajouter au panier'}
                        </button>
                    </div>
                </div>`;
        };

        const isPriorityEnabled = dureeEnHeures < 72;
        optionsContainer.innerHTML += createOptionHTML('priority', isPriorityEnabled);

        const isPremiumEnabled = globalLieuxData.length > 0;
        const lieuxOptionsHTML = isPremiumEnabled ? globalLieuxData.map(lieu => `<option value="${lieu.id}">${lieu.libelle}</option>`).join('') : '';
        const premiumLieuxHTML = `<label class="block text-sm font-medium">Lieu de rendez-vous *</label><select name="option_lieu_opt_premium" class="input-style custom-select w-full">${lieuxOptionsHTML}</select>`;
        optionsContainer.innerHTML += createOptionHTML('premium', isPremiumEnabled, premiumLieuxHTML);
    }

    function updateCartDisplay() {
        const cartItemsContainer = document.getElementById('cart-items-container');
        const cartElement = document.getElementById('cart-summary');
        const emptyCartElement = document.getElementById('empty-cart');
        const durationElement = document.getElementById('cart-duration');
        cartItemsContainer.innerHTML = '';
        let total = 0;
        let diffDays = 0;

        // Calculate and display duration
        const dateDepot = document.getElementById('date-depot').value;
        const dateRecuperation = document.getElementById('date-recuperation').value;
        if (dateDepot && dateRecuperation) {
            const start = new Date(dateDepot);
            const end = new Date(dateRecuperation);
            const diffTime = Math.abs(end - start);
            diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            durationElement.textContent = `${diffDays} jour(s)`;
        } else {
            durationElement.textContent = '';
        }

        cartItems.forEach((item, index) => {
            let itemTotal = 0;
            if (item.itemCategory === 'baggage') {
                const product = globalProductsData.find(p => p.id === item.productId);
                const itemPrice = product ? product.prixUnitaire : 0;
                
                let pricePerDayPerUnit = itemPrice;
                if (diffDays > 0) {
                    pricePerDayPerUnit = itemPrice / diffDays;
                }

                itemTotal = itemPrice * item.quantity;
                cartItemsContainer.innerHTML += `
                    <div class="py-2 flex justify-between items-center">
                        <div>
                            <span class="font-medium">${item.quantity} x ${item.libelle}</span>
                            <span class="block text-xs text-gray-500">${pricePerDayPerUnit.toFixed(2)} € / jour </span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="font-semibold">${itemTotal.toFixed(2)} €</span>
                            <button type="button" class="delete-item-btn" data-index="${index}" title="Supprimer">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            </button>
                        </div>
                    </div>`;
            } else if (item.itemCategory === 'option') {
                itemTotal = item.prix;
                cartItemsContainer.innerHTML += `
                    <div class="py-2 flex justify-between items-center text-sm">
                        <div>
                            <span class="font-medium">+ ${item.libelle}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="font-semibold">${itemTotal.toFixed(2)} €</span>
                            <button type="button" class="delete-item-btn" data-index="${index}" title="Supprimer">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            </button>
                        </div>
                    </div>`;
            }
            total += itemTotal;
        });

        // Update UI
        const summaryPriceElement = document.getElementById('summary-price');
        const totalPanierElements = document.querySelectorAll('.total-panier');

        if (cartItems.length > 0) {
            cartElement.style.display = 'block';
            emptyCartElement.style.display = 'none';
            cartElement.classList.add('cart-updated');
            setTimeout(() => cartElement.classList.remove('cart-updated'), 500);
        } else {
            cartElement.style.display = 'none';
            emptyCartElement.style.display = 'block';
        }
        
        summaryPriceElement.textContent = `${total.toFixed(2)} €`;
        totalPanierElements.forEach(el => el.textContent = `${total.toFixed(2)}€`);
        
        const payButton = document.querySelector('.summary-total-container');
        if (total > 0) {
            payButton.style.cursor = 'pointer';
            payButton.onclick = handleTotalClick;
        } else {
            payButton.style.cursor = 'default';
            payButton.onclick = null;
        }
        
        // Disable option buttons if already in cart
        document.querySelectorAll('.add-option-btn').forEach(btn => {
            const isAlreadyInCart = cartItems.some(item => item.itemCategory === 'option' && item.key === btn.dataset.optionKey);
            btn.disabled = isAlreadyInCart;
            if(isAlreadyInCart) btn.textContent = 'Ajouté au panier';
        });

        saveStateToSession(); // Save state after any cart update
    }
    
    async function handleTotalClick() {
        if (cartItems.length === 0) {
            alert("Votre panier est vide.");
            return;
        }

        try {
            const authResponse = await fetch('/check-auth-status');
            const authData = await authResponse.json();
            
            if (!authData.authenticated) {
                if (!guestEmail) { // Check global guestEmail
                    guestEmail = prompt("Veuillez entrer votre adresse e-mail pour continuer en tant qu'invité:", "");
                    if (!guestEmail) {
                        alert("Une adresse e-mail est requise pour continuer.");
                        return;
                    }
                    // Basic email validation
                    if (!/^\S+@\S+\.\S+$/.test(guestEmail)) {
                        alert("Veuillez entrer une adresse e-mail valide.");
                        guestEmail = null; // Clear invalid email
                        return;
                    }
                    saveStateToSession(); // Save email after successful prompt
                }
            } else {
                guestEmail = null; // Clear guest email if user logs in
                saveStateToSession();
            }

            const baggages = cartItems.filter(i => i.itemCategory === 'baggage').map(item => ({ type: item.type, quantity: item.quantity }));
            const options = cartItems.filter(i => i.itemCategory === 'option').map(item => ({
                id: item.id,
                lieu_id: item.lieu_id,
                informations_complementaires: item.informations_complementaires,
                commentaire: item.commentaire
            }));

            const formData = {
                airportId: airportId,
                dateDepot: document.getElementById('date-depot').value,
                heureDepot: document.getElementById('heure-depot').value,
                dateRecuperation: document.getElementById('date-recuperation').value,
                heureRecuperation: document.getElementById('heure-recuperation').value,
                baggages: baggages,
                products: globalProductsData,
                options: options
            };

            if (guestEmail) {
                formData.guest_email = guestEmail;
            }

            const prepareResponse = await fetch('/prepare-payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify(formData)
            });

            const resultData = await prepareResponse.json();
            if (prepareResponse.ok) {
                window.location.href = resultData.redirect_url;
            } else {
                alert('Erreur: ' + (resultData.message || 'Erreur inconnue.'));
            }
        } catch (error) {
            console.error('Erreur critique dans handleTotalClick:', error);
            alert('Une erreur technique est survenue.');
        }
    }

</script>
<script>
    // Second script block for completion styles etc.
    document.addEventListener('DOMContentLoaded', () => {
        const fields = [
            document.getElementById('airport-select'),
            document.getElementById('date-depot'),
            document.getElementById('heure-depot'),
            document.getElementById('date-recuperation'),
            document.getElementById('heure-recuperation')
        ];

        fields.forEach(field => {
            if (field) {
                field.addEventListener('change', handleInputCompletion);
                field.addEventListener('input', handleInputCompletion);
                handleInputCompletion({ target: field });
            }
        });
    });

    function handleInputCompletion(event) {
        const input = event.target;
        if (input.value.trim() !== '') {
            input.classList.add('input-completed');
        } else {
            input.classList.remove('input-completed');
        }
    }
</script>
   

</body>
</html>