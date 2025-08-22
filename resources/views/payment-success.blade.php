<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Paiement - HelloPassenger</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md text-center">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Succès!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Erreur!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <h1 class="text-2xl font-bold mb-4">Confirmation de Paiement</h1>

        @if ($apiResult && $apiResult['statut'] === 1)
            <p class="text-green-600 text-lg mb-2">Votre commande a été passée avec succès !</p>
            <p class="text-gray-700 mb-4">Référence de la commande API : <span class="font-semibold">{{ $apiResult['message'] ?? 'N/A' }}</span></p>

            @if ($apiResult['content'])
                <h2 class="text-xl font-semibold mt-6 mb-2">Facture (Base64) :</h2>
                <textarea class="w-full h-48 bg-gray-100 p-4 rounded-md text-sm overflow-auto font-mono" readonly>{{ $apiResult['content'] }}</textarea>
                <p class="text-sm text-gray-600 mt-2">Vous pouvez décoder ce contenu Base64 pour obtenir la facture.</p>
            @endif

        @else
            <p class="text-red-600 text-lg mb-2">Échec de la commande.</p>
            <p class="text-gray-700 mb-4">Message d'erreur : <span class="font-semibold">{{ $apiResult['message'] ?? 'Erreur inconnue' }}</span></p>
        @endif

        <a href="{{ route('mes.reservations') }}" class="mt-6 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Voir mes réservations
        </a>
        <a href="{{ route('form-consigne') }}" class="mt-4 inline-block bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            Nouvelle commande
        </a>
    </div>
</body>
</html>