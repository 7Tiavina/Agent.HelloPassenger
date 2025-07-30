@include('components.header')

<style>[x-cloak] { display: none !important; }</style>

<div class="flex">
  @include('components.sidebar')

  <main class="flex-1 p-6" x-data="{ openModal: false, selectedHistory: null }" x-cloak>
    <h2 class="text-2xl font-bold mb-4">Mes collectes de bagages</h2>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
      <table class="min-w-full text-sm divide-y divide-gray-200">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
          <tr>
            <th class="px-4 py-3 text-left">Référence</th>
            <th class="px-4 py-3 text-left">Client</th>
            <th class="px-4 py-3 text-left">Statut</th>
            <th class="px-4 py-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          @foreach ($reservations as $reservation)
            <tr>
              <td class="px-4 py-3 font-medium">{{ $reservation->ref }}</td>
              <td class="px-4 py-3">{{ $reservation->user->email }}</td>
              <td class="px-4 py-3 capitalize">{{ $reservation->status }}</td>
              <td class="px-4 py-3">
                <button
                  class="text-blue-600 hover:underline font-semibold"
                  @click='selectedHistory = @json($reservation); openModal = true'
                >
                  Voir
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- MODAL --}}
    <div
      x-show="openModal && selectedHistory && selectedHistory.histories"
      x-transition
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4"
    >
      <div class="bg-white w-full max-w-2xl p-6 rounded-xl shadow-xl relative overflow-hidden">
        <h3 class="text-2xl font-bold mb-4 text-gray-800">Historique du bagage</h3>

        <template x-if="selectedHistory">
          <div class="space-y-4 max-h-[420px] overflow-y-auto pr-2">
            <template x-for="history in selectedHistory.histories" :key="history.id">
              <div class="p-4 rounded-lg border border-gray-200 bg-gray-50 space-y-2">
                <p class="text-sm"><strong>Statut :</strong> <span x-text="history.status"></span></p>
                <p class="text-sm"><strong>Agent :</strong> <span x-text="history.agent?.email ?? 'Inconnu'"></span></p>
                <p class="text-sm"><strong>Date :</strong> <span x-text="new Date(history.timestamp).toLocaleString()"></span></p>

                <template x-if="history.photo_url">
                  <div class="pt-2">
                    <img :src="'{{ asset('') }}' + history.photo_url.replace(/^\/+/, '')" class="w-40 rounded shadow-md border">
                  </div>
                </template>
              </div>
            </template>
          </div>
        </template>

        <button
          @click="openModal = false"
          class="mt-6 w-full py-2 bg-yellow-500 text-white font-semibold rounded-lg hover:bg-yellow-600"
        >
          Fermer
        </button>
      </div>
    </div>
  </main>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
