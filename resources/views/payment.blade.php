<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
</body>
</html>