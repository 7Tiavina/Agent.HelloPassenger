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
            
            <button class="bg-gray-dark text-white px-6 py-2 rounded-full text-sm font-medium hover:bg-gray-700 transition-colors btn-hover" type="button">
                SE CONNECTER
            </button>
            <button class="bg-gray-dark text-white px-6 py-2 rounded-full text-sm font-medium hover:bg-gray-700 transition-colors" type="button">
                MES RÉSERVATIONS
            </button>
            <div class="flex items-center space-x-2 cursor-pointer" role="button" tabindex="0" aria-label="Menu">
                <span class="text-black text-sm font-medium">MENU</span>
                <div class="flex flex-col space-y-1">
                    <div class="w-4 h-0.5 bg-black"></div>
                    <div class="w-4 h-0.5 bg-black"></div>
                    <div class="w-4 h-0.5 bg-black"></div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Loading ANIMATION -->
<div id="loader" class="fixed inset-0 bg-gray-900 bg-opacity-80 z-50 hidden flex flex-col items-center justify-center" role="status" aria-live="polite" aria-label="Loading">
    <!-- Barre de progression -->
    <div class="w-64 h-2 bg-gray-700 rounded-full overflow-hidden mb-4">
        <div class="h-full bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 animate-progress"></div>
    </div>
    
    <!-- Texte Loading -->
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
        <form class="space-y-4" novalidate>
            <div>
                <label for="loginEmail" class="block text-sm font-medium">VOTRE ADRESSE EMAIL : <span class="text-red-500">*</span></label>
                <input id="loginEmail" type="email" class="w-full px-4 py-2 border rounded bg-gray-100" required autocomplete="email" />
            </div>
            <div>
                <label for="loginPassword" class="block text-sm font-medium">VOTRE MOT DE PASSE : <span class="text-red-500">*</span></label>
                <input id="loginPassword" type="password" class="w-full px-4 py-2 border rounded bg-gray-100" required autocomplete="current-password" />
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

<!-- Modal Créer un compte -->
<div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="registerModalTitle" tabindex="-1">
    <div class="bg-yellow-400 w-full max-w-md p-8 rounded shadow-lg relative">
        <button id="closeRegisterModal" class="absolute top-2 right-2 text-black text-xl font-bold" aria-label="Close register modal">&times;</button>
        <h2 id="registerModalTitle" class="text-2xl font-bold text-center mb-6">Créer un compte</h2>
        <form class="space-y-4" novalidate>
            <div>
                <label for="registerName" class="block text-sm font-medium">NOM COMPLET : <span class="text-red-500">*</span></label>
                <input id="registerName" type="text" class="w-full px-4 py-2 border rounded bg-gray-100" required autocomplete="name" />
            </div>
            <div>
                <label for="registerEmail" class="block text-sm font-medium">ADRESSE EMAIL : <span class="text-red-500">*</span></label>
                <input id="registerEmail" type="email" class="w-full px-4 py-2 border rounded bg-gray-100" required autocomplete="email" />
            </div>
            <div>
                <label for="registerPassword" class="block text-sm font-medium">MOT DE PASSE : <span class="text-red-500">*</span></label>
                <input id="registerPassword" type="password" class="w-full px-4 py-2 border rounded bg-gray-100" required autocomplete="new-password" />
            </div>
            <div>
                <label for="registerPasswordConfirm" class="block text-sm font-medium">CONFIRMER MOT DE PASSE : <span class="text-red-500">*</span></label>
                <input id="registerPasswordConfirm" type="password" class="w-full px-4 py-2 border rounded bg-gray-100" required autocomplete="new-password" />
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

<script>
    document.getElementById('goToLoginBtn').addEventListener('click', () => {
    registerModal.classList.add('hidden');
    loader.classList.remove('hidden');
    setTimeout(() => {
        loader.classList.add('hidden');
        loginModal.classList.remove('hidden');
    }, 400);
});

</script>

<!-- Script Modal -->
<script>
    const openLoginBtn = document.querySelector('.btn-hover'); // SE CONNECTER
    const loginModal = document.getElementById('loginModal');
    const loader = document.getElementById('loader');
    const closeLoginBtn = document.getElementById('closeModal');

    const openRegisterBtn = document.getElementById('openRegister');
    const registerModal = document.getElementById('registerModal');
    const closeRegisterBtn = document.getElementById('closeRegisterModal');

    openLoginBtn.addEventListener('click', () => {
        loader.classList.remove('hidden');
        setTimeout(() => {
            loader.classList.add('hidden');
            loginModal.classList.remove('hidden');
        }, 400);
    });

    closeLoginBtn.addEventListener('click', () => {
        loginModal.classList.add('hidden');
    });

    closeRegisterBtn.addEventListener('click', () => {
        registerModal.classList.add('hidden');
    });

    openRegisterBtn.addEventListener('click', () => {
        loginModal.classList.add('hidden');
        loader.classList.remove('hidden');
        setTimeout(() => {
            loader.classList.add('hidden');
            registerModal.classList.remove('hidden');
        }, 400);
    });

    window.addEventListener('click', (e) => {
        if (e.target === loginModal) {
            loginModal.classList.add('hidden');
        }
        if (e.target === registerModal) {
            registerModal.classList.add('hidden');
        }
    });
</script>

<!-- Configuration Tailwind Loader animation -->
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
