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

<!-- Loader Overlay -->
<div id="loader" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center">
    <div class="custom-spinner !w-12 !h-12 !border-4"></div>
</div>

<!-- Custom Modal -->
<div id="custom-modal-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center px-4">
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
<div id="options-advert-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-[10000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl transform transition-all max-h-[90vh] overflow-y-auto relative">
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
                    <p class="mt-4 text-3xl font-extrabold text-gray-900">+15 €</p>
                    <button id="add-priority-from-modal" data-option-key="priority" class="mt-6 w-full bg-yellow-custom text-gray-dark font-bold py-3 px-4 rounded-lg btn-hover">Ajouter au panier</button>
                </div>
            </div>

            <!-- Premium Option -->
            <div id="advert-option-premium" class="hidden bg-white p-8">
                <div class="text-center">
                    <span class="inline-block bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">PREMIUM</span>
                    <h3 class="mt-4 text-2xl font-bold text-gray-900">Service Premium</h3>
                    <p class="mt-2 text-gray-500">Un agent vous attend à un point de rendez-vous défini pour une prise en charge VIP.</p>
                    <p class="mt-4 text-3xl font-extrabold text-gray-900">+25 €</p>
                    <div id="premium-details-modal" class="mt-4 text-left space-y-3">
                        <!-- Premium specific fields will be injected here -->
                    </div>
                    <button id="add-premium-from-modal" data-option-key="premium" class="mt-6 w-full bg-purple-600 text-white font-bold py-3 px-4 rounded-lg btn-hover">Ajouter au panier</button>
                </div>
            </div>
        </div>

        <div class="p-6 text-center bg-gray-50">
            <button id="continue-from-options-modal" class="text-gray-600 font-medium hover:text-gray-900">Valider et continuer →</button>
        </div>
    </div>
</div>



<div id="baggage-tooltip" class="hidden absolute z-10 p-2 text-sm font-medium text-white bg-gray-800 rounded-lg shadow-sm" role="tooltip">
    <!-- Tooltip content will be injected here -->
</div>

@include('Front.header-front')

<div class="max-w-6xl mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Réserver une consigne</h1>
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
                <!-- Display Dates -->
                <div id="dates-display" class="flex justify-around bg-gray-100 p-4 rounded-lg mb-6 text-center">
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
                                    'Bagage spécial' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="4" y="7" width="16" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M8 17h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
                                    'Vestiaire' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path d="M16 10V8a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2" stroke="currentColor" stroke-width="2"/><path d="M8 10h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="2"/><path d="M8 10v-2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="1.5"/></svg>'
                                ];
                                $default_icon = '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path stroke="currentColor" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /><path stroke="currentColor" stroke-width="2" d="M9.5 9.5h.01v.01h-.01V9.5zm5 0h.01v.01h-.01V9.5zm-2.5 5a2.5 2.5 0 00-5 0h5z" /></svg>';
                            @endphp
                            @foreach($products as $product)
                                @php
                                    $libelle = $product['libelle'];
                                    $icon = $product_map_icons[$libelle] ?? $default_icon;
                                @endphp
                                <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2" data-product-id="{{ $product['id'] }}">
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
                                    <div class="flex items-center space-x-2 mt-2">
                                        <button type="button" class="quantity-change-btn w-8 h-8 border border-gray-300 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-100" data-action="minus" data-product-id="{{ $product['id'] }}">−</button>
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
    // --- MODAL ---
    let modalResolve;

    function showCustomAlert(title, message) {
        const modalOverlay = document.getElementById('custom-modal-overlay');
        const modalTitle = document.getElementById('custom-modal-title');
        const modalMessage = document.getElementById('custom-modal-message');
        const promptContainer = document.getElementById('custom-modal-prompt-container');
        const modalCancelBtn = document.getElementById('custom-modal-cancel-btn');
        const modalConfirmBtn = document.getElementById('custom-modal-confirm-btn');

        modalTitle.textContent = title;
        modalMessage.textContent = message;

        promptContainer.classList.add('hidden');
        modalCancelBtn.classList.add('hidden');
        modalConfirmBtn.textContent = 'OK';

        modalOverlay.classList.remove('hidden');

        return new Promise(resolve => {
            modalResolve = resolve;
        });
    }

    function showCustomPrompt(title, message, label) {
        const modalOverlay = document.getElementById('custom-modal-overlay');
        const modalTitle = document.getElementById('custom-modal-title');
        const modalMessage = document.getElementById('custom-modal-message');
        const promptContainer = document.getElementById('custom-modal-prompt-container');
        const promptLabel = document.getElementById('custom-modal-prompt-label');
        const promptInput = document.getElementById('custom-modal-input');
        const modalError = document.getElementById('custom-modal-error');
        const modalCancelBtn = document.getElementById('custom-modal-cancel-btn');
        const modalConfirmBtn = document.getElementById('custom-modal-confirm-btn');

        modalTitle.textContent = title;
        modalMessage.textContent = message;
        promptLabel.textContent = label;

        promptContainer.classList.remove('hidden');
        promptInput.value = '';
        modalError.classList.add('hidden');

        modalCancelBtn.classList.remove('hidden');
        modalConfirmBtn.textContent = 'Confirmer';
        
        modalOverlay.classList.remove('hidden');

        return new Promise(resolve => {
            modalResolve = resolve;
        });
    }

    function showLoginOrGuestPrompt() {
        const modalOverlay = document.getElementById('custom-modal-overlay');
        const modalTitle = document.getElementById('custom-modal-title');
        const modalMessage = document.getElementById('custom-modal-message');
        const promptContainer = document.getElementById('custom-modal-prompt-container');
        const modalCancelBtn = document.getElementById('custom-modal-cancel-btn');
        const modalConfirmBtn = document.getElementById('custom-modal-confirm-btn');
        const modalFooter = document.getElementById('custom-modal-footer');

        modalTitle.textContent = 'Comment souhaitez-vous procéder ?';
        modalMessage.textContent = 'Connectez-vous pour utiliser vos informations enregistrées ou continuez en tant qu\'invité.';
        promptContainer.classList.add('hidden'); // Hide prompt input

        // Clear existing buttons and add new ones
        modalFooter.innerHTML = `
            <button id="btn-continue-guest" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-full btn-hover">Continuer en invité</button>
            <button id="btn-login-modal" class="bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover">Se connecter</button>
        `;
        
        modalOverlay.classList.remove('hidden');

        return new Promise(resolve => {
            modalResolve = resolve;
            document.getElementById('btn-login-modal').onclick = () => {
                closeModal();
                resolve('login');
            };
            document.getElementById('btn-continue-guest').onclick = () => {
                closeModal();
                resolve('guest');
            };
        });
    }

    function closeModal() {
        document.getElementById('custom-modal-overlay').classList.add('hidden');
        // Clean up dynamically added password field
        const passwordField = document.getElementById('custom-modal-password');
        if (passwordField) {
            passwordField.previousElementSibling.remove(); // Remove label
            passwordField.remove(); // Remove input
        }
        // Restore default footer buttons
        const modalFooter = document.getElementById('custom-modal-footer');
        modalFooter.innerHTML = `
            <button id="custom-modal-cancel-btn" class="hidden bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-full btn-hover">Annuler</button>
            <button id="custom-modal-confirm-btn" class="bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover">OK</button>
        `;
        // Re-attach default listeners (if they were removed)
        document.getElementById('custom-modal-cancel-btn').onclick = () => {
            closeModal();
            if (modalResolve) modalResolve(null);
        };
        document.getElementById('custom-modal-confirm-btn').onclick = () => {
            const isPrompt = !document.getElementById('custom-modal-prompt-container').classList.contains('hidden');
            if (isPrompt) {
                const value = document.getElementById('custom-modal-input').value;
                if (value.trim() === '' || !/^\S+@\S+\.\S+$/.test(value)) {
                    document.getElementById('custom-modal-error').textContent = 'Veuillez entrer une adresse e-mail valide.';
                    document.getElementById('custom-modal-error').classList.remove('hidden');
                    return;
                }
                closeModal();
                if (modalResolve) modalResolve(value);
            } else {
                closeModal();
                if (modalResolve) modalResolve(true);
            }
        };
    }
    // --- END MODAL ---

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

    let isPriorityAvailable = false;
    let isPremiumAvailable = false;


    const productMapJs = {
        'Accessoires': { type: 'accessory', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path d="M12 14a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="2"/><path d="M17.94 6.06a8 8 0 00-11.88 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>', description: 'Petits objets comme un sac à main, un ordinateur portable ou un casque.' },
        'Bagage cabine': { type: 'cabin', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="6" y="8" width="12" height="10" rx="1" stroke="currentColor" stroke-width="2"/><path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/><circle cx="10" cy="18" r="1" fill="currentColor"/><circle cx="14" cy="18" r="1" fill="currentColor"/><path d="M10 10v4M14 10v4" stroke="currentColor" stroke-width="1.5"/></svg>', description: 'Valise de taille cabine, généralement jusqu\'à 55x35x25 cm.' },
        'Bagage soute': { type: 'hold', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="5" y="6" width="14" height="12" rx="1" stroke="currentColor" stroke-width="2"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/><path d="M5 10h14" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="15" r="1" fill="currentColor"/><circle cx="15" cy="15" r="1" fill="currentColor"/></svg>', description: 'Grande valise enregistrée en soute.' },
        'Bagage spécial': { type: 'special', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="4" y="7" width="16" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M8 17h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>', description: 'Objets volumineux ou hors format comme un équipement de sport ou un instrument de musique.' },
        'Vestiaire': { type: 'cloakroom', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path d="M16 10V8a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2" stroke="currentColor" stroke-width="2"/><path d="M8 10h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="2"/><path d="M8 10v-2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="1.5"/></svg>', description: 'Pour les manteaux, vestes ou autres vêtements sur cintre.' }
    };

    document.addEventListener('DOMContentLoaded', function() {
        loadStateFromSession(); // Load state on page load

        // --- EVENT LISTENERS ---
        document.getElementById('back-to-step-1-btn').addEventListener('click', function() {
            document.getElementById('baggage-selection-step').style.display = 'none';
            document.getElementById('step-1').style.display = 'block';
            this.classList.add('hidden'); // Hide button
            saveStateToSession();
        });

        document.getElementById('airport-select').addEventListener('change', function() { 
            airportId = this.value; 
            saveStateToSession();
        });
        document.getElementById('check-availability-btn').addEventListener('click', checkAvailability);
        
        document.getElementById('baggage-grid-container').addEventListener('click', handleQuantityChange);

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
            const target = e.target.closest('.info-icon');
            if (!target) return;

            const libelle = target.dataset.libelle;
            const productData = productMapJs[libelle];
            
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
            const target = e.target.closest('.info-icon'); // New target
            if (target) {
                tooltip.classList.add('hidden');
            }
        });

        // --- MODAL LISTENERS ---
        const modalOverlay = document.getElementById('custom-modal-overlay');
        const modalCloseBtn = document.getElementById('custom-modal-close');
        const modalConfirmBtn = document.getElementById('custom-modal-confirm-btn');
        const modalCancelBtn = document.getElementById('custom-modal-cancel-btn');
        const promptContainer = document.getElementById('custom-modal-prompt-container');
        const promptInput = document.getElementById('custom-modal-input');
        const modalError = document.getElementById('custom-modal-error');

        modalCloseBtn.addEventListener('click', () => {
            closeModal();
            if (modalResolve) modalResolve(null);
        });

        modalCancelBtn.addEventListener('click', () => {
            closeModal();
            if (modalResolve) modalResolve(null);
        });

        modalConfirmBtn.addEventListener('click', () => {
            const isPrompt = !promptContainer.classList.contains('hidden');
            if (isPrompt) {
                const value = promptInput.value;
                if (value.trim() === '' || !/^\S+@\S+\.\S+$/.test(value)) {
                    modalError.textContent = 'Veuillez entrer une adresse e-mail valide.';
                    modalError.classList.remove('hidden');
                    return;
                }
                closeModal();
                if (modalResolve) modalResolve(value);
            } else {
                closeModal();
                if (modalResolve) modalResolve(true);
            }
        });

        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                closeModal();
                if (modalResolve) modalResolve(null);
            }
        });
        // --- END MODAL LISTENERS ---
    });

    

    function displaySelectedDates() {
        const options = { month: 'short', day: 'numeric' };
        const depotDate = new Date(document.getElementById('date-depot').value).toLocaleDateString('fr-FR', options);
        const recupDate = new Date(document.getElementById('date-recuperation').value).toLocaleDateString('fr-FR', options);
        const depotHeure = document.getElementById('heure-depot').value;
        const recupHeure = document.getElementById('heure-recuperation').value;

        document.getElementById('display-date-depot').textContent = `${depotDate}, ${depotHeure}`;
        document.getElementById('display-date-recuperation').textContent = `${recupDate}, ${recupHeure}`;
    }

    function handleQuantityChange(e) {
        const target = e.target.closest('.quantity-change-btn');
        if (!target) return;

        const cartSpinner = document.getElementById('loading-spinner-cart');
        if(cartSpinner) cartSpinner.style.display = 'inline-block';

        // Simulate a small delay to make the spinner visible
        setTimeout(() => {
            const action = target.dataset.action;
            const productId = target.dataset.productId;
            
            const product = initialProducts.find(p => p.id == productId);
            if (!product) {
                if(cartSpinner) cartSpinner.style.display = 'none';
                return;
            }

            let itemInCart = cartItems.find(item => item.productId === productId && item.itemCategory === 'baggage');

            if (action === 'plus') {
                if (itemInCart) {
                    itemInCart.quantity++;
                } else {
                    const mapData = productMapJs[product.libelle];
                    const type = mapData ? mapData.type : 'unknown';
                    cartItems.push({
                        itemCategory: 'baggage',
                        productId: productId,
                        libelle: product.libelle,
                        type: type,
                        quantity: 1
                    });
                }
            } else if (action === 'minus') {
                if (itemInCart) {
                    itemInCart.quantity--;
                    if (itemInCart.quantity <= 0) {
                        cartItems = cartItems.filter(item => item.productId !== productId || item.itemCategory !== 'baggage');
                    }
                }
            }
            updateCartDisplay();
        }, 1000); // 1000ms delay
    }

    function saveStateToSession() {
        const state = {
            airportId: document.getElementById('airport-select').value,
            dateDepot: document.getElementById('date-depot').value,
            heureDepot: document.getElementById('heure-depot').value,
            dateRecuperation: document.getElementById('date-recuperation').value,
            heureRecuperation: document.getElementById('heure-recuperation').value,
            isBaggageStepVisible: document.getElementById('baggage-selection-step').style.display === 'block',
            cartItems: cartItems,
            globalProductsData: globalProductsData,
            globalLieuxData: globalLieuxData,
            guestEmail: guestEmail
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
        
        globalProductsData = state.globalProductsData || [];
        globalLieuxData = state.globalLieuxData || [];
        cartItems = state.cartItems || [];
        guestEmail = state.guestEmail || null; // Add this line

        if (state.isBaggageStepVisible) {
            document.getElementById('step-1').style.display = 'none';
            document.getElementById('baggage-selection-step').style.display = 'block';
            document.getElementById('back-to-step-1-btn').classList.remove('hidden'); // Show button
            displaySelectedDates();
            
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
        const btn = document.getElementById('check-availability-btn');
        spinner.style.display = 'inline-block';
        btn.disabled = true;

        const dateDepot = document.getElementById('date-depot').value;
        const heureDepot = document.getElementById('heure-depot').value;

        if (!airportId || !dateDepot || !heureDepot) {
            await showCustomAlert('Champs incomplets', 'Veuillez remplir tous les champs : aéroport, date et heure de dépôt.');
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
                document.getElementById('back-to-step-1-btn').classList.remove('hidden'); // Show button
                displaySelectedDates();
                getQuoteAndDisplay();
            } else {
                await showCustomAlert('Indisponible', result.message || 'La plateforme est fermée à la date de dépôt sélectionnée.');
            }
        } catch (error) {
            console.error('Erreur lors de la vérification de disponibilité:', error);
            await showCustomAlert('Erreur', 'Une erreur technique est survenue lors de la vérification de la disponibilité.');
        } finally {
            spinner.style.display = 'none';
            btn.disabled = false;
        }
    }

    
    
    async function getQuoteAndDisplay() {
        const cartSpinner = document.getElementById('loading-spinner-cart');
        if(cartSpinner) cartSpinner.style.display = 'inline-block';

        const dateDepot = document.getElementById('date-depot').value;
        const heureDepot = document.getElementById('heure-depot').value;
        const dateRecuperation = document.getElementById('date-recuperation').value;
        const heureRecuperation = document.getElementById('heure-recuperation').value;

        if (!dateDepot || !heureDepot || !dateRecuperation || !heureRecuperation) {
            await showCustomAlert('Attention', 'Veuillez vérifier les dates et heures de dépôt et de récupération.');
            if(cartSpinner) cartSpinner.style.display = 'none';
            return;
        }

        const debut = new Date(`${dateDepot}T${heureDepot}:00`);
        const fin = new Date(`${dateRecuperation}T${heureRecuperation}:00`);
        const dureeEnMinutes = Math.ceil(Math.abs(fin - debut) / (1000 * 60));

        if (dureeEnMinutes <= 0) {
            await showCustomAlert('Attention', 'La date de récupération doit être postérieure à la date de dépôt.');
            if(cartSpinner) cartSpinner.style.display = 'none';
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
            if(cartSpinner) cartSpinner.style.display = 'none';
        }
    }

    function displayOptions(dureeEnMinutes) {
        const dureeEnHeures = dureeEnMinutes / 60;
        isPriorityAvailable = dureeEnHeures < 72;
        isPremiumAvailable = globalLieuxData.length > 0;
    }

    function updateAdvertModalButtons() {
        ['priority', 'premium'].forEach(optionKey => {
            const addButton = document.getElementById(`add-${optionKey}-from-modal`);
            if (!addButton) return;

            const isInCart = cartItems.some(item => item.key === optionKey);
            addButton.disabled = false; // Always enable the button for toggling

            if (isInCart) {
                addButton.textContent = 'Enlever du panier';
                addButton.classList.remove('bg-yellow-custom', 'text-gray-dark', 'bg-purple-600', 'text-white');
                addButton.classList.add('bg-red-600', 'text-white');
            } else {
                addButton.textContent = 'Ajouter au panier';
                addButton.classList.remove('bg-red-600', 'text-white');
                if (optionKey === 'priority') {
                    addButton.classList.add('bg-yellow-custom', 'text-gray-dark');
                } else {
                    addButton.classList.add('bg-purple-600', 'text-white');
                }
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
            let infoComplementaires = '';
            let commentaire = '';
            let lieuId = null;

            const modalContent = document.getElementById(`advert-option-${optionKey}`);
            if (optionKey === 'premium') {
                const select = modalContent.querySelector('select[name^="option_lieu_"]');
                if(select) lieuId = select.value;
            }
            const infoInput = modalContent.querySelector('input[name^="option_info_"]');
            const commentTextarea = modalContent.querySelector('textarea[name^="option_comment_"]');
            if (infoInput) infoComplementaires = infoInput.value;
            if (commentTextarea) commentaire = commentTextarea.value;

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
        }
        
        updateCartDisplay();
        updateAdvertModalButtons(); // Update button states after toggle
    }

    function showOptionsAdvertisementModal() {
        return new Promise(resolve => {
            const modal = document.getElementById('options-advert-modal');
            const closeBtn = document.getElementById('close-options-advert-modal');
            const prioritySection = document.getElementById('advert-option-priority');
            const premiumSection = document.getElementById('advert-option-premium');
            const addPriorityBtn = document.getElementById('add-priority-from-modal');
            const addPremiumBtn = document.getElementById('add-premium-from-modal');
            const continueBtn = document.getElementById('continue-from-options-modal');

            // Reset visibility
            prioritySection.classList.add('hidden');
            premiumSection.classList.add('hidden');
            
            if (isPriorityAvailable) {
                prioritySection.classList.remove('hidden');
            }
            if (isPremiumAvailable) {
                premiumSection.classList.remove('hidden');
                const premiumDetailsContainer = document.getElementById('premium-details-modal');
                const lieuxOptionsHTML = globalLieuxData.map(lieu => `<option value="${lieu.id}">${lieu.libelle}</option>`).join('');
                premiumDetailsContainer.innerHTML = `
                    <label class="block text-sm font-medium">Lieu de rendez-vous *</label>
                    <select name="option_lieu_opt_premium" class="input-style custom-select w-full">${lieuxOptionsHTML}</select>
                    <label class="block text-sm font-medium">Informations complémentaires</label>
                    <input type="text" name="option_info_opt_premium" class="input-style w-full" placeholder="Ex: N° de vol, provenance...">
                    <label class="block text-sm font-medium">Commentaire</label>
                    <textarea name="option_comment_opt_premium" class="input-style w-full" rows="2" placeholder="Ajoutez un commentaire..."></textarea>
                `;
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
        const heureDepot = document.getElementById('heure-depot').value;
        const dateRecuperation = document.getElementById('date-recuperation').value;
        const heureRecuperation = document.getElementById('heure-recuperation').value;
        
        let duration_display = '';
        if (dateDepot && heureDepot && dateRecuperation && heureRecuperation) {
            const start = new Date(`${dateDepot}T${heureDepot}`);
            const end = new Date(`${dateRecuperation}T${heureRecuperation}`);
            const duration_in_minutes = Math.round((end - start) / (1000 * 60));

            if (duration_in_minutes > 0) {
                if (duration_in_minutes < 1440) { // Moins d'un jour (1440 minutes = 24 heures)
                    const hours = Math.floor(duration_in_minutes / 60);
                    const minutes = duration_in_minutes % 60;
                    duration_display = hours + ' heure(s)';
                    if (minutes > 0) {
                        duration_display += ' et ' + minutes + ' minute(s)';
                    }
                } else { // Un jour ou plus
                    const days = Math.floor(duration_in_minutes / 1440);
                    const remaining_hours = Math.floor((duration_in_minutes % 1440) / 60);
                    duration_display = days + ' jour(s)';
                    if (remaining_hours > 0) {
                        duration_display += ' et ' + remaining_hours + ' heure(s)';
                    }
                }
            }
        }
        durationElement.textContent = duration_display;

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

        // Update quantity displays in the baggage list
        document.querySelectorAll('[data-quantity-display]').forEach(span => {
            const productId = span.dataset.quantityDisplay;
            const itemInCart = cartItems.find(item => item.productId === productId && item.itemCategory === 'baggage');
            span.textContent = itemInCart ? itemInCart.quantity : '0';
        });

        // Add yellow highlight to selected baggage boxes
        document.querySelectorAll('#baggage-grid-container .baggage-option').forEach(box => {
            const productId = box.dataset.productId;
            const itemInCart = cartItems.find(item => item.productId === productId && item.itemCategory === 'baggage');
            if (itemInCart && itemInCart.quantity > 0) {
                box.classList.add('selected');
            } else {
                box.classList.remove('selected');
            }
        });

        saveStateToSession(); // Save state after any cart update
        
        const cartSpinner = document.getElementById('loading-spinner-cart');
        if(cartSpinner) cartSpinner.style.display = 'none';
    }
    
    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    async function handleTotalClick() {
        const loader = document.getElementById('loader');
        if (loader) loader.classList.remove('hidden'); // Show loader

        if (cartItems.length === 0) {
            await showCustomAlert('Panier vide', "Votre panier est vide.");
            if (loader) loader.classList.add('hidden'); // Hide loader
            return;
        }

        // --- NEW: Show options modal first if any options are available ---
        if (isPriorityAvailable || isPremiumAvailable) {
            // Loader is already visible, keep it that way for options modal.
            // showOptionsAdvertisementModal will handle hiding loader if user just closes it.
            const result = await showOptionsAdvertisementModal(); // This promise resolves when modal is dismissed
            if (result === 'cancelled') { // User explicitly chose to cancel or closed the modal
                if (loader) loader.classList.add('hidden');
                return;
            }
        }
        // --- END NEW ---

        try {
            const authResponse = await fetch('/check-auth-status');
            const authData = await authResponse.json();
            
            if (!authData.authenticated) {
                if (!guestEmail) {
                    await sleep(300); // Shorter delay after options modal, if applicable
                    if (loader) loader.classList.add('hidden'); // Hide loader before showing auth prompt

                    const choice = await showLoginOrGuestPrompt();

                    if (choice === 'login') {
                        if (window.openLoginModal) {
                            window.openLoginModal();
                        } else {
                            console.error('Global openLoginModal function not found.');
                            await showCustomAlert('Erreur', 'Impossible d\'ouvrir la fenêtre de connexion.');
                        }
                        // Stop the process here; the global login modal will handle the rest.
                        if (loader) loader.classList.add('hidden');
                        return;
                    } else if (choice === 'guest') {
                        // Do NOT show loader here, as user needs to interact with the prompt
                        const email = await showCustomPrompt(
                            'Comment pouvons-nous vous joindre ?',
                            'C’est sur ce mail que vous recevrez la confirmation de réservation.',
                            'Adresse e-mail'
                        );
                        
                        if (email) {
                            guestEmail = email;
                            saveStateToSession();
                            if (loader) loader.classList.remove('hidden'); // Show loader AFTER email is provided
                        } else {
                            // User cancelled the prompt, so hide loader and return
                            if (loader) loader.classList.add('hidden');
                            return;
                        }
                    } else { // User cancelled the initial choice prompt
                        if (loader) loader.classList.add('hidden');
                        return;
                    }
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
                await showCustomAlert('Erreur', resultData.message || 'Une erreur inconnue est survenue lors de la préparation du paiement.');
            }
        } catch (error) {
            console.error('Erreur critique dans handleTotalClick:', error);
            await showCustomAlert('Erreur', 'Une erreur technique est survenue.');
        } finally {
            if (loader) loader.classList.add('hidden'); // Ensure loader is hidden
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