{{-- resources/views/components/orders.blade.php --}}
@include('components.header')

<div class="flex">
  @include('components.sidebar')

  <main class="flex-1 p-6">
    <div id="orders" data-tab-content class="space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Gestion des commandes</h2>
        <div class="flex items-center gap-3">
          <button
            onclick="exportOrders()"
            class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 text-sm hover:bg-gray-50 flex items-center"
          >
            <i class="fas fa-download mr-2"></i>Exporter
          </button>
          <button
            onclick="createOrder()"
            class="px-4 py-2 rounded-md bg-yellow-400 text-gray-800 hover:bg-yellow-500 flex items-center"
          >
            <i class="fas fa-plus mr-2"></i>Nouvelle commande
          </button>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Terminees -->
        <div class="bg-white rounded-lg shadow-sm p-4 flex items-center gap-3">
          <i class="fas fa-check-circle text-green-600 text-2xl"></i>
          <div>
            <p class="text-sm text-gray-600">Terminées</p>
            <p class="text-xl text-gray-800">156</p>
          </div>
        </div>
        <!-- En cours -->
        <div class="bg-white rounded-lg shadow-sm p-4 flex items-center gap-3">
          <i class="fas fa-clock text-orange-600 text-2xl"></i>
          <div>
            <p class="text-sm text-gray-600">En cours</p>
            <p class="text-xl text-gray-800">24</p>
          </div>
        </div>
        <!-- En attente -->
        <div class="bg-white rounded-lg shadow-sm p-4 flex items-center gap-3">
          <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
          <div>
            <p class="text-sm text-gray-600">En attente</p>
            <p class="text-xl text-gray-800">8</p>
          </div>
        </div>
        <!-- Revenus jour -->
        <div class="bg-white rounded-lg shadow-sm p-4 flex items-center gap-3">
          <i class="fas fa-euro-sign text-blue-600 text-2xl"></i>
          <div>
            <p class="text-sm text-gray-600">Revenus jour</p>
            <p class="text-xl text-gray-800">€450</p>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex flex-wrap items-center gap-4">
          <div class="relative flex-1 min-w-[200px]">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input
              type="text"
              placeholder="Rechercher une commande..."
              class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400"
              oninput="filterOrders(this.value)"
            >
          </div>
          <select
            class="w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400"
            onchange="filterByStatus(this.value)"
          >
            <option value="">Filtrer par statut</option>
            <option value="active">Actif</option>
            <option value="completed">Terminé</option>
            <option value="pending">En attente</option>
          </select>
          <select
            class="w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400"
            onchange="filterByService(this.value)"
          >
            <option value="">Service</option>
            <option value="consigne">Consigne</option>
            <option value="transfert">Transfert</option>
          </select>
          <button
            onclick="applyFilters()"
            class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 flex items-center"
          >
            <i class="fas fa-filter mr-2"></i>Filtres
          </button>
        </div>
      </div>

      <!-- Table des commandes -->
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm divide-y divide-gray-200">
            <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider text-xs font-medium">
              <tr>
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Client</th>
                <th class="px-4 py-3">Service</th>
                <th class="px-4 py-3">Localisation</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Prix</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3">Actions</th>
              </tr>
            </thead>
            <tbody id="orders-table" class="bg-white divide-y divide-gray-200">
              <!-- Lignes générées dynamiquement -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    initOrders();
  });

  function exportOrders() {
    // TODO: déclencher export côté controller
  }

  function createOrder() {
    // TODO: rediriger vers formulaire de création
  }

  function filterOrders(query) {
    // TODO
  }
  function filterByStatus(status) {
    // TODO
  }
  function filterByService(service) {
    // TODO
  }
  function applyFilters() {
    // TODO
  }

  function initOrders() {
    // TODO: générer stats et ligne de commandes
  }
</script>
