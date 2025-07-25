{{-- resources/views/components/users.blade.php --}}
@include('components.header')

<div class="flex">
  @include('components.sidebar')

  <main class="flex-1 p-6">
    <div id="users" data-tab-content class="space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Gestion des utilisateurs</h2>
        <div class="flex items-center gap-3">
          <button
            onclick="exportUsers()"
            class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 text-sm hover:bg-gray-50 flex items-center"
          >
            <i class="fas fa-download mr-2"></i>Exporter
          </button>
          <button
            onclick="createUser()"
            class="px-4 py-2 rounded-md bg-yellow-400 text-gray-800 hover:bg-yellow-500 flex items-center"
          >
            <i class="fas fa-plus mr-2"></i>Nouvel utilisateur
          </button>
        </div>
      </div>

      <!-- Contenu de l'onglet Utilisateurs -->
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm divide-y divide-gray-200">
            <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider text-xs font-medium">
              <tr>
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Nom</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Rôle</th>
                <th class="px-4 py-3">Inscription</th>
                <th class="px-4 py-3">Actions</th>
              </tr>
            </thead>
            <tbody id="users-table" class="bg-white divide-y divide-gray-200">
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
    initUsers();
  });

  function exportUsers() {
    // TODO: appeler la route d’export
    console.log('Export des utilisateurs');
  }

  function createUser() {
    // TODO: rediriger vers formulaire de création
    console.log('Création nouvel utilisateur');
  }

  function initUsers() {
    // TODO: charger et afficher les utilisateurs dans #users-table
  }
</script>
