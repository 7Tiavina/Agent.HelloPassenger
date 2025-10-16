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

    /* Styles pour le spinner */
    .custom-spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-left-color: #FFC107; /* Couleur du spinner, jaune comme le bouton */
        border-radius: 50%;
        width: 1.5em;
        height: 1.5em;
        animation: spin 1s linear infinite;
        display: inline-block; /* Pour qu'il soit visible */
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
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="space-y-4">
                        <div class="baggage-block">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    QUEL EST LE TYPE DE BAGAGE CONSIGNÉ ? *
                                </label>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mt-3">
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
                                            // Utilise le mappage s'il existe, sinon crée une configuration par défaut
                                            $map_data = $product_map[$libelle] ?? [
                                                'type' => Illuminate\Support\Str::slug($libelle),
                                                'icon' => $default_icon
                                            ];
                                        @endphp
                                        <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="{{ $map_data['type'] }}" data-product-id="{{ $product['id'] }}">
                                            <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                                {!! $map_data['icon'] !!}
                                            </div>
                                            <span class="text-sm font-medium text-center">{{ $libelle }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-5 text-center text-gray-500">Aucun type de bagage n'est disponible pour le moment.</p>
                                @endif
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

                <div class="mt-8 text-center">
                    <button id="get-quote-btn" class="bg-yellow-custom text-gray-dark font-bold py-3 px-8 rounded-full btn-hover">
                        INTERROGER LES TARIFS
                        <span class="custom-spinner" role="status" aria-hidden="true" id="loading-spinner-tarifs" style="display: none;"></span>
                    </button>
                </div>
            </div>

            <!-- Étape 3: Options additionnelles (initialement masquée) -->
            <div id="options-step" class="hidden mt-6">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Souhaitez-vous bénéficier de services additionnels ?</h3>
                    <div id="options-container" class="space-y-4">
                        <!-- Les options (Priority, Premium) seront injectées ici par JavaScript -->
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
                    <div class="bg-yellow-custom rounded p-3 mt-4 flex justify-between items-center summary-total-container">
                        <p class="text-lg font-bold text-gray-dark">Total:</p>
                        <p class="text-2xl font-bold text-gray-dark total-panier">0€</p>
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

    // Options sont maintenant statiques, comme demandé.
    const staticOptions = {
        priority: { id: 'opt_priority', libelle: 'Service Priority', prixUnitaire: 15 },
        premium: { id: 'opt_premium', libelle: 'Service Premium', prixUnitaire: 25 }
    };

    // Mappage des libellés aux icônes et types pour JavaScript
    const productMapJs = {
        'Accessoires': { type: 'accessory', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path d="M12 14a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="2"/><path d="M17.94 6.06a8 8 0 00-11.88 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>' },
        'Bagage cabine': { type: 'cabin', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="6" y="8" width="12" height="10" rx="1" stroke="currentColor" stroke-width="2"/><path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/><circle cx="10" cy="18" r="1" fill="currentColor"/><circle cx="14" cy="18" r="1" fill="currentColor"/><path d="M10 10v4M14 10v4" stroke="currentColor" stroke-width="1.5"/></svg>' },
        'Bagage soute': { type: 'hold', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="5" y="6" width="14" height="12" rx="1" stroke="currentColor" stroke-width="2"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/><path d="M5 10h14" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="15" r="1" fill="currentColor"/><circle cx="15" cy="15" r="1" fill="currentColor"/></svg>' },
        'Bagage spécial': { type: 'special', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><rect x="4" y="7" width="16" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M8 17h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>' },
        'Vestiaire': { type: 'cloakroom', icon: '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path d="M16 10V8a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2" stroke="currentColor" stroke-width="2"/><path d="M8 10h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="2"/><path d="M8 10v-2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="1.5"/></svg>' }
    };
    const defaultIconJs = '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600"><path stroke="currentColor" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /><path stroke="currentColor" stroke-width="2" d="M9.5 9.5h.01v.01h-.01V9.5zm5 0h.01v.01h-.01V9.5zm-2.5 5a2.5 2.5 0 00-5 0h5z" /></svg>';

    document.addEventListener('DOMContentLoaded', function() {
        // --- EVENT LISTENERS ---
        document.getElementById('airport-select').addEventListener('change', function() { airportId = this.value; });
        document.getElementById('check-availability-btn').addEventListener('click', checkAvailability);
        document.getElementById('get-quote-btn').addEventListener('click', getQuoteAndLieux);
        document.getElementById('add-baggage-type').addEventListener('click', addBaggageType);

        document.addEventListener('click', function(e) {
            if (e.target.closest('.baggage-option')) {
                const block = e.target.closest('.baggage-block');
                block.querySelectorAll('.baggage-option').forEach(el => el.classList.remove('selected'));
                e.target.closest('.baggage-option').classList.add('selected');
            }
            if (e.target.classList.contains('btn-plus')) { e.target.closest('.flex').querySelector('input').value++; }
            if (e.target.classList.contains('btn-minus') && e.target.closest('.flex').querySelector('input').value > 1) { e.target.closest('.flex').querySelector('input').value--; }
            if (e.target.closest('.remove-baggage-btn')) { e.target.closest('.baggage-block').remove(); }
        });
    });

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

    async function getQuoteAndLieux() {
        const spinner = document.getElementById('loading-spinner-tarifs');
        const btn = this;
        spinner.style.display = 'inline-block';
        btn.disabled = true;

        const dateDepot = document.getElementById('date-depot').value;
        const heureDepot = document.getElementById('heure-depot').value;
        const dateRecuperation = document.getElementById('date-recuperation').value;
        const heureRecuperation = document.getElementById('heure-recuperation').value;

        if (!dateDepot || !heureDepot || !dateRecuperation || !heureRecuperation) {
            alert('Veuillez remplir les dates et heures de dépôt et de récupération.');
            spinner.style.display = 'none';
            btn.disabled = false;
            return;
        }

        const debut = new Date(`${dateDepot}T${heureDepot}:00`);
        const fin = new Date(`${dateRecuperation}T${heureRecuperation}:00`);
        const dureeEnMinutes = Math.ceil(Math.abs(fin - debut) / (1000 * 60));

        if (dureeEnMinutes <= 0) {
            alert('La date de récupération doit être postérieure à la date de dépôt.');
            spinner.style.display = 'none';
            btn.disabled = false;
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
                recalculateCart();
            } else {
                alert('Erreur lors de la récupération des tarifs : ' + (result.message || 'Réponse invalide'));
            }
        } catch (error) {
            console.error('Erreur lors de la récupération des tarifs et lieux:', error);
            alert('Une erreur technique est survenue.');
        } finally {
            spinner.style.display = 'none';
            btn.disabled = false;
        }
    }

    function displayOptions(dureeEnMinutes) {
        const optionsContainer = document.getElementById('options-container');
        optionsContainer.innerHTML = '';
        const dureeEnHeures = dureeEnMinutes / 60;

        // Priority Option
        if (dureeEnHeures < 72) {
            const option = staticOptions.priority;
            const optionHtml = `
                <div class="border rounded-2xl p-5 hover:shadow-md transition bg-white">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <input type="checkbox" id="${option.id}" data-option-key="priority" class="option-checkbox mr-3 h-5 w-5 text-yellow-custom border-gray-300 focus:ring-yellow-custom">
                            <span class="text-lg font-medium">${option.libelle}</span>
                        </div>
                        <span class="text-yellow-custom font-semibold">+${option.prixUnitaire} €</span>
                    </label>
                    <p class="text-gray-500 text-sm mt-2">Bénéficiez d’un traitement prioritaire pour vos bagages.</p>
                    <div id="details-${option.id}" class="hidden mt-4 border-t pt-4 space-y-3">
                        <label class="block text-sm font-medium">Lieu</label>
                        <select class="mt-1 w-full border rounded-lg px-3 py-2 focus:ring-yellow-custom focus:border-yellow-custom"></select>
                    </div>
                </div>`;
            optionsContainer.insertAdjacentHTML('beforeend', optionHtml);
        }

        // Premium Option
        if (globalLieuxData.length > 0) {
            const option = staticOptions.premium;
            const lieuxOptions = globalLieuxData.map(lieu => `<option value="${lieu.id}">${lieu.libelle}</option>`).join('');
            const optionHtml = `
                <div class="border rounded-2xl p-5 hover:shadow-md transition bg-white">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <input type="checkbox" id="${option.id}" data-option-key="premium" class="option-checkbox mr-3 h-5 w-5 text-yellow-custom border-gray-300 focus:ring-yellow-custom">
                            <span class="text-lg font-medium">${option.libelle}</span>
                        </div>
                        <span class="text-yellow-custom font-semibold">+${option.prixUnitaire} €</span>
                    </label>
                    <p class="text-gray-500 text-sm mt-2">Accédez à un service complet de gestion de vos bagages de bout en bout.</p>
                    <div id="details-${option.id}" class="hidden mt-4 border-t pt-4 space-y-3">
                        <label class="block text-sm font-medium">Lieu de rendez-vous *</label>
                        <select name="option_lieu_${option.id}" class="mt-1 w-full border rounded-lg px-3 py-2 focus:ring-yellow-custom focus:border-yellow-custom">${lieuxOptions}</select>
                    </div>
                </div>`;
            optionsContainer.insertAdjacentHTML('beforeend', optionHtml);
        }

        if (optionsContainer.innerHTML !== '') {
            document.getElementById('options-step').style.display = 'block';
            optionsContainer.querySelectorAll('.option-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', (e) => {
                    document.getElementById(`details-${e.target.id}`).classList.toggle('hidden', !e.target.checked);
                    recalculateCart();
                });
            });
        }
    }

    function recalculateCart() {
        let total = 0;
        let cartContent = '';

        document.querySelectorAll('.baggage-block').forEach(block => {
            const selectedBaggage = block.querySelector('.baggage-option.selected');
            if (selectedBaggage) {
                const quantity = parseInt(block.querySelector('input[type="text"]').value) || 0;
                const productId = selectedBaggage.dataset.productId;
                const product = globalProductsData.find(p => p.id == productId);
                if (product && quantity > 0) {
                    const itemTotal = product.prixUnitaire * quantity;
                    total += itemTotal;
                    cartContent += `<div class="flex justify-between items-center mb-2"><span>${quantity} x ${product.libelle}</span><span>${itemTotal.toFixed(2)} €</span></div>`;
                }
            }
        });

        document.querySelectorAll('.option-checkbox:checked').forEach(checkbox => {
            const optionKey = checkbox.dataset.optionKey;
            const option = staticOptions[optionKey];
            if (option) {
                total += option.prixUnitaire;
                cartContent += `<div class="flex justify-between items-center mb-2 text-sm text-gray-600"><span>+ ${option.libelle}</span><span>${option.prixUnitaire.toFixed(2)} €</span></div>`;
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
            document.querySelector('.summary-total-container').onclick = handleTotalClick;
        } else {
            cartElement.style.display = 'none';
            emptyCartElement.style.display = 'block';
            summaryPriceElement.textContent = `0 €`;
        }
    }
    
    async function handleTotalClick() {
        try {
            const authResponse = await fetch('/check-auth-status');
            const authData = await authResponse.json();

            if (authData.authenticated) {
                const baggages = [];
                document.querySelectorAll('.baggage-block').forEach(block => {
                    const selectedBaggage = block.querySelector('.baggage-option.selected');
                    if (selectedBaggage) {
                        const quantity = parseInt(block.querySelector('input[type="text"]').value) || 0;
                        if (quantity > 0) { baggages.push({ type: selectedBaggage.dataset.type, quantity: quantity }); }
                    }
                });

                const options = [];
                document.querySelectorAll('.option-checkbox:checked').forEach(checkbox => {
                    const optionKey = checkbox.dataset.optionKey;
                    const option = staticOptions[optionKey];
                    let lieuId = null;
                    if (optionKey === 'premium') {
                        const detailsDiv = document.getElementById(`details-${option.id}`);
                        lieuId = detailsDiv.querySelector('select').value;
                    }
                    options.push({ id: option.id, lieu_id: lieuId });
                });

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
            } else {
                if (typeof window.openLoginModal === 'function') { window.openLoginModal(); } else { window.location.href = '/client/login'; }
            }
        } catch (error) {
            console.error('Erreur critique dans handleTotalClick:', error);
            alert('Une erreur technique est survenue.');
        }
    }

    function addBaggageType() {
        const container = document.getElementById('additional-baggages-container');
        const newBaggageBlock = document.createElement('div');
        newBaggageBlock.classList.add('baggage-block', 'bg-white', 'border', 'border-gray-200', 'rounded-lg', 'p-6', 'mt-4');
        newBaggageBlock.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-medium text-gray-700">TYPE DE BAGAGE SUPPLÉMENTAIRE *</label>
                <button type="button" class="remove-baggage-btn w-6 h-6 border rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mt-3">
                ${initialProducts.map(product => {
                    const map_data = productMapJs[product.libelle] || { type: product.libelle.toLowerCase().replace(/ /g, '-'), icon: defaultIconJs };
                    return `
                        <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="${map_data.type}" data-product-id="${product.id}">
                            <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">${map_data.icon}</div>
                            <span class="text-sm font-medium text-center">${product.libelle}</span>
                        </div>`;
                }).join('')}
            </div>
            <div class="mt-3">
                <label class="block text-sm text-gray-600 mb-2">COMBIEN ? *</label>
                <div class="flex items-center space-x-2">
                    <button type="button" class="w-8 h-8 border rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-minus">−</button>
                    <input type="text" class="input-style w-16 text-center" value="1" readonly />
                    <button type="button" class="w-8 h-8 border rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-plus">+</button>
                </div>
            </div>
        `;
        container.appendChild(newBaggageBlock);
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