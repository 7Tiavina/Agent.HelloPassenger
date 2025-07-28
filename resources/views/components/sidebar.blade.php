<aside class="w-64 bg-white border-r sidebar">
  <nav class="p-6">
    <div class="space-y-2">
      <a href="{{ route('overview') }}"
         class="w-full flex items-center px-4 py-2 rounded-md {{ request()->routeIs('overview') ? 'bg-yellow-400 text-gray-800 hover:bg-yellow-500' : 'hover:bg-gray-100 text-gray-700' }}">
        <i class="fas fa-chart-line mr-3"></i>
        Vue d'ensemble
      </a>

      <a href="{{ route('orders') }}"
         class="w-full flex items-center px-4 py-2 rounded-md {{ request()->routeIs('orders') ? 'bg-yellow-400 text-gray-800 hover:bg-yellow-500' : 'hover:bg-gray-100 text-gray-700' }}">
        <i class="fas fa-box mr-3"></i>
        Commandes
      </a>

      <a href="{{ route('users') }}"
         class="w-full flex items-center px-4 py-2 rounded-md {{ request()->routeIs('users') ? 'bg-yellow-400 text-gray-800 hover:bg-yellow-500' : 'hover:bg-gray-100 text-gray-700' }}">
        <i class="fas fa-users mr-3"></i>
        Utilisateurs
      </a>

      <a href="{{ route('chat') }}"
         class="w-full flex items-center px-4 py-2 rounded-md {{ request()->routeIs('chat') ? 'bg-yellow-400 text-gray-800 hover:bg-yellow-500' : 'hover:bg-gray-100 text-gray-700' }}">
        <i class="fas fa-comments mr-3"></i>
        Chat
      </a>


      <a href="{{ route('analytics') }}"
         class="w-full flex items-center px-4 py-2 rounded-md {{ request()->routeIs('analytics') ? 'bg-yellow-400 text-gray-800 hover:bg-yellow-500' : 'hover:bg-gray-100 text-gray-700' }}">
        <i class="fas fa-chart-bar mr-3"></i>
        Analytiques
      </a>
    </div>

    <!-- Live Notifications -->
    <div class="mt-8">
      <p class="text-xs text-gray-500 mb-3 uppercase tracking-wider">Notifications</p>
      <div class="space-y-3">
        <div class="text-xs p-2 bg-gray-50 rounded-lg">
          <p class="text-gray-800 mb-1">Nouvelle réservation de Marie Dubois</p>
          <p class="text-gray-500">Il y a 5 min</p>
        </div>
        <div class="text-xs p-2 bg-gray-50 rounded-lg">
          <p class="text-gray-800 mb-1">Consigne #47 bientôt libérée</p>
          <p class="text-gray-500">Il y a 15 min</p>
        </div>
        <div class="text-xs p-2 bg-gray-50 rounded-lg">
          <p class="text-gray-800 mb-1">Paiement reçu - €45</p>
          <p class="text-gray-500">Il y a 30 min</p>
        </div>
      </div>
    </div>
  </nav>
</aside>
