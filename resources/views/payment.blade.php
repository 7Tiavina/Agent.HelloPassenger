@php
    $commandeData = Session::get('commande_en_cours');
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon-hellopassenger.png') }}">
    <title>Finaliser le Paiement - HelloPassenger</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts Monetico -->
    <script
        src="https://api.gateway.monetico-retail.com/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
        kr-public-key="43559169:testpublickey_TpUnzWl3wta3iKfuUeeYylRCWZ99SwdFKQktpbbxaOdxz"
        kr-post-url-success="{{ route('payment.success') }}"
        kr-post-url-refused="{{ route('payment.error') }}"
        kr-post-url-canceled="{{ route('payment.cancel') }}">
    </script>
    <link rel="stylesheet" href="https://api.gateway.monetico-retail.com/static/js/krypton-client/V4.0/ext/neon-reset.min.css">
    <script src="https://api.gateway.monetico-retail.com/static/js/krypton-client/V4.0/ext/neon.js"></script>
    <!-- Fin Scripts Monetico -->
</head>
<body class="bg-gray-50">

@include('Front.header-front')

<div class="container mx-auto max-w-5xl my-12 px-4">

    <!-- Afficheur de message d'erreur -->
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-bold">Action requise</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if ($commandeData)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Colonne de gauche : Récapitulatif de la commande -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Récapitulatif de votre commande</h1>
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <ul class="divide-y divide-gray-200">
                        @foreach($commandeData['commandeLignes'] as $ligne)
                            <li class="py-4 flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $ligne['libelleProduit'] }}</p>
                                    <p class="text-sm text-gray-500">Quantité : {{ $ligne['quantite'] }}</p>
                                </div>
                                <p class="font-semibold text-gray-800">{{ number_format($ligne['prixTTC'], 2, ',', ' ') }} €</p>
                            </li>
                        @endforeach
                    </ul>
                    <div class="py-4 flex justify-between items-center border-t-2 border-gray-200 mt-4">
                        <p class="text-lg font-bold text-gray-900">Total à payer</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($commandeData['total_prix_ttc'], 2, ',', ' ') }} €</p>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite : Informations et Paiement -->
            <div class="space-y-8">
                <!-- Bloc d'informations client -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Vos informations</h2>
                    <div class="text-sm text-gray-600 space-y-2">
                        <p id="display-user-name"><strong>Nom:</strong> {{ $user->prenom }} {{ $user->nom }}</p>
                        <p id="display-user-email"><strong>Email:</strong> {{ $user->email }}</p>
                        <p id="display-user-phone"><strong>Téléphone:</strong> {{ $user->telephone ?? 'Non renseigné' }}</p>
                        <p id="display-user-address"><strong>Adresse:</strong> {{ $user->adresse ?? 'Non renseignée' }}</p>
                    </div>
                    <button id="openClientProfileModalBtn" class="mt-4 text-sm text-yellow-600 hover:text-yellow-700 font-semibold">Modifier</button>
                </div>

                <!-- Bloc de paiement -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Paiement sécurisé</h2>
                    @if(!session('open_modal'))
                        <div class="kr-smart-form" kr-form-token="{{ $formToken }}"></div>
                    @else
                        <div class="p-4 bg-gray-100 rounded-md text-center text-gray-600">
                            Veuillez compléter votre profil pour activer le paiement.
                        </div>
                    @endif
                </div>

                <!-- Bloc de débogage -->
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                     <details>
                        <summary class="text-sm text-gray-600 cursor-pointer">Aperçu des données de commande (JSON)</summary>
                        <pre class="bg-gray-800 text-white p-4 rounded-md text-xs overflow-x-auto mt-2">{{ json_encode($commandeData, JSON_PRETTY_PRINT) }}</pre>
                    </details>
                </div>
            </div>
        </div>
    @else
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative max-w-lg mx-auto" role="alert">
            <strong class="font-bold">Erreur!</strong>
            <span class="block sm:inline">Aucune donnée de commande trouvée. Votre session a peut-être expiré.</span>
            <a href="{{ route('form-consigne') }}" class="mt-4 inline-block bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Retour au formulaire
            </a>
        </div>
    @endif
</div>

@include('Front.footer-front')

@include('components.client-profile-modal')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clientProfileModal = document.getElementById('clientProfileModal');
        const openClientProfileModalBtn = document.getElementById('openClientProfileModalBtn');
        const closeClientProfileModalBtn = document.getElementById('closeClientProfileModalBtn');
        const clientProfileForm = document.getElementById('clientProfileForm');

        const userData = @json($user);

        openClientProfileModalBtn.addEventListener('click', () => {
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

            for (const key in data) {
                if (data[key] === '') {
                    data[key] = null;
                }
            }

            try {
                const response = await fetch('{{ route("client.update-profile") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    alert('Informations mises à jour avec succès! La page va se recharger pour prendre en compte les changements.');
                    location.reload(); // Recharger pour que le controleur refasse la vérification
                } else {
                    alert('Erreur lors de la mise à jour: ' + (result.message || 'Erreur inconnue'));
                    console.error('Update error:', result);
                }
            } catch (error) {
                alert('Une erreur réseau est survenue.');
                console.error('Network error:', error);
            }
        });

        // Auto-ouverture de la modale si demandé par le serveur
        @if(session('open_modal'))
            openClientProfileModalBtn.click();
        @endif
    });
</script>

</body>
</html>