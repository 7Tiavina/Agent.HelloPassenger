<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $commande->id_api_commande ?? $commande->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto my-10 p-8 bg-white shadow-lg">
        <!-- En-tête -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">FACTURE</h1>
                <p class="text-gray-500">Référence: {{ $commande->paymentClient->monetico_order_id ?? $commande->id }}</p>
                <p class="text-gray-500">Date: {{ $commande->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-semibold text-gray-700">HelloPassenger</h2>
                <p class="text-gray-500">Service Consigne Bagages</p>
                <p class="text-gray-500">contact@hellopassenger.com</p>
            </div>
        </div>

        <!-- Informations Client -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold border-b pb-2 mb-2">Facturé à :</h3>
            <p class="font-bold">{{ $commande->client_prenom }} {{ $commande->client_nom }}</p>
            <p>{{ $commande->client_adresse }}</p>
            <p>{{ $commande->client_code_postal }} {{ $commande->client_ville }}</p>
            <p>{{ $commande->client_pays }}</p>
            <p>Email: {{ $commande->client_email }}</p>
            @if($commande->client_telephone)
                <p>Téléphone: {{ $commande->client_telephone }}</p>
            @endif
            @if($commande->client_nom_societe)
                <p>Société: {{ $commande->client_nom_societe }}</p>
            @endif
        </div>

        <!-- Lignes de la commande -->
        <table class="w-full mb-8">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-2 font-semibold">Description</th>
                    <th class="p-2 font-semibold text-center">Quantité</th>
                    <th class="p-2 font-semibold text-right">Prix Unitaire</th>
                    <th class="p-2 font-semibold text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $details = json_decode($commande->details_commande_lignes, true);
                @endphp
                @foreach($details as $item)
                    <tr class="border-b">
                        <td class="p-2">
                            {{ $item['libelleProduit'] }}
                            @if(isset($item['idLieu']) && $item['idLieu'])
                                <span class="text-sm text-gray-500 block">Lieu: {{ $item['idLieu'] }}</span>
                            @endif
                             @if(isset($item['informationsComplementaires']) && $item['informationsComplementaires'])
                                <span class="text-sm text-gray-500 block">Infos: {{ $item['informationsComplementaires'] }}</span>
                            @endif
                        </td>
                        <td class="p-2 text-center">{{ $item['quantite'] }}</td>
                        <td class="p-2 text-right">{{ number_format($item['prixTTC'] / $item['quantite'], 2, ',', ' ') }} €</td>
                        <td class="p-2 text-right">{{ number_format($item['prixTTC'], 2, ',', ' ') }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total -->
        <div class="flex justify-end">
            <div class="w-full md:w-1/3">
                <div class="flex justify-between py-2">
                    <span class="font-semibold text-gray-600">Sous-total</span>
                    <span>{{ number_format($commande->total_prix_ttc, 2, ',', ' ') }} €</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="font-semibold text-gray-600">TVA (0%)</span>
                    <span>0,00 €</span>
                </div>
                <div class="flex justify-between py-2 border-t-2 border-gray-300 mt-2">
                    <span class="font-bold text-xl">TOTAL</span>
                    <span class="font-bold text-xl">{{ number_format($commande->total_prix_ttc, 2, ',', ' ') }} €</span>
                </div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="mt-16 text-center text-sm text-gray-500">
            <p>Merci pour votre confiance.</p>
            <p>HelloPassenger - SAS au capital de 1000€ - SIRET 123456789</p>
        </div>
    </div>
</body>
</html>
