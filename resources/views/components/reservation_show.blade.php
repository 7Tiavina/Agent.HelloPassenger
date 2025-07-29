@include('components.header')

<div class="flex">
  @include('components.sidebar')

  <main class="flex-1 p-6">
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
      <h2 class="text-2xl font-semibold">Détails de la réservation</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <p><strong>Référence :</strong> {{ $reservation->ref }}</p>
          <p><strong>Client :</strong> {{ $reservation->user->email }}</p>
          <p><strong>Départ :</strong> {{ $reservation->departure }}</p>
          <p><strong>Arrivée :</strong> {{ $reservation->arrival }}</p>
        </div>
        <div>
          <p><strong>Date de dépôt :</strong> {{ $reservation->collect_date }}</p>
          <p><strong>Date de retrait :</strong> {{ $reservation->deliver_date }}</p>
          <p><strong>Statut :</strong> {{ ucfirst($reservation->status) }}</p>
          <p><strong>Créée le :</strong> {{ $reservation->created_at->format('d/m/Y H:i') }}</p>
        </div>
      </div>
      <div class="mt-6">
        <a href="{{ route('orders') }}"
           class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
          ← Retour aux commandes
        </a>
      </div>
    </div>
  </main>
</div>
