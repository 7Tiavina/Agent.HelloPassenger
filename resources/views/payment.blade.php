@php
    $commandeData = Session::get('commande_en_cours');
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon-hellopassenger.png') }}">
    <title>Finaliser le Paiement - HelloPassenger</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Intl Tel Input CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
    
    <!-- Scripts Monetico -->
    <script
        src="https://api.gateway.monetico-retail.com/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
        kr-public-key="43559169:testpublickey_TpUnzWl3wta3iKfuUeeYylRCWZ99SwdFKQktpbbxaOdxz"
        kr-post-url-success="{{ route('payment.success') }}"
        kr-post-url-refused="{{ route('payment.error') }}"
        kr-post-url-canceled="{{ route('payment.cancel') }}">
    </script>
    <link rel="stylesheet" href="https://api.gateway.monetico-retail.com/static/js/krypton-client/V4.0/ext/neon-reset.min.css">
    <script src="https://api.gateway.monetico-retail.com/static/js/krypton-client/V4.0/ext/neon.js"></script>
    
    <!-- Google Places API - Chargement conditionnel -->
    @php
        $googlePlacesApiKey = config('services.google.places_api_key');
        $isProduction = app()->environment('production');
    @endphp
    
    <style>
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
        
        .input-error {
            border: 2px solid #ef4444 !important;
        }
        
        .custom-spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: #FFC107;
            border-radius: 50%;
            width: 1.5em;
            height: 1.5em;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Styles pour intl-tel-input */
        .iti {
            width: 100%;
        }
        .iti__tel-input {
            width: 100% !important;
        }
        
        /* Cache busting pour les fichiers statiques */
        .cache-bust {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Cache busting parameter -->
    <div class="cache-bust" data-version="{{ config('app.version', '1.0.0') }}"></div>

    <!-- Loader Overlay -->
    <div id="loader" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center">
        <div class="custom-spinner !w-12 !h-12 !border-4" style="margin-left: 0;"></div>
    </div>
    
    <!-- Custom Modal -->
    <div id="custom-modal-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center px-4">
        <div id="custom-modal" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md transform transition-all" onclick="event.stopPropagation();">
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <h3 id="custom-modal-title" class="text-xl font-bold text-gray-800"></h3>
                <button id="custom-modal-close" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="py-4">
                <p id="custom-modal-message" class="text-gray-600"></p>
                <div id="custom-modal-prompt-container" class="hidden mt-4">
                    <label id="custom-modal-prompt-label" for="custom-modal-input" class="block text-sm font-medium text-gray-700 mb-1"></label>
                    <input type="text" id="custom-modal-input" class="input-style w-full">
                    <p id="custom-modal-error" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>
            </div>
            <div id="custom-modal-footer" class="flex justify-end pt-3 border-t border-gray-200 space-x-3">
                <button id="custom-modal-cancel-btn" class="hidden bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-full btn-hover">Annuler</button>
                <button id="custom-modal-confirm-btn" class="bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover">OK</button>
            </div>
        </div>
    </div>

    @include('Front.header-front')

    <div class="container mx-auto max-w-5xl my-12 px-4">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('form-consigne') }}" class="bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Retour au formulaire
            </a>
            <button id="payment-reset-btn" class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center space-x-1 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Réinitialiser et recommencer</span>
            </button>
        </div>

        <!-- Afficheur de message d'erreur -->
        @if(!$isProfileComplete)
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="status">
                <p class="font-bold">Votre sécurité est notre priorité</p>
                <p>Afin de garantir la protection de vos effets personnels et de respecter les normes de sécurité par rayons X, merci de compléter les informations manquantes.</p>
            </div>
        @endif

        @if ($commandeData)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Colonne de gauche : Récapitulatif de la commande -->
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Récapitulatif de votre commande</h1>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        @php
                            // --- Duration Calculation ---
                            $duration_in_minutes = $commandeData['duration_in_minutes'] ?? 0;
                            $duration_display = '';
                            if ($duration_in_minutes > 0) {
                                if ($duration_in_minutes < 1440) {
                                    $hours = floor($duration_in_minutes / 60);
                                    $minutes = $duration_in_minutes % 60;
                                    $duration_display = $hours . ' heure(s)';
                                    if ($minutes > 0) {
                                        $duration_display .= ' et ' . $minutes . ' minute(s)';
                                    }
                                } else {
                                    $days = floor($duration_in_minutes / 1440);
                                    $remaining_hours = floor(($duration_in_minutes % 1440) / 60);
                                    $duration_display = $days . ' jour(s)';
                                    if ($remaining_hours > 0) {
                                        $duration_display .= ' et ' . $remaining_hours . ' heure(s)';
                                    }
                                }
                            }

                            // --- Date Extraction ---
                            $firstLigne = $commandeData['commandeLignes'][0] ?? null;
                            $dateDebut = null;
                            $dateFin = null;
                            if ($firstLigne) {
                                try {
                                    $dateDebut = \Carbon\Carbon::parse($firstLigne['dateDebut']);
                                    $dateFin = \Carbon\Carbon::parse($firstLigne['dateFin']);
                                } catch (\Exception $e) {
                                    // In case of parsing error
                                }
                            }
                        @endphp

                        @if($duration_display || ($dateDebut && $dateFin))
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <div class="space-y-2">
                                <div class="flex items-center text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 mr-2 text-yellow-custom">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                    <p class="text-base"><strong>Service :</strong> <span class="font-bold text-gray-900">Consigne de bagage</span></p>
                                </div>

                                @if(isset($commandeData['airportName']))
                                <div class="flex items-center text-gray-800 mt-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 mr-2 text-yellow-custom">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                    </svg>
                                    <p class="text-base"><strong>Aéroport :</strong> <span class="font-bold text-gray-900">{{ $commandeData['airportName'] }}</span></p>
                                </div>
                                @endif
                            </div>
                            
                            @if($duration_display)
                            <p class="font-semibold text-gray-700 mt-3">Durée totale</p>
                            <p class="text-lg font-bold text-gray-900">{{ $duration_display }}</p>
                            @endif
                            
                            @if($dateDebut && $dateFin)
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-custom" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-base"><strong>Du :</strong> <span class="font-bold text-gray-900">{{ $dateDebut->format('d/m/Y à H:i') }}</span></p>
                                </div>
                                <div class="flex items-center text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-custom" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-base"><strong>Au :</strong> <span class="font-bold text-gray-900">{{ $dateFin->format('d/m/Y à H:i') }}</span></p>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                        
                        <ul class="divide-y divide-gray-200">
                            @foreach($commandeData['commandeLignes'] as $ligne)
                                <li class="py-4 flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $ligne['libelleProduit'] }}</p>
                                        <p class="text-sm text-gray-500">Quantité : {{ $ligne['quantite'] }}</p>
                                    </div>
                                    <p class="font-semibold text-gray-800">{{ number_format($ligne['prixTTC'], 2, ',', ' ') }} €</p>
                                </li>
                            @endforeach
                        </ul>
                        <div class="py-4 flex justify-between items-center border-t-2 border-gray-200 mt-4">
                            <p class="text-lg font-bold text-gray-900">Total à payer</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($commandeData['total_prix_ttc'], 2, ',', ' ') }} €</p>
                        </div>
                    </div>
                </div>

                <!-- Colonne de droite : Informations et Paiement -->
                <div class="space-y-8">
                    <!-- Bloc d'informations client -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 text-center">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">Vos informations</h2>
                        <div class="text-sm text-gray-600 space-y-2 text-left mx-auto max-w-sm">
                            <p id="display-user-name"><strong>Nom:</strong> {{ $user->prenom ?? 'Non renseigné' }} {{ $user->nom ?? 'Non renseigné' }}</p>
                            <p id="display-user-email"><strong>Email:</strong> {{ $user->email }}</p>
                            <p id="display-user-phone"><strong>Téléphone:</strong> {{ $user->telephone ?? 'Non renseigné' }}</p>
                            <p id="display-user-address"><strong>Adresse:</strong> {{ $user->adresse ?? 'Non renseignée' }}</p>
                            <p id="display-user-company"><strong>Société:</strong> {{ $user->nomSociete ?? 'Non renseignée' }}</p>
                            <p id="display-user-city"><strong>Ville:</strong> {{ $user->ville ?? 'Non renseignée' }}</p>
                            <p id="display-user-zipcode"><strong>Code postal:</strong> {{ $user->codePostal ?? 'Non renseigné' }}</p>
                            <p id="display-user-country"><strong>Pays:</strong> {{ $user->pays ?? 'Non renseigné' }}</p>
                        </div>
                        <button id="openClientProfileModalBtn" class="mt-4 bg-yellow-custom text-gray-dark font-bold py-2 px-4 rounded-full btn-hover mx-auto">Modifier</button>
                    </div>

                    <!-- Bloc de paiement -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 text-center">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">Paiement sécurisé</h2>
                        @if($isProfileComplete)
                            <div class="kr-smart-form mx-auto" kr-form-token="{{ $formToken }}"></div>
                        @else
                            <div class="p-4 bg-gray-100 rounded-md text-center text-gray-600">
                                Veuillez compléter vos informations pour activer le paiement.
                            </div>
                        @endif
                    </div>

                    <!-- Bloc de débogage -->
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                         <details>
                            <summary class="text-sm text-gray-600 cursor-pointer">Aperçu des données de commande (JSON)</summary>
                            <pre class="bg-gray-800 text-white p-4 rounded-md text-xs overflow-x-auto mt-2">{{ json_encode($commandeData, JSON_PRETTY_PRINT) }}</pre>
                        </details>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative max-w-lg mx-auto" role="alert">
                <strong class="font-bold">Erreur!</strong>
                <span class="block sm:inline">Aucune donnée de commande trouvée. Votre session a peut-être expiré.</span>
                <a href="{{ route('form-consigne') }}" class="mt-4 inline-block bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Retour au formulaire
                </a>
            </div>
        @endif
    </div>

    @include('Front.footer-front')
    @include('components.client-profile-modal')

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    
    <!-- Google Places API - Script optimisé -->
    @if($googlePlacesApiKey)
    <script>
        // Version avec cache-busting
        const googleApiVersion = '{{ config('app.version', '1.0.0') }}';
        const googlePlacesApiKey = '{{ $googlePlacesApiKey }}';
        
        // Fonction pour charger Google Maps API avec rappel
        function loadGoogleMapsAPI(callback) {
            // Vérifier si l'API est déjà chargée
            if (window.google && window.google.maps && window.google.maps.places) {
                if (callback) callback();
                return;
            }
            
            // Créer l'élément script
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${googlePlacesApiKey}&libraries=places&language=fr&callback=initGooglePlaces&v=3.52&_=${googleApiVersion}`;
            script.async = true;
            script.defer = true;
            
            // Définir la fonction de rappel globale
            window.initGooglePlaces = function() {
                console.log('Google Places API loaded successfully');
                if (callback) callback();
            };
            
            // Gérer les erreurs de chargement
            script.onerror = function() {
                console.error('Failed to load Google Places API');
                const fallbackScript = document.createElement('script');
                fallbackScript.src = `https://maps.googleapis.com/maps/api/js?key=${googlePlacesApiKey}&libraries=places&language=fr&v=3.52`;
                fallbackScript.async = true;
                fallbackScript.defer = true;
                document.head.appendChild(fallbackScript);
                
                const checkGoogleAPI = setInterval(function() {
                    if (window.google && window.google.maps && window.google.maps.places) {
                        clearInterval(checkGoogleAPI);
                        if (callback) callback();
                    }
                }, 500);
            };
            
            document.head.appendChild(script);
        }
        
        // Initialiser l'autocomplétion une fois l'API chargée
        function initAutocomplete() {
            const addressInput = document.getElementById('modal-adresse');
            
            if (!addressInput) {
                console.error('Address input not found');
                return;
            }
            
            if (!window.google || !window.google.maps || !window.google.maps.places) {
                console.error('Google Maps API not available');
                return;
            }
            
            try {
                const autocomplete = new google.maps.places.Autocomplete(addressInput, {
                    types: ['address'],
                    componentRestrictions: { country: [] },
                    fields: ['address_components', 'geometry', 'name']
                });

                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    
                    if (!place.geometry) {
                        console.log("No geometry found for the selected place");
                        return;
                    }

                    let street_number = '';
                    let route = '';
                    let city = '';
                    let postal_code = '';

                    for (let i = 0; i < place.address_components.length; i++) {
                        const component = place.address_components[i];
                        const addressType = component.types[0];
                        
                        if (addressType === 'street_number') {
                            street_number = component.long_name;
                        } else if (addressType === 'route') {
                            route = component.long_name;
                        } else if (addressType === 'locality' || addressType === 'administrative_area_level_3') {
                            city = component.long_name;
                        } else if (addressType === 'postal_code') {
                            postal_code = component.long_name;
                        }
                    }
                    
                    let fullAddress = street_number + (route ? ' ' + route : '');
                    if (postal_code) {
                        fullAddress += ', ' + postal_code;
                    }
                    if (city) {
                        fullAddress += ' ' + city;
                    }
                    
                    document.getElementById('modal-adresse').value = fullAddress.trim();
                    
                    console.log('Complete address filled:', fullAddress);
                });
                
                console.log('Google Places Autocomplete initialized');
            } catch (error) {
                console.error('Error initializing Google Places Autocomplete:', error);
            }
        }
    </script>
    @else
    <script>
        console.warn('Google Places API key not configured');
    </script>
    @endif
    
    <script>
        // ========================================================================
        // SIMPLIFIED PHONE NUMBER NORMALIZATION - FOCUS ON RELIABILITY
        // ========================================================================
        
        function normalizePhoneNumber(value, countryData) {
            if (!value) return '';

            const raw = value.trim();
            const dialCode = String(countryData.dialCode || '');
            const countryCode = String(countryData.iso2 || '').toLowerCase();
            const countryCodeUpper = countryCode.toUpperCase();

            console.log('🔧 Input:', value, '| Country:', countryCode, '| DialCode:', dialCode);

            // Normaliser l'entrée (garder + et chiffres)
            let normalizedInput = raw;
            if (normalizedInput.startsWith('00')) {
                normalizedInput = '+' + normalizedInput.substring(2);
            }
            normalizedInput = normalizedInput.replace(/[\s\-\(\)\.]/g, '');
            if (normalizedInput.startsWith('+')) {
                normalizedInput = '+' + normalizedInput.substring(1).replace(/\D/g, '');
            } else {
                normalizedInput = normalizedInput.replace(/\D/g, '');
            }

            // Utiliser libphonenumber via intl-tel-input utils pour TOUS les pays
            if (window.intlTelInputUtils) {
                const candidates = [normalizedInput];
                if (!normalizedInput.startsWith('0') && !normalizedInput.startsWith('+')) {
                    candidates.push('0' + normalizedInput);
                }

                for (const candidate of candidates) {
                    const e164 = window.intlTelInputUtils.formatNumber(
                        candidate,
                        countryCodeUpper,
                        window.intlTelInputUtils.numberFormat.E164
                    );

                    if (e164) {
                        const isValid = window.intlTelInputUtils.isValidNumber(
                            e164,
                            countryCodeUpper
                        );

                        if (isValid) {
                            console.log('→ E164 via utils:', e164);
                            return e164;
                        }
                    }
                }

                // Si utils renvoie un format sans +, on tente de le corriger
                const e164Loose = window.intlTelInputUtils.formatNumber(
                    normalizedInput,
                    countryCodeUpper,
                    window.intlTelInputUtils.numberFormat.E164
                );

                if (e164Loose) {
                    let fixed = e164Loose;
                    if (!fixed.startsWith('+')) {
                        if (dialCode && fixed.startsWith(dialCode)) {
                            fixed = '+' + fixed;
                        } else {
                            fixed = '+' + dialCode + fixed;
                        }
                    }
                    console.log('→ E164 corrigé:', fixed);
                    return fixed;
                }
            }

            // Fallback générique si utils non disponible
            if (!normalizedInput) return '';

            if (normalizedInput.startsWith('+')) {
                console.log('→ Fallback +:', normalizedInput);
                return normalizedInput;
            }

            // Si ça commence déjà par le dialCode, on ajoute juste le +
            if (dialCode && normalizedInput.startsWith(dialCode) && normalizedInput.length > dialCode.length) {
                const withPlus = '+' + normalizedInput;
                console.log('→ Fallback dial code:', withPlus);
                return withPlus;
            }

            const fallback = '+' + dialCode + normalizedInput;
            console.log('→ Fallback concat:', fallback);
            return fallback;
        }

        function isLenientlyValidNumber(value, countryCodeUpper) {
            if (!value) return false;

            if (window.intlTelInputUtils) {
                if (window.intlTelInputUtils.isValidNumber(value, countryCodeUpper)) {
                    return true;
                }
            }

            const digits = value.replace(/\D/g, '');
            return digits.length >= 6 && digits.length <= 15;
        }

        function detectCountryFromNumber(phoneNumber, itiInstance) {
            if (!window.intlTelInputUtils || !itiInstance) return null;

            console.log('🔎 Attempting auto-detection for:', phoneNumber);

            // Essayer d'abord avec région vide (pour numéros avec code pays)
            try {
                const parsed = window.intlTelInputUtils.parseNumber(phoneNumber, '');
                if (parsed && parsed.getCountryCode && parsed.getNationalNumber) {
                    const cc = parsed.getCountryCode();
                    const nn = parsed.getNationalNumber();
                    
                    if (cc && nn) {
                        const regionCode = window.intlTelInputUtils.getRegionCodeForCountryCode(cc);
                        if (regionCode) {
                            console.log('🌐 Auto-detected (with intl code):', regionCode, '(country code:', cc, ')');
                            return regionCode;
                        }
                    }
                }
            } catch (e) {
                console.log('  → Parse attempt 1 (empty region) failed:', e.message);
            }

            // Si le numéro ne commence pas par +, essayer en ajoutant TOUS les codes pays (1-999)
            if (!phoneNumber.startsWith('+')) {
                console.log('  → Trying all country codes 1-999 for bare number...');
                
                for (let cc = 1; cc <= 999; cc++) {
                    try {
                        // Essayer en préfixant avec le code pays
                        const testNumber = '+' + cc + phoneNumber;
                        const parsed = window.intlTelInputUtils.parseNumber(testNumber, '');
                        
                        if (parsed && parsed.getCountryCode && parsed.getNationalNumber) {
                            const parsedCC = parsed.getCountryCode();
                            const nn = parsed.getNationalNumber();
                            
                            // Vérifier que le code pays correspond et que le numéro national est valide
                            if (parsedCC === cc && nn && String(nn).length >= 6) {
                                const regionCode = window.intlTelInputUtils.getRegionCodeForCountryCode(cc);
                                if (regionCode) {
                                    console.log('🌐 Auto-detected (country code', cc, '):', regionCode);
                                    return regionCode;
                                }
                            }
                        }
                    } catch (e) {
                        // Continue to next code - fail silently for speed
                    }
                }
            }

            console.log('  → No country auto-detection successful');
            return null;
        }

        // Fonction utilitaire pour afficher des alertes personnalisées
        async function showCustomAlert(title, message) {
            return new Promise(resolve => {
                const modal = document.getElementById('custom-modal-overlay');
                const titleEl = document.getElementById('custom-modal-title');
                const messageEl = document.getElementById('custom-modal-message');
                const confirmBtn = document.getElementById('custom-modal-confirm-btn');
                const closeBtn = document.getElementById('custom-modal-close');
                const cancelBtn = document.getElementById('custom-modal-cancel-btn');
                const promptContainer = document.getElementById('custom-modal-prompt-container');

                titleEl.textContent = title;
                messageEl.textContent = message;
                promptContainer.classList.add('hidden');

                if (title === 'Erreur') {
                    confirmBtn.textContent = 'Retour au formulaire';
                } else {
                    confirmBtn.textContent = 'OK';
                }

                modal.classList.remove('hidden');

                const closeModal = () => {
                    modal.classList.add('hidden');
                    if (title === 'Erreur') {
                        window.location.href = '{{ route("form-consigne") }}';
                    }
                    resolve(true);
                };

                confirmBtn.onclick = closeModal;
                closeBtn.onclick = closeModal;
                cancelBtn.onclick = closeModal;

                modal.onclick = function(e) {
                    if (e.target === modal) closeModal();
                };
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser intl-tel-input
            const phoneInput = document.querySelector("#modal-telephone");
            let itiInstance = null;
            
            if (phoneInput) {
                itiInstance = window.intlTelInput(phoneInput, {
                    initialCountry: "fr",
                    preferredCountries: ["fr", "be", "ch", "ca", "mu"],
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
                    formatOnDisplay: false, // Changé à false pour plus de contrôle
                    autoPlaceholder: "aggressive",
                    separateDialCode: true,
                    nationalMode: false
                });

                console.log('✅ Phone input initialized');

                // Permettre la saisie libre
                phoneInput.addEventListener('input', function(e) {
                    let value = phoneInput.value;
                    // Garder: chiffres, +, espaces, tirets, parenthèses
                    value = value.replace(/[^\d+\s\-\(\)]/g, '');
                    phoneInput.value = value;
                });

                // Valider au blur
                phoneInput.addEventListener('blur', function() {
                    const value = phoneInput.value.trim();
                    
                    if (!value) {
                        phoneInput.classList.remove('input-error');
                        const errorMsg = phoneInput.parentElement.querySelector('.phone-error-msg');
                        if (errorMsg) errorMsg.remove();
                        return;
                    }
                    
                    console.log('🔍 Validation du numéro...');
                    
                    let countryData = itiInstance.getSelectedCountryData();
                    let countryCodeUpper = String(countryData.iso2 || '').toUpperCase();
                    const normalized = normalizePhoneNumber(value, countryData);
                    
                    // Tenter de détecter le pays depuis le numéro
                    const detectedRegion = detectCountryFromNumber(normalized, itiInstance);
                    if (detectedRegion && detectedRegion !== countryCodeUpper) {
                        console.log('🔄 Switching country to detected:', detectedRegion);
                        itiInstance.setCountry(detectedRegion.toLowerCase());
                        countryData = itiInstance.getSelectedCountryData();
                        countryCodeUpper = detectedRegion;
                    }
                    
                    phoneInput.value = normalized;
                    itiInstance.setNumber(normalized);
                    
                    // Attendre que ITI traite le numéro
                    setTimeout(() => {
                        const formatted = itiInstance.getNumber() || normalized;
                        const isValid = isLenientlyValidNumber(formatted, countryCodeUpper);
                        
                        if (isValid) {
                            phoneInput.classList.remove('input-error');
                            const errorMsg = phoneInput.parentElement.querySelector('.phone-error-msg');
                            if (errorMsg) errorMsg.remove();
                            
                            phoneInput.value = formatted;
                            console.log('✅ Numéro valide:', formatted);
                        } else {
                            phoneInput.classList.add('input-error');
                            
                            let errorMsg = phoneInput.parentElement.querySelector('.phone-error-msg');
                            if (!errorMsg) {
                                errorMsg = document.createElement('p');
                                errorMsg.className = 'phone-error-msg text-red-500 text-sm mt-1';
                                phoneInput.parentElement.appendChild(errorMsg);
                            }
                            
                            const errorCode = itiInstance.getValidationError();
                            const errors = {
                                0: 'Numéro invalide',
                                1: 'Code pays invalide',
                                2: 'Numéro trop court',
                                3: 'Numéro trop long',
                                4: 'Format invalide'
                            };
                            errorMsg.textContent = errors[errorCode] || 'Numéro invalide';
                            console.log('❌ Numéro invalide:', errors[errorCode]);
                        }
                    }, 100);
                });

                // Re-normaliser au changement de pays
                phoneInput.addEventListener('countrychange', function() {
                    const value = phoneInput.value.trim();
                    if (value) {
                        console.log('🌍 Changement de pays');
                        phoneInput.dispatchEvent(new Event('blur'));
                    }
                });
            }
            
            // Charger Google Places API si nécessaire
            @if($googlePlacesApiKey)
            const openClientProfileModalBtn = document.getElementById('openClientProfileModalBtn');
            if (openClientProfileModalBtn) {
                openClientProfileModalBtn.addEventListener('click', function() {
                    if (!window.google || !window.google.maps || !window.google.maps.places) {
                        loadGoogleMapsAPI(initAutocomplete);
                    } else {
                        setTimeout(initAutocomplete, 100);
                    }
                });
            }
            @endif
            
            const isProfileComplete = @json($isProfileComplete);
            const isGuest = @json($isGuest);
            console.log('Script loaded. isGuest:', isGuest, 'isProfileComplete:', isProfileComplete);
            
            const clientProfileModal = document.getElementById('clientProfileModal');
            const closeClientProfileModalBtn = document.getElementById('closeClientProfileModalBtn');
            const clientProfileForm = document.getElementById('clientProfileForm');
            const userData = @json($user);
            let areAdditionalFieldsVisible = false;

            const additionalFieldsContainer = document.getElementById('additional-fields-container');
            const toggleAdditionalFieldsBtn = document.getElementById('toggleAdditionalFieldsBtn');
            const toggleText = document.getElementById('toggleText');

            function toggleAdditionalFields() {
                if (additionalFieldsContainer) {
                    additionalFieldsContainer.classList.toggle('hidden');
                    areAdditionalFieldsVisible = !additionalFieldsContainer.classList.contains('hidden');
                    toggleText.textContent = areAdditionalFieldsVisible ? "Masquer les champs optionnels" : "Compléter mon profil (facultatif)";
                }
            }

            if (toggleAdditionalFieldsBtn) {
                toggleAdditionalFieldsBtn.addEventListener('click', toggleAdditionalFields);
            }

            function validateGuestForm() {
                console.log('validateGuestForm called');

                const requiredFields = [
                    'modal-prenom',
                    'modal-nom',
                    'modal-telephone',
                    'modal-adresse'
                ];

                let isValid = true;

                requiredFields.forEach(fieldId => {
                    const input = document.getElementById(fieldId);
                    if (!input || input.value.trim() === '') {
                        isValid = false;
                        if (input) input.classList.add('input-error');
                    } else {
                        input.classList.remove('input-error');
                    }
                });

                // Validation téléphone
                const phoneInput = document.getElementById('modal-telephone');
                if (phoneInput && window.intlTelInputGlobals) {
                    const itiInstance = window.intlTelInputGlobals.getInstance(phoneInput);
                    if (itiInstance && phoneInput.value.trim()) {
                        let countryData = itiInstance.getSelectedCountryData();
                        let countryCodeUpper = String(countryData.iso2 || '').toUpperCase();
                        const value = phoneInput.value.trim();
                        
                        const normalizedNumber = normalizePhoneNumber(value, countryData);
                        
                        // Auto-détecter le pays depuis le numéro
                        const detectedRegion = detectCountryFromNumber(normalizedNumber, itiInstance);
                        if (detectedRegion && detectedRegion !== countryCodeUpper) {
                            console.log('🔄 Form validation: Switching country to detected:', detectedRegion);
                            itiInstance.setCountry(detectedRegion.toLowerCase());
                            countryData = itiInstance.getSelectedCountryData();
                            countryCodeUpper = detectedRegion;
                        }
                        
                        phoneInput.value = normalizedNumber;
                        itiInstance.setNumber(normalizedNumber);
                        
                        const formatted = itiInstance.getNumber() || normalizedNumber;
                        const isValidPhone = isLenientlyValidNumber(formatted, countryCodeUpper);
                        if (!isValidPhone) {
                            isValid = false;
                            phoneInput.classList.add('input-error');
                            
                            let errorMsg = phoneInput.parentElement.querySelector('.phone-error-msg');
                            if (!errorMsg) {
                                errorMsg = document.createElement('p');
                                errorMsg.className = 'phone-error-msg text-red-500 text-sm mt-1';
                                phoneInput.parentElement.appendChild(errorMsg);
                            }
                            errorMsg.textContent = 'Numéro de téléphone invalide';
                        } else {
                            phoneInput.value = itiInstance.getNumber();
                            const errorMsg = phoneInput.parentElement.querySelector('.phone-error-msg');
                            if (errorMsg) {
                                errorMsg.remove();
                            }
                        }
                    }
                }

                const optionalValidators = [
                    {
                        id: 'modal-codePostal',
                        validate: value => /^\d+$/.test(value),
                        error: 'Code postal invalide'
                    }
                ];

                optionalValidators.forEach(({ id, validate }) => {
                    const input = document.getElementById(id);
                    if (input && input.value.trim() !== '') {
                        if (!validate(input.value.trim())) {
                            isValid = false;
                            input.classList.add('input-error');
                        } else {
                            input.classList.remove('input-error');
                        }
                    } else if (input) {
                        input.classList.remove('input-error');
                    }
                });

                console.log('Validation result:', isValid);
                return isValid;
            }

            if (openClientProfileModalBtn) {
                openClientProfileModalBtn.addEventListener('click', () => {
                    if (additionalFieldsContainer) {
                        additionalFieldsContainer.classList.add('hidden');
                        areAdditionalFieldsVisible = false;
                        toggleText.textContent = "Compléter mon profil (facultatif)";
                    }

                    document.getElementById('modal-email').value = userData.email || '';
                    document.getElementById('modal-nom').value = (isGuest && (userData.nom === 'Invité' || userData.nom === null)) ? '' : (userData.nom || '');
                    document.getElementById('modal-prenom').value = (isGuest && (userData.prenom === 'Client' || userData.prenom === null)) ? '' : (userData.prenom || '');
                    document.getElementById('modal-telephone').value = userData.telephone || '';
                    document.getElementById('modal-adresse').value = userData.adresse || '';
                    document.getElementById('modal-ville').value = userData.ville || '';
                    document.getElementById('modal-codePostal').value = userData.codePostal || '';
                    document.getElementById('modal-pays').value = userData.pays || '';
                    document.getElementById('modal-civilite').value = userData.civilite || '';
                    document.getElementById('modal-nomSociete').value = userData.nomSociete || '';
                    document.getElementById('modal-complementAdresse').value = userData.complementAdresse || '';

                    clientProfileModal.classList.remove('hidden');
                });
            }

            if (closeClientProfileModalBtn) {
                closeClientProfileModalBtn.addEventListener('click', () => {
                    clientProfileModal.classList.add('hidden');
                });
            }

            if (clientProfileForm) {
                clientProfileForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    console.log('Form submission intercepted.');

                    if (isGuest) {
                        console.log('Running validation for guest...');
                        if (!validateGuestForm()) {
                            console.log('Validation failed. Submission stopped.');
                            await showCustomAlert('Erreur', 'Veuillez remplir tous les champs obligatoires correctement.');
                            return; 
                        }
                        console.log('Validation passed.');
                    }

                    const formData = new FormData(clientProfileForm);
                    const data = Object.fromEntries(formData.entries());

                    for (const key in data) {
                        if (data[key] === '') {
                            data[key] = null;
                        }
                    }

                    const url = isGuest ? '{{ route("session.updateGuestInfo") }}' : '{{ route("client.update-profile") }}';

                    try {
                        clientProfileModal.classList.add('hidden');

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            const loader = document.getElementById('loader');
                            if (loader) {
                                loader.classList.remove('hidden');
                            }
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        } else {
                            clientProfileModal.classList.remove('hidden'); 
                            let errorMessage = result.message || 'Une erreur inconnue est survenue.';
                            if (result.errors) {
                                errorMessage = 'Veuillez corriger les erreurs suivantes :\n';
                                Object.values(result.errors).forEach(errorArray => {
                                    errorMessage += `\n- ${errorArray[0]}`;
                                });
                            }
                            await showCustomAlert('Erreur de mise à jour', errorMessage);
                            console.error('Update error:', result);
                        }
                    } catch (error) {
                        clientProfileModal.classList.remove('hidden');
                        await showCustomAlert('Erreur', 'Une erreur réseau est survenue.');
                        console.error('Network error:', error);
                    }
                });
            }

            if (!isProfileComplete && openClientProfileModalBtn) {
                setTimeout(() => {
                    openClientProfileModalBtn.click();
                }, 500);
            }

            const paymentResetBtn = document.getElementById('payment-reset-btn');
            if (paymentResetBtn) {
                const modalHTML = `
                    <div id="payment-reset-confirm-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 z-50 flex items-center justify-center px-4">
                        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                            <h3 class="text-xl font-bold text-gray-800">Réinitialiser la commande</h3>
                            <p class="mt-4 text-gray-600">Voulez-vous vraiment continuer ? Toutes les données saisies pour votre commande actuelle seront définitivement perdues.</p>
                            <div class="mt-6 flex justify-end space-x-4">
                                <button id="payment-reset-cancel-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-full hover:bg-gray-300">Annuler</button>
                                <button id="payment-reset-confirm-btn" class="px-4 py-2 bg-red-600 text-white rounded-full hover:bg-red-700">Confirmer</button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHTML);

                const resetModal = document.getElementById('payment-reset-confirm-modal');
                const cancelBtn = document.getElementById('payment-reset-cancel-btn');
                const confirmBtn = document.getElementById('payment-reset-confirm-btn');

                const showResetConfirm = () => {
                    return new Promise(resolve => {
                        resetModal.classList.remove('hidden');
                        cancelBtn.onclick = () => {
                            resetModal.classList.add('hidden');
                            resolve(false);
                        };
                        confirmBtn.onclick = () => {
                            resetModal.classList.add('hidden');
                            resolve(true);
                        };
                    });
                };

                paymentResetBtn.addEventListener('click', async function() {
                    const confirmed = await showResetConfirm();

                    if (confirmed) {
                        const loader = document.getElementById('loader');
                        if (loader) loader.classList.remove('hidden');

                        sessionStorage.removeItem('formState');
                        
                        try {
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
                            window.location.href = '{{ route("form-consigne") }}';
                        }, 500);
                    }
                });
            }
            
            if (window.location.search.includes('nocache') || !window.google) {
                const links = document.querySelectorAll('link[rel="stylesheet"]');
                links.forEach(link => {
                    if (link.href) {
                        link.href = link.href.split('?')[0] + '?v=' + googleApiVersion;
                    }
                });
            }

            @if(session('error'))
                showCustomAlert('Erreur', '{{ session('error') }}');
            @endif
        });
    </script>
</body>
</html>




