<!-- resources/views/partials/header.blade.php -->
@php
    $clientGuard = Auth::guard('client');
@endphp

<!-- Header -->
<header class="bg-gray-dark px-6 py-4 shadow-md">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
            <a href="{{ url('/') }}" class="flex items-center space-x-2">
    <img src="{{ asset('HP-Logo.png') }}" alt="HelloPassenger" class="h-40 w-auto">
</a>

        </div>

        <!-- Navigation Desktop -->
        <div class="hidden md:flex items-center space-x-6">
    @if($clientGuard->check())
        <!-- Utilisateur connecté -->
        <!--
        <a
            href="{{ route('form-consigne') }}"
            class="bg-yellow-custom text-gray-dark px-10 py-5 rounded-full text-lg font-extrabold
                   hover:bg-yellow-hover transition-all duration-200
                   shadow-xl hover:shadow-2xl hover:scale-110"
        >
            RÉSERVER
        </a> -->

        <form method="POST" action="{{ route('client.logout') }}" class="inline-block">
            @csrf
            <button
                type="submit"
                class="bg-red-600 text-white px-10 py-5 rounded-full text-lg font-extrabold
                       hover:bg-red-700 transition-all duration-200
                       shadow-xl hover:shadow-2xl hover:scale-110"
            >
                DÉCONNECTER
            </button>
        </form>
    @else
        <!-- Non connecté -->
        <button
            id="openLoginDesktop"
            class="bg-yellow-custom text-gray-dark px-10 py-5 rounded-full text-lg font-extrabold
                   hover:bg-yellow-hover transition-all duration-200
                   shadow-xl hover:shadow-2xl hover:scale-110"
            type="button"
        >
            SE CONNECTER
        </button>

        <!--
        <button
            id="openMyOrders"
            class="bg-yellow-custom text-gray-dark px-10 py-5 rounded-full text-lg font-extrabold
                   hover:bg-yellow-hover transition-all duration-200
                   shadow-xl hover:shadow-2xl hover:scale-110"
            type="button"
        >
            MES RÉSERVATIONS
        </button> -->
    @endif

    <!-- Lien Admin discret -->
    <a
        href="{{ route('login') }}"
        class="ml-4 pl-4 border-l border-gray-600
               text-gray-300 hover:text-white transition-colors"
        title="Admin"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
        </svg>
    </a>
</div>


        <!-- Menu Hamburger pour mobile -->
        <button class="md:hidden flex items-center space-x-3 cursor-pointer focus:outline-none group" aria-label="Toggle menu" id="mobile-menu-button">
            <span class="text-yellow-custom text-base font-bold group-hover:text-yellow-hover transition-colors">MENU</span>
            <div class="flex flex-col space-y-1.5">
                <div class="w-6 h-1 bg-yellow-custom group-hover:bg-yellow-hover transition-colors rounded-full"></div>
                <div class="w-6 h-1 bg-yellow-custom group-hover:bg-yellow-hover transition-colors rounded-full"></div>
                <div class="w-6 h-1 bg-yellow-custom group-hover:bg-yellow-hover transition-colors rounded-full"></div>
            </div>
        </button>
    </div>
</header>

<!-- Mobile Menu (Off-canvas) -->
<div id="mobile-menu" class="fixed inset-0 bg-gray-dark bg-opacity-98 z-50 hidden md:hidden flex-col items-center justify-center space-y-6 p-8">
    <button class="absolute top-8 right-8 text-yellow-custom text-5xl font-bold hover:text-yellow-hover transition-colors focus:outline-none" aria-label="Close menu" id="close-mobile-menu">
        &times;
    </button>

    <!-- Logo en mobile -->
    <div class="mb-10">
        <img src="{{ asset('HP-Logo-White.png') }}" alt="HelloPassenger" class="h-20 w-auto mx-auto">
    </div>

    @if($clientGuard->check())
        <!-- Utilisateur connecté mobile -->
        <a href="{{ route('form-consigne') }}" class="w-full max-w-sm bg-yellow-custom text-gray-dark px-8 py-5 rounded-full text-xl font-bold hover:bg-yellow-hover transition-all duration-200 transform hover:scale-105 shadow-2xl text-center">
            RÉSERVER
        </a>

        <form method="POST" action="{{ route('client.logout') }}" class="w-full max-w-sm">
            @csrf
            <button type="submit" class="w-full bg-red-600 text-white px-8 py-5 rounded-full text-xl font-bold hover:bg-red-700 transition-all duration-200 transform hover:scale-105 shadow-2xl">
                DÉCONNECTER
            </button>
        </form>
    @else
        <!-- Non connecté mobile -->
        <button id="openLoginMobile" class="w-full max-w-sm bg-yellow-custom text-gray-dark px-8 py-5 rounded-full text-xl font-bold hover:bg-yellow-hover transition-all duration-200 transform hover:scale-105 shadow-2xl" type="button">
            SE CONNECTER
        </button>

        <button id="openMyOrdersMobile" class="w-full max-w-sm bg-yellow-custom text-gray-dark px-8 py-5 rounded-full text-xl font-bold hover:bg-yellow-hover transition-all duration-200 transform hover:scale-105 shadow-2xl" type="button">
            MES RÉSERVATIONS
        </button>
    @endif

    <!-- Lien Admin mobile -->
    <a href="{{ route('login') }}" class="text-gray-300 hover:text-white text-lg font-medium transition-colors mt-10 pt-10 border-t border-gray-700 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
        </svg>
        Accès Admin
    </a>
</div>

<!-- Loading ANIMATION -->
<div id="loader" class="fixed inset-0 bg-gray-dark bg-opacity-95 z-50 hidden flex flex-col items-center justify-center" role="status" aria-live="polite" aria-label="Loading">
    <div class="mb-8">
        <img src="{{ asset('HP-Logo-White.png') }}" alt="HelloPassenger" class="h-16 w-auto mx-auto">
    </div>
    <div class="w-80 h-3 bg-gray-700 rounded-full overflow-hidden mb-6">
        <div class="h-full bg-gradient-to-r from-yellow-custom via-yellow-hover to-yellow-custom animate-progress"></div>
    </div>
    <div class="text-yellow-custom font-bold tracking-widest text-xl flex items-center">
        <span class="animate-pulse">CHARGEMENT</span>
        <span class="ml-2 animate-pulse delay-75">.</span>
        <span class="animate-pulse delay-150">.</span>
        <span class="animate-pulse delay-300">.</span>
    </div>
</div>

<!-- Modal LOGIN -->
<div id="loginModal" class="fixed inset-0 bg-gray-dark bg-opacity-95 z-50 hidden flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="loginModalTitle" tabindex="-1">
    <div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-2xl relative">
        <button id="closeModal" class="absolute top-4 right-4 text-gray-700 text-2xl font-bold hover:text-black transition-colors" aria-label="Close login modal">&times;</button>
        
        <div class="text-center mb-6">
            <img src="{{ asset('HP-Logo-White.png') }}" alt="HelloPassenger" class="h-14 w-auto mx-auto mb-4">
            <h2 id="loginModalTitle" class="text-3xl font-bold text-gray-dark mb-2">Se connecter</h2>
            <p class="text-gray-600">Accédez à votre compte</p>
        </div>

        <form method="POST" action="{{ route('client.login.submit') }}" class="space-y-5" novalidate>
            @csrf
            <div>
                <label for="loginEmail" class="block text-sm font-bold text-gray-700 mb-2">VOTRE ADRESSE EMAIL : <span class="text-red-500">*</span></label>
                <input id="loginEmail" name="email" type="email" value="{{ old('email') }}" 
                       class="w-full px-4 py-3 border-2 border-gray-300 bg-gray-50 text-gray-800 rounded-lg focus:border-yellow-custom focus:ring-2 focus:ring-yellow-custom focus:outline-none transition-all" 
                       required autocomplete="email" />
            </div>
            <div>
                <label for="loginPassword" class="block text-sm font-bold text-gray-700 mb-2">VOTRE MOT DE PASSE : <span class="text-red-500">*</span></label>
                <input id="loginPassword" name="password" type="password" 
                       class="w-full px-4 py-3 border-2 border-gray-300 bg-gray-50 text-gray-800 rounded-lg focus:border-yellow-custom focus:ring-2 focus:ring-yellow-custom focus:outline-none transition-all" 
                       required autocomplete="current-password" />
            </div>
            <div class="flex items-center justify-between text-sm">
                <a href="#" class="text-gray-dark font-bold hover:text-yellow-custom transition-colors underline">Mot de passe oublié ?</a>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="accent-yellow-custom" />
                    <span class="text-gray-700">Rester connecté(e)</span>
                </label>
            </div>
            <button type="submit" class="w-full bg-yellow-custom text-gray-dark py-4 rounded-full font-bold hover:bg-yellow-hover transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-3 text-lg">
                SE CONNECTER
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600 mb-3">Pas encore de compte ?</p>
            <button id="openRegister" class="w-full bg-gray-dark text-white py-3 rounded-full font-bold hover:bg-gray-800 transition-all duration-200 border-2 border-gray-dark" type="button">
                CRÉER UN COMPTE →
            </button>
        </div>
    </div>
</div>

<!-- Modal Erreur connexion -->
<div id="loginErrorModal" class="fixed inset-0 bg-gray-dark bg-opacity-95 z-60 hidden flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="loginErrorTitle" tabindex="-1">
    <div class="bg-white w-full max-w-sm p-8 rounded-2xl shadow-2xl relative text-center">
        <div class="text-red-500 text-5xl mb-4">⚠️</div>
        <h3 id="loginErrorTitle" class="text-2xl font-bold text-gray-dark mb-4">Erreur de connexion</h3>
        <p class="mb-8 text-gray-600 text-lg">Identifiants invalides — veuillez réessayer.</p>
        <button id="closeLoginError" class="bg-yellow-custom text-gray-dark px-8 py-3 rounded-full font-bold hover:bg-yellow-hover transition-all duration-200 shadow-lg">
            Fermer
        </button>
    </div>
</div>

<!-- Modal Créer un compte -->
<div id="registerModal" class="fixed inset-0 bg-gray-dark bg-opacity-95 z-50 hidden flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="registerModalTitle" tabindex="-1">
    <div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <button id="closeRegisterModal" class="absolute top-4 right-4 text-gray-700 text-2xl font-bold hover:text-black transition-colors" aria-label="Close register modal">&times;</button>
        
        <div class="text-center mb-6">
            <img src="{{ asset('HP-Logo-White.png') }}" alt="HelloPassenger" class="h-14 w-auto mx-auto mb-4">
            <h2 id="registerModalTitle" class="text-3xl font-bold text-gray-dark mb-2">Créer un compte</h2>
            <p class="text-gray-600">Rejoignez HelloPassenger</p>
        </div>

        <form method="POST" action="{{ route('client.register') }}" class="space-y-4" novalidate>
            @csrf

            <div>
                <label for="registerNom" class="block text-sm font-bold text-gray-700 mb-2">NOM : <span class="text-red-500">*</span></label>
                <input id="registerNom" name="nom" type="text" value="{{ old('nom') }}" 
                       class="w-full px-4 py-3 border-2 border-gray-300 bg-gray-50 text-gray-800 rounded-lg focus:border-yellow-custom focus:ring-2 focus:ring-yellow-custom focus:outline-none transition-all" 
                       required />
                @error('nom') <p class="text-red-600 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="registerPrenom" class="block text-sm font-bold text-gray-700 mb-2">PRÉNOM : <span class="text-red-500">*</span></label>
                <input id="registerPrenom" name="prenom" type="text" value="{{ old('prenom') }}" 
                       class="w-full px-4 py-3 border-2 border-gray-300 bg-gray-50 text-gray-800 rounded-lg focus:border-yellow-custom focus:ring-2 focus:ring-yellow-custom focus:outline-none transition-all" 
                       required />
                @error('prenom') <p class="text-red-600 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="registerEmail" class="block text-sm font-bold text-gray-700 mb-2">ADRESSE EMAIL : <span class="text-red-500">*</span></label>
                <input id="registerEmail" name="email" type="email" value="{{ old('email') }}" 
                       class="w-full px-4 py-3 border-2 border-gray-300 bg-gray-50 text-gray-800 rounded-lg focus:border-yellow-custom focus:ring-2 focus:ring-yellow-custom focus:outline-none transition-all" 
                       required />
                @error('email') <p class="text-red-600 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="registerTelephone" class="block text-sm font-bold text-gray-700 mb-2">TÉLÉPHONE :</label>
                <input id="registerTelephone" name="telephone" type="text" value="{{ old('telephone') }}" 
                       class="w-full px-4 py-3 border-2 border-gray-300 bg-gray-50 text-gray-800 rounded-lg focus:border-yellow-custom focus:ring-2 focus:ring-yellow-custom focus:outline-none transition-all" />
                @error('telephone') <p class="text-red-600 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="registerPassword" class="block text-sm font-bold text-gray-700 mb-2">MOT DE PASSE : <span class="text-red-500">*</span></label>
                <input id="registerPassword" name="password" type="password" 
                       class="w-full px-4 py-3 border-2 border-gray-300 bg-gray-50 text-gray-800 rounded-lg focus:border-yellow-custom focus:ring-2 focus:ring-yellow-custom focus:outline-none transition-all" 
                       required autocomplete="new-password" />
                @error('password') <p class="text-red-600 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="registerPasswordConfirm" class="block text-sm font-bold text-gray-700 mb-2">CONFIRMER MOT DE PASSE : <span class="text-red-500">*</span></label>
                <input id="registerPasswordConfirm" name="password_confirmation" type="password" 
                       class="w-full px-4 py-3 border-2 border-gray-300 bg-gray-50 text-gray-800 rounded-lg focus:border-yellow-custom focus:ring-2 focus:ring-yellow-custom focus:outline-none transition-all" 
                       required />
            </div>

            <button type="submit" class="w-full bg-yellow-custom text-gray-dark py-4 rounded-full font-bold hover:bg-yellow-hover transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-3 text-lg mt-6">
                CRÉER MON COMPTE
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600 mb-3">Déjà un compte ?</p>
            <button id="goToLoginBtn" class="w-full bg-gray-dark text-white py-3 rounded-full font-bold hover:bg-gray-800 transition-all duration-200 border-2 border-gray-dark">
                SE CONNECTER
            </button>
        </div>
    </div>
</div>

<!-- Scripts -->
@if(session('from_register') || $errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const registerModal = document.getElementById('registerModal');
        if (registerModal) {
            registerModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        const first = registerModal.querySelector('input');
        if (first) first.focus();
    });
</script>
@endif

<script>
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    const loader = document.getElementById('loader');

    // Fonction pour gérer l'overflow du body
    function toggleBodyOverflow(show) {
        document.body.style.overflow = show ? 'hidden' : '';
    }

    window.openLoginModal = function() {
        loader.classList.remove('hidden');
        toggleBodyOverflow(true);
        setTimeout(() => {
            loader.classList.add('hidden');
            loginModal.classList.remove('hidden');
        }, 300);
    }

    // Gestionnaires d'ouverture
    const openLoginDesktop = document.getElementById('openLoginDesktop');
    if (openLoginDesktop) {
        openLoginDesktop.addEventListener('click', () => openLoginModal());
    }

    const openRegisterBtn = document.getElementById('openRegister');
    if (openRegisterBtn) {
        openRegisterBtn.addEventListener('click', () => {
            loginModal.classList.add('hidden');
            loader.classList.remove('hidden');
            setTimeout(() => {
                loader.classList.add('hidden');
                registerModal.classList.remove('hidden');
            }, 300);
        });
    }

    const goToLoginBtn = document.getElementById('goToLoginBtn');
    if (goToLoginBtn) {
        goToLoginBtn.addEventListener('click', () => {
            registerModal.classList.add('hidden');
            loader.classList.remove('hidden');
            setTimeout(() => {
                loader.classList.add('hidden');
                loginModal.classList.remove('hidden');
            }, 300);
        });
    }

    // Gestionnaires de fermeture
    const closeLoginBtn = document.getElementById('closeModal');
    if (closeLoginBtn) {
        closeLoginBtn.addEventListener('click', () => {
            loginModal.classList.add('hidden');
            toggleBodyOverflow(false);
        });
    }

    const closeRegisterBtn = document.getElementById('closeRegisterModal');
    if (closeRegisterBtn) {
        closeRegisterBtn.addEventListener('click', () => {
            registerModal.classList.add('hidden');
            toggleBodyOverflow(false);
        });
    }

    window.addEventListener('click', (e) => {
        if (e.target === loginModal) {
            loginModal.classList.add('hidden');
            toggleBodyOverflow(false);
        }
        if (e.target === registerModal) {
            registerModal.classList.add('hidden');
            toggleBodyOverflow(false);
        }
    });

    // Modal d'erreur
    @if(session('login_error'))
        document.addEventListener('DOMContentLoaded', function() {
            const loginErrorModal = document.getElementById('loginErrorModal');
            const closeLoginError = document.getElementById('closeLoginError');
            if (loginErrorModal) {
                loginErrorModal.classList.remove('hidden');
                toggleBodyOverflow(true);
            }
            if (closeLoginError) {
                closeLoginError.addEventListener('click', () => {
                    loginErrorModal.classList.add('hidden');
                    toggleBodyOverflow(false);
                });
            }
        });
    @endif
</script>

<!-- Configuration Tailwind -->
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'yellow-custom': '#FFC107',
                    'yellow-hover': '#FFB300',
                    'gray-dark': '#1f2937'
                },
                keyframes: {
                    progress: {
                        '0%': { 
                            'background-position': '0% 50%',
                            'width': '0%' 
                        },
                        '50%': { 
                            'width': '100%',
                            'background-position': '100% 50%'
                        },
                        '100%': { 
                            'background-position': '0% 50%',
                            'width': '0%' 
                        }
                    }
                },
                animation: {
                    progress: 'progress 2s infinite linear'
                }
            }
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMobileMenuButton = document.getElementById('close-mobile-menu');

        if (mobileMenuButton && mobileMenu && closeMobileMenuButton) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('flex');
                toggleBodyOverflow(true);
            });

            closeMobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('flex');
                toggleBodyOverflow(false);
            });

            // Fermer menu mobile quand on clique sur un lien
            mobileMenu.querySelectorAll('a, button').forEach(element => {
                if (!element.id.includes('open')) {
                    element.addEventListener('click', () => {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('flex');
                        toggleBodyOverflow(false);
                    });
                }
            });
        }

        // Gestionnaire mobile pour login
        const openLoginMobile = document.getElementById('openLoginMobile');
        if (openLoginMobile && window.openLoginModal) {
            openLoginMobile.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('flex');
                window.openLoginModal();
            });
        }

        // Gestionnaire pour "Mes réservations"
        const openMyOrders = document.getElementById('openMyOrders');
        const openMyOrdersMobile = document.getElementById('openMyOrdersMobile');
        
        function handleMyOrders() {
            // À personnaliser selon votre logique
            alert('Fonctionnalité "Mes réservations" à implémenter');
        }
        
        if (openMyOrders) openMyOrders.addEventListener('click', handleMyOrders);
        if (openMyOrdersMobile) openMyOrdersMobile.addEventListener('click', handleMyOrders);
    });
</script>