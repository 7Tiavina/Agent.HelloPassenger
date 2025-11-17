@php
    // Il est préférable de ne pas avoir de logique complexe dans la vue.
    // Le contenu de la facture est dans $apiResult['content']
    // L'ID de la commande pour le téléchargement est dans $lastCommandeId
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon-hellopassenger.png') }}">
    <title>Paiement Réussi - HelloPassenger</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

@include('Front.header-front')

<div class="container mx-auto max-w-4xl my-12 px-4">
    <div class="bg-white p-8 rounded-lg shadow-lg text-center border border-gray-200">
        
        <!-- Icône de succès -->
        <div class="mx-auto bg-green-100 rounded-full h-16 w-16 flex items-center justify-center mb-4">
            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-2">Paiement réussi !</h1>
        <p class="text-gray-600 mb-6">Votre commande a été confirmée et votre facture a été générée.</p>

        <!-- Boutons d'action -->
        <div class="flex justify-center space-x-4 mb-8">
            <a href="{{ route('invoices.show', ['id' => $lastCommandeId]) }}" target="_blank"
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                Voir ma facture
            </a>
        </div>

        <!-- Aperçu de la facture -->
        <div class="bg-gray-100 p-4 rounded-lg border border-gray-200">
            <h2 class="text-xl font-semibold text-left mb-4">Aperçu de la facture</h2>
            <div class="w-full h-[600px] border rounded-md bg-white">
                <iframe src="{{ route('invoices.show', ['id' => $lastCommandeId]) }}" class="w-full h-full" frameborder="0"></iframe>
            </div>
        </div>

    </div>
</div>

@include('Front.footer-front')

</body>
</html>
