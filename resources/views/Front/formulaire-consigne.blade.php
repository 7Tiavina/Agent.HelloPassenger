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
<body class="min-h-screen bg-white" data-selected-airport-id="{{ $selectedAirportId ?? '' }}">

<!-- Loader Overlay -->
<div id="loader" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[10003] flex items-center justify-center">
    <div class="custom-spinner !w-12 !h-12 !border-4"></div>
</div>

<!-- Custom Modal -->
<div id="custom-modal-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[10005] flex items-center justify-center px-4">
    <div id="custom-modal" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md transform transition-all" onclick="event.stopPropagation();">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
            <h3 id="custom-modal-title" class="text-xl font-bold text-gray-800"></h3>
            <button id="custom-modal-close" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- Modal Body -->
        <div class="py-4">
            <p id="custom-modal-message" class="text-gray-600"></p>
            <div id="custom-modal-prompt-container" class="hidden mt-4">
                <label id="custom-modal-prompt-label" for="custom-modal-input" class="block text-sm font-medium text-gray-700 mb-1"></label>
                <input type="text" id="custom-modal-input" class="input-style w-full">
                <p id="custom-modal-error" class="text-red-500 text-sm mt-1 hidden"></p>
            </div>
        </div>
        <!-- Modal Footer -->
        <div id="custom-modal-footer" class="flex justify-end pt-3 border-t border-gray-200 space-x-3">
            <button id="custom-modal-cancel-btn" class="hidden bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-full btn-hover">Annuler</button>
            <button id="custom-modal-confirm-btn" class="bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover">OK</button>
        </div>
    </div>
</div>

<!-- Options Advertisement Modal -->
<div id="options-advert-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-[10004] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl transform transition-all max-h-[90vh] overflow-y-auto relative">
        <!-- Close Button -->
        <button id="close-options-advert-modal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 z-10">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="p-8 text-center">
            <h2 class="text-3xl font-bold text-gray-800">Optimisez votre expérience !</h2>
            <p class="mt-2 text-gray-600">Ajoutez nos services exclusifs pour un voyage sans tracas.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-gray-200">
            <!-- Priority Option -->
            <div id="advert-option-priority" class="hidden bg-white p-8">
                <div class="text-center">
                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">PRIORITAIRE</span>
                    <h3 class="mt-4 text-2xl font-bold text-gray-900">Service Priority</h3>
                    <p class="mt-2 text-gray-500">Bénéficiez d’un traitement prioritaire pour vos bagages à la dépose et à la récupération.</p>
                    <p id="advert-priority-price" class="mt-4 text-3xl font-extrabold text-gray-900">+15 €</p>
                    <button id="add-priority-from-modal" data-option-key="priority" class="mt-6 w-full bg-transparent border border-gray-400 text-gray-700 font-bold py-3 px-4 rounded-lg btn-hover hover:bg-gray-100">Ajouter au panier</button>
                </div>
            </div>

            <!-- Premium Option -->
            <div id="advert-option-premium" class="hidden bg-white p-8 relative">
                <div id="premium-available-content">
                    <div class="text-center">
                        <span class="inline-block bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">PREMIUM</span>
                        <h3 class="mt-4 text-2xl font-bold text-gray-900">Service Premium</h3>
                        <p class="mt-2 text-gray-500">Permet de remettre ou récupérer ses bagages directement à l’endroit exact choisi à l’aéroport, avec l’aide d’un porteur dédié. Le client indique le lieu, son mode de transport et un commentaire, et l’équipe s’occupe de tout.</p>
                        <p id="advert-premium-price" class="mt-4 text-3xl font-extrabold text-gray-900">+25 €</p>
                        <div id="premium-details-modal" class="mt-4 text-left space-y-3">
                            <!-- Premium specific fields will be injected here -->
                        </div>
                        <button id="add-premium-from-modal" data-option-key="premium" class="mt-6 w-full bg-transparent border border-gray-400 text-gray-700 font-bold py-3 px-4 rounded-lg btn-hover hover:bg-gray-100">Ajouter au panier</button>
                    </div>
                </div>
                <div id="premium-unavailable-message" class="absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-90 rounded-lg hidden">
                    <p class="text-lg font-semibold text-gray-600">Service Premium indisponible</p>
                </div>
            </div>
        </div>

        <div class="p-6 text-center bg-gray-50">
            <button id="continue-from-options-modal" class="bg-yellow-custom text-gray-dark font-bold py-3 px-8 rounded-full btn-hover">Valider et continuer →</button>
        </div>
    </div>
</div>

<!-- Quick Date Edit Modal -->
<div id="quick-date-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-[10001] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-800">Modifier les dates</h3>
            <button id="close-quick-date-modal" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            <!-- Date Blocks -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Depot Block -->
                <div id="quick-depot-block" class="border border-gray-300 rounded-lg p-4 text-center cursor-pointer">
                    <p class="font-semibold text-gray-700">DÉPÔT</p>
                    <p id="quick-depot-date-display" class="text-2xl font-bold text-gray-900 mt-2">--</p>
                    <p id="quick-depot-time-display" class="text-lg text-gray-600">--:--</p>
                </div>
                <!-- Retrait Block -->
                <div id="quick-retrait-block" class="border border-gray-200 rounded-lg p-4 text-center cursor-pointer">
                    <p class="font-semibold text-gray-700">RETRAIT</p>
                    <p id="quick-retrait-date-display" class="text-2xl font-bold text-gray-900 mt-2">--</p>
                    <p id="quick-retrait-time-display" class="text-lg text-gray-600">--:--</p>
                </div>
            </div>

            <!-- Date Selection Mode -->
            <div class="text-center hidden">
                <div class="inline-flex rounded-md shadow-sm" role="group">
                    <button type="button" id="qdm-btn-depot" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white rounded-l-lg border border-gray-200 hover:bg-gray-100 focus:z-10 focus:ring-2 focus:ring-yellow-custom">
                        Modifier Dépôt
                    </button>
                    <button type="button" id="qdm-btn-retrait" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 focus:z-10 focus:ring-2 focus:ring-yellow-custom">
                        Modifier Retrait
                    </button>
                </div>
            </div>

            <!-- Quick Select Buttons -->
            <div id="qdm-quick-select-container" class="p-4 bg-gray-50 rounded-lg">
                <p id="qdm-editing-label" class="text-center font-semibold mb-4">Modification de la date de Dépôt</p>
                <div class="flex justify-center space-x-4">
                    <button data-day="today" class="qdm-day-btn py-2 px-6 bg-gray-200 rounded-full">Auj.</button>
                    <button data-day="tomorrow" class="qdm-day-btn py-2 px-6 bg-gray-200 rounded-full">Demain</button>
                    <button data-day="custom" class="qdm-day-btn py-2 px-6 bg-gray-200 rounded-full">Personnalisé</button>
                </div>
                <!-- Custom Date Input -->
                <div id="qdm-custom-date-container" class="hidden mt-4 text-center">
                    <input type="date" id="qdm-custom-date-input" class="input-style mx-auto">
                </div>
            </div>

            <!-- Hour Selection -->
            <div id="qdm-hour-container" class="p-4 bg-gray-50 rounded-lg">
                 <p class="text-center font-semibold mb-4">Heure</p>
                 <div id="qdm-hour-grid" class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                    <!-- Hour buttons will be injected here -->
                 </div>
                 <div id="qdm-custom-hour-container" class="hidden mt-4 text-center">
                    <input type="time" id="qdm-custom-time-input" class="input-style mx-auto">
                 </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-center p-6 border-t border-gray-200">
            <button id="qdm-validate-btn" class="bg-yellow-custom text-gray-dark font-bold py-2 px-6 rounded-full btn-hover w-full">
                Valider
            </button>
        </div>
    </div>
</div>




<div id="baggage-tooltip" class="hidden absolute z-10 p-2 text-sm font-medium text-white bg-gray-800 rounded-lg shadow-sm" role="tooltip">
    <!-- Tooltip content will be injected here -->
</div>

@include('Front.header-front')

<div class="max-w-6xl mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-2">
        <h1 class="text-3xl font-bold text-gray-800">Réserver une consigne</h1>
        <button id="reset-form-btn" class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center space-x-1 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <span>Réinitialiser</span>
        </button>
    </div>
    <p class="text-gray-600 mb-8">
        Sélectionnez le type de consigne et suivez les étapes du formulaire. Nous vous indiquerons les informations à fournir.
    </p>

    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <span>Accueil</span>
            <span>→</span>
            <span class="text-gray-800 font-medium">Réserver une consigne</span>
        </div>
        <button id="back-to-step-1-btn" class="hidden bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour
        </button>
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
                <!-- Display Airport Name -->
                <div class="bg-gray-100 p-4 rounded-lg mb-6 text-center">
                    <p class="text-sm font-medium text-gray-600">AÉROPORT SÉLECTIONNÉ</p>
                    <p id="display-airport-name" class="text-lg font-bold text-gray-900"></p>
                </div>

                <!-- Display Dates -->
                <div id="dates-display" class="flex justify-around bg-gray-100 p-4 rounded-lg mb-6 text-center cursor-pointer hover:bg-gray-200 hover:shadow-md transition-all duration-200">
                    <div>
                        <p class="text-sm font-medium text-gray-600">DÉPÔT</p>
                        <p id="display-date-depot" class="text-lg font-bold text-gray-900"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">RETRAIT</p>
                        <p id="display-date-recuperation" class="text-lg font-bold text-gray-900"></p>
                    </div>
                </div>

                <!-- New Baggage Selection -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800">1. Choisissez vos bagages</h3>
                    </div>
                    <div id="baggage-grid-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mt-3">
                        @if(isset($products) && count($products) > 0)
                            @php
                                $product_map_icons = [
                                    'Accessoires' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path d="M12 14a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="2"/><path d="M17.94 6.06a8 8 0 00-11.88 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
                                    'Bagage cabine' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="6" y="8" width="12" height="10" rx="1" stroke="currentColor" stroke-width="2"/><path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/><circle cx="10" cy="18" r="1" fill="currentColor"/><circle cx="14" cy="18" r="1" fill="currentColor"/><path d="M10 10v4M14 10v4" stroke="currentColor" stroke-width="1.5"/></svg>',
                                    'Bagage soute' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="5" y="6" width="14" height="12" rx="1" stroke="currentColor" stroke-width="2"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/><path d="M5 10h14" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="15" r="1" fill="currentColor"/><circle cx="15" cy="15" r="1" fill="currentColor"/></svg>',
                                    'Bagage spécial' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="4" y="7" width="16" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M8 17h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
                                    'Vestiaire' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path d="M16 10V8a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2" stroke="currentColor" stroke-width="2"/><path d="M8 10h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="2"/><path d="M8 10v-2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="1.5"/></svg>'
                                ];
                                $default_icon = '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path stroke="currentColor" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /><path stroke="currentColor" stroke-width="2" d="M9.5 9.5h.01v.01h-.01V9.5zm5 0h.01v.01h-.01V9.5zm-2.5 5a2.5 2.5 0 00-5 0h5z" /></svg>';
                            @endphp
                            @foreach($products as $product)
                                @php
                                    $libelle = $product['libelle'];
                                    $icon = $product_map_icons[$libelle] ?? $default_icon;
                                @endphp
                                <div class="baggage-option p-4 rounded-lg flex flex-col items-center justify-between space-y-2" data-product-id="{{ $product['id'] }}">
                                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                        {!! $icon !!}
                                    </div>
                                    <div class="flex items-center justify-center space-x-1">
                                        <span class="text-sm font-medium text-center">{{ $libelle }}</span>
                                        <span class="info-icon cursor-pointer" data-libelle="{{ $libelle }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">                                        <button type="button" class="quantity-change-btn w-8 h-8 border border-gray-300 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-100" data-action="minus" data-product-id="{{ $product['id'] }}">−</button>
                                        <span class="font-bold text-lg w-5 text-center" data-quantity-display="{{ $product['id'] }}">0</span>
                                        <button type="button" class="quantity-change-btn w-8 h-8 border border-gray-300 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-100" data-action="plus" data-product-id="{{ $product['id'] }}">+</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
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

        <div class="w-full lg:w-full relative hidden" id="sticky-wrapper">
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
    // Injection des données Blade dans JS global
    var initialProducts = @json($products);
</script>

<!-- Scripts JS externalisés -->
<script src="{{ asset('js/state.js') }}"></script>
<script src="{{ asset('js/utils.js') }}"></script>
<script src="{{ asset('js/modal.js') }}"></script>
<script src="{{ asset('js/quick-date-modal.js') }}"></script>
<script src="{{ asset('js/cart.js') }}"></script>
<script src="{{ asset('js/booking.js') }}"></script>

<script>
    // Ce script reste en ligne car il contient une route Blade resolue par PHP
    document.addEventListener('DOMContentLoaded', function () {
        // Initialisation des listeners qui dépendent d'éléments du DOM chargés
        if(typeof setupQdmListeners !== 'undefined') setupQdmListeners();

        // Le setup des listeners de la modale custom est déjà dans modal.js
        // Le setup des listeners du booking est dans booking.js
        
        // Listener pour le bouton de réinitialisation
        document.getElementById('reset-form-btn').addEventListener('click', async function () {
            const confirmed = await showCustomConfirm(
                'Réinitialiser la commande',
                'Voulez-vous vraiment continuer ? Toutes les données saisies pour votre commande actuelle seront définitivement perdues.'
            );
            if (confirmed) {
                const loader = document.getElementById('loader');
                if (loader) {
                    loader.classList.remove('hidden');
                }

                sessionStorage.removeItem('formState');

                try {
                    // La route 'session.reset' est necessaire ici.
                    await fetch('{{ route("session.reset") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                } catch (error) {
                    console.error('Failed to reset server session:', error);
                }

                setTimeout(() => {
                    location.reload();
                }, 500);
            }
        });
    });
</script>

</body>
</html>