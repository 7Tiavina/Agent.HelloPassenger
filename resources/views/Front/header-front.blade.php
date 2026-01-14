<!-- resources/views/partials/header.blade.php (ou l'endroit où se trouve ton header) -->

@php
    $clientGuard = Auth::guard('client');
@endphp

<!-- Header -->
<header class="bg-yellow-custom px-6 py-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <a href="{{ url('/') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                    <span class="text-yellow-custom text-sm font-bold">H</span>
                </div>
                <span class="text-black font-semibold text-lg">HelloPassenger</span>
            </a>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Bouton Admin discret -->
            <a href="{{ route('login') }}" class="text-gray-600 hover:text-black text-xs font-medium transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                <span class="sr-only">Admin</span>
            </a>

            <div class="hidden md:flex items-center space-x-4">
                @if($clientGuard->check())
                    <!-- Utilisateur connecté : afficher Déconnecter et lien vers form-consigne -->
                    <a href="{{ route('form-consigne') }}" class="bg-gray-dark text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-700 transition-colors">
                        RÉSERVER
                    </a>

                    <form method="POST" action="{{ route('client.logout') }}" class="inline-block">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-red-500 transition-colors">
                            DÉCONNECTER
                        </button>
                    </form>
                @else
                    <!-- Non connecté : bouton afficher modal login -->
                    <button id="openLoginDesktop" class="bg-gray-dark text-white px-6 py-2 rounded-full text-sm font-medium hover:bg-gray-700 transition-colors btn-hover" type="button">
                        SE CONNECTER
                    </button>

                    <button id="openMyOrders" class="bg-gray-dark text-white px-6 py-2 rounded-full text-sm font-medium hover:bg-gray-700 transition-colors" type="button">
                        MES RÉSERVATIONS
                    </button>
                @endif
            </div>

            <!-- Menu Hamburger pour mobile -->
            <button class="md:hidden flex items-center space-x-2 cursor-pointer focus:outline-none" aria-label="Toggle menu" id="mobile-menu-button">
                <span class="text-black text-sm font-medium">MENU</span>
                <div class="flex flex-col space-y-1">
                    <div class="w-4 h-0.5 bg-black"></div>
                    <div class="w-4 h-0.5 bg-black"></div>
                    <div class="w-4 h-0.5 bg-black"></div>
                </div>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Menu (Off-canvas ou Modal) -->
<div id="mobile-menu" class="fixed inset-0 bg-yellow-custom z-40 hidden md:hidden flex-col items-center justify-center space-y-8">
    <button class="absolute top-4 right-4 text-black text-3xl focus:outline-none" aria-label="Close menu" id="close-mobile-menu">
        &times;
    </button>

    <a href="{{ route('login') }}" class="text-gray-600 hover:text-black text-2xl font-medium transition-colors">
        Admin
    </a>

    @if($clientGuard->check())
        <a href="{{ route('form-consigne') }}" class="bg-gray-dark text-white px-8 py-4 rounded-full text-xl font-medium hover:bg-gray-700 transition-colors">
            RÉSERVER
        </a>

        <form method="POST" action="{{ route('client.logout') }}" class="inline-block">
            @csrf
            <button type="submit" class="bg-red-600 text-white px-8 py-4 rounded-full text-xl font-medium hover:bg-red-500 transition-colors">
                DÉCONNECTER
            </button>
        </form>
    @else
        <button id="openLoginMobile" class="bg-gray-dark text-white px-8 py-4 rounded-full text-xl font-medium hover:bg-gray-700 transition-colors btn-hover" type="button">
            SE CONNECTER
        </button>

        <button id="openMyOrdersMobile" class="bg-gray-dark text-white px-8 py-4 rounded-full text-xl font-medium hover:bg-gray-700 transition-colors" type="button">
            MES RÉSERVATIONS
        </button>
    @endif
</div>

<!-- Loading ANIMATION (idem) -->
<div id="loader" class="fixed inset-0 bg-gray-900 bg-opacity-80 z-50 hidden flex flex-col items-center justify-center" role="status" aria-live="polite" aria-label="Loading">
    <div class="w-64 h-2 bg-gray-700 rounded-full overflow-hidden mb-4">
        <div class="h-full bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 animate-progress"></div>
    </div>
    <div class="text-yellow-400 font-medium tracking-wider flex items-center">
        <span class="animate-pulse">LOADING</span>
        <span class="ml-1 animate-pulse delay-75">.</span>
        <span class="animate-pulse delay-150">.</span>
        <span class="animate-pulse delay-300">.</span>
    </div>
</div>

<!-- Modal LOGIN -->
<div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="loginModalTitle" tabindex="-1">
    <div class="bg-yellow-400 w-full max-w-md p-8 rounded shadow-lg relative">
        <button id="closeModal" class="absolute top-2 right-2 text-black text-xl font-bold" aria-label="Close login modal">&times;</button>
        <h2 id="loginModalTitle" class="text-2xl font-bold text-center mb-6">Se connecter</h2>

        <form method="POST" action="{{ route('client.login.submit') }}" class="space-y-4" novalidate>
            @csrf
            <div>
                <label for="loginEmail" class="block text-sm font-medium">VOTRE ADRESSE EMAIL : <span class="text-red-500">*</span></label>
                <input id="loginEmail" name="email" type="email" value="{{ old('email') }}" class="w-full px-4 py-2 border rounded bg-gray-100" required autocomplete="email" />
            </div>
            <div>
                <label for="loginPassword" class="block text-sm font-medium">VOTRE MOT DE PASSE : <span class="text-red-500">*</span></label>
                <input id="loginPassword" name="password" type="password" class="w-full px-4 py-2 border rounded bg-gray-100" required autocomplete="current-password" />
            </div>
            <div class="flex items-center justify-between text-sm">
                <a href="#" class="text-black font-semibold underline">Mot de passe oublié ?</a>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="accent-black" />
                    <span>Rester connecté(e)</span>
                </label>
            </div>
            <button type="submit" class="w-full bg-black text-white py-2 rounded-full font-bold hover:bg-gray-800 flex items-center justify-center gap-2">
                SE CONNECTER
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </form>

        <button id="openRegister" class="w-full mt-4 bg-white text-black py-2 rounded-full font-bold hover:bg-gray-100" type="button">CRÉER UN COMPTE →</button>
    </div>
</div>

<!-- Modal Erreur connexion -->
<div id="loginErrorModal" class="fixed inset-0 bg-black bg-opacity-50 z-60 hidden flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="loginErrorTitle" tabindex="-1">
    <div class="bg-white w-full max-w-sm p-6 rounded shadow-lg relative text-center">
        <h3 id="loginErrorTitle" class="text-xl font-semibold mb-4">Erreur de connexion</h3>
        <p class="mb-6">Identifiants invalides — veuillez réessayer.</p>
        <button id="closeLoginError" class="bg-black text-white px-5 py-2 rounded-full">Fermer</button>
    </div>
</div>

<!-- Modal Créer un compte -->
<div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="registerModalTitle" tabindex="-1">
    <div class="bg-yellow-400 w-full max-w-md p-8 rounded shadow-lg relative">
        <button id="closeRegisterModal" class="absolute top-2 right-2 text-black text-xl font-bold" aria-label="Close register modal">&times;</button>
        <h2 id="registerModalTitle" class="text-2xl font-bold text-center mb-6">Créer un compte</h2>

        <form method="POST" action="{{ route('client.register') }}" class="space-y-4" novalidate>
            @csrf

            <div>
                <label for="registerNom" class="block text-sm font-medium">NOM : <span class="text-red-500">*</span></label>
                <input id="registerNom" name="nom" type="text" value="{{ old('nom') }}" class="w-full px-4 py-2 border rounded bg-gray-100" required />
                @error('nom') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="registerPrenom" class="block text-sm font-medium">PRÉNOM : <span class="text-red-500">*</span></label>
                <input id="registerPrenom" name="prenom" type="text" value="{{ old('prenom') }}" class="w-full px-4 py-2 border rounded bg-gray-100" required />
                @error('prenom') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="registerEmail" class="block text-sm font-medium">ADRESSE EMAIL : <span class="text-red-500">*</span></label>
                <input id="registerEmail" name="email" type="email" value="{{ old('email') }}" class="w-full px-4 py-2 border rounded bg-gray-100" required />
                @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="registerTelephone" class="block text-sm font-medium">TÉLÉPHONE :</label>
                <input id="registerTelephone" name="telephone" type="text" value="{{ old('telephone') }}" class="w-full px-4 py-2 border rounded bg-gray-100" />
                @error('telephone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="registerPassword" class="block text-sm font-medium">MOT DE PASSE : <span class="text-red-500">*</span></label>
                <input id="registerPassword" name="password" type="password" class="w-full px-4 py-2 border rounded bg-gray-100" required autocomplete="new-password" />
                @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="registerPasswordConfirm" class="block text-sm font-medium">CONFIRMER MOT DE PASSE : <span class="text-red-500">*</span></label>
                <input id="registerPasswordConfirm" name="password_confirmation" type="password" class="w-full px-4 py-2 border rounded bg-gray-100" required />
            </div>

            <button type="submit" class="w-full bg-black text-white py-2 rounded-full font-bold hover:bg-gray-800 flex items-center justify-center gap-2">
                CRÉER MON COMPTE
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        </form>

        <button id="goToLoginBtn" class="mt-4 w-full bg-white text-black py-2 rounded-full font-bold hover:bg-gray-200">
            SE CONNECTER
        </button>
    </div>
</div>

<!-- Scripts -->
@if(session('from_register') || $errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const registerModal = document.getElementById('registerModal');
        if (registerModal) registerModal.classList.remove('hidden');

        // optionnel : focus first input
        const first = registerModal.querySelector('input');
        if (first) first.focus();
    });
</script>
@endif

<script>
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    const loader = document.getElementById('loader');

    window.openLoginModal = function() { // Rendre la fonction globale
        loader.classList.remove('hidden');
        setTimeout(() => {
            loader.classList.add('hidden');
            loginModal.classList.remove('hidden');
        }, 300);
    }

    // open login btn (desktop)
    const openLoginDesktop = document.getElementById('openLoginDesktop');
    if (openLoginDesktop) {
        openLoginDesktop.addEventListener('click', () => {
            openLoginModal(); // Appeler la nouvelle fonction
        });
    }

    // open register btn inside login
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

    // go to login from register
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

    // close handlers
    const closeLoginBtn = document.getElementById('closeModal');
    if (closeLoginBtn) closeLoginBtn.addEventListener('click', () => loginModal.classList.add('hidden'));

    const closeRegisterBtn = document.getElementById('closeRegisterModal');
    if (closeRegisterBtn) closeRegisterBtn.addEventListener('click', () => registerModal.classList.add('hidden'));

    window.addEventListener('click', (e) => {
        if (e.target === loginModal) loginModal.classList.add('hidden');
        if (e.target === registerModal) registerModal.classList.add('hidden');
    });

    // Error modal (open if session flash 'login_error' exists)
    @if(session('login_error'))
        document.addEventListener('DOMContentLoaded', function() {
            const loginErrorModal = document.getElementById('loginErrorModal');
            const closeLoginError = document.getElementById('closeLoginError');
            if (loginErrorModal) loginErrorModal.classList.remove('hidden');
            if (closeLoginError) closeLoginError.addEventListener('click', () => loginErrorModal.classList.add('hidden'));
        });
    @endif
</script>

<!-- tailwind config script (idem) -->
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
                mobileMenu.classList.add('flex'); // Assure que flex est appliqué pour la disposition
            });

            closeMobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('flex');
            });
        }

        // Gérer le clic sur le bouton "SE CONNECTER" mobile pour ouvrir la modale de login
        const openLoginMobile = document.getElementById('openLoginMobile');
        if (openLoginMobile && window.openLoginModal) { // window.openLoginModal est définie dans le script du loginModal
            openLoginMobile.addEventListener('click', () => {
                mobileMenu.classList.add('hidden'); // Fermer le menu mobile
                mobileMenu.classList.remove('flex');
                window.openLoginModal(); // Ouvrir la modale de login
            });
        }
    });
</script>
