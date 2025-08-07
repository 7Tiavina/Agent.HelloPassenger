<!-- Header -->
<header class="bg-yellow-custom px-6 py-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                <span class="text-yellow-custom text-sm font-bold">H</span>
            </div>
            <span class="text-black font-semibold text-lg">HelloPassenger</span>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Bouton Admin discret -->
            <a href="{{ route('login') }}" class="text-gray-600 hover:text-black text-xs font-medium transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                <span class="sr-only">Admin</span>
            </a>
            
            <button class="bg-gray-dark text-white px-6 py-2 rounded-full text-sm font-medium hover:bg-gray-700 transition-colors btn-hover">
                SE CONNECTER
            </button>
            <button class="bg-gray-dark text-white px-6 py-2 rounded-full text-sm font-medium hover:bg-gray-700 transition-colors btn-hover">
                MES RÉSERVATIONS
            </button>
            <div class="flex items-center space-x-2 cursor-pointer">
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