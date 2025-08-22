<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Added CSRF token -->
    <title>Page de Paiement - HelloPassenger</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6">Page de Paiement</h1>

        <p class="mb-4">Ceci est la page de paiement simulée. Veuillez confirmer les détails de votre commande avant de procéder.</p>

        @php
            $commandeData = Session::get('commande_en_cours');
        @endphp

        @if ($commandeData)
            <h2 class="text-xl font-semibold mb-3">Détails de la commande :</h2>
            <button id="openClientProfileModalBtn" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded text-sm mb-4">
                Mettre à jour mes informations
            </button>
            <pre class="bg-gray-100 p-4 rounded-md text-sm overflow-x-auto mb-6">{{ json_encode($commandeData, JSON_PRETTY_PRINT) }}</pre>

            <form action="{{ route('process.payment') }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Confirmer le paiement (simulé)
                </button>
            </form>
        @else
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Erreur!</strong>
                <span class="block sm:inline">Aucune donnée de commande trouvée. Veuillez retourner au formulaire.</span>
            </div>
            <a href="{{ route('form-consigne') }}" class="mt-4 inline-block bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Retour au formulaire
            </a>
        @endif
    </div>
@include('components.client-profile-modal')

    <script>
        const clientProfileModal = document.getElementById('clientProfileModal');
        const openClientProfileModalBtn = document.getElementById('openClientProfileModalBtn');
        const closeClientProfileModalBtn = document.getElementById('closeClientProfileModalBtn');
        const clientProfileForm = document.getElementById('clientProfileForm');

        // Get user data from Laravel Blade (passed from controller)
        const userData = @json($user);

        openClientProfileModalBtn.addEventListener('click', () => {
            // Populate form fields with current user data
            document.getElementById('modal-email').value = userData.email || '';
            document.getElementById('modal-telephone').value = userData.telephone || '';
            document.getElementById('modal-civilite').value = userData.civilite || 'M.';
            document.getElementById('modal-nomSociete').value = userData.nomSociete || '';
            document.getElementById('modal-adresse').value = userData.adresse || '';
            document.getElementById('modal-complementAdresse').value = userData.complementAdresse || '';
            document.getElementById('modal-ville').value = userData.ville || '';
            document.getElementById('modal-codePostal').value = userData.codePostal || '';
            document.getElementById('modal-pays').value = userData.pays || '';

            clientProfileModal.classList.remove('hidden');
        });

        closeClientProfileModalBtn.addEventListener('click', () => {
            clientProfileModal.classList.add('hidden');
        });

        clientProfileForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(clientProfileForm);
            const data = Object.fromEntries(formData.entries());

            // Convert empty strings to null for fields that can be null
            for (const key in data) {
                if (data[key] === '') {
                    data[key] = null;
                }
            }

            try {
                const response = await fetch('/client/update-profile', { // Changed URL
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Informations mises à jour avec succès!');
                    clientProfileModal.classList.add('hidden');
                    // Redirect to the payment page to ensure updated client info is loaded into session
                    window.location.href = '{{ route('payment') }}';
                } else {
                    alert('Erreur lors de la mise à jour: ' + (result.message || 'Erreur inconnue'));
                    console.error('Update error:', result);
                }
            } catch (error) {
                alert('Une erreur réseau est survenue.');
                console.error('Network error:', error);
            }
        });
    </script>
</body>
</html>