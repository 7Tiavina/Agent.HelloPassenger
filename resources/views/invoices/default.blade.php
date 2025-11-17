<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $commande->id_api_commande ?? $commande->id }}</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact; /* Pour les fonds et couleurs d'arrière-plan */
            background-color: #f8f8f8; /* Couleur de fond légère pour le corps */
        }
        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #eee;
        }
        .header-table, .total-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .header-table td {
            padding: 0;
            vertical-align: top;
        }
        .header-table .left-col {
            width: 50%;
            text-align: left;
        }
        .header-table .right-col {
            width: 50%;
            text-align: right;
        }
        h1 {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        h2 {
            font-size: 22px;
            font-weight: 600;
            color: #444;
            margin-bottom: 5px;
        }
        p {
            font-size: 14px;
            color: #555;
            line-height: 1.5;
            margin-bottom: 3px;
        }
        .client-info {
            margin-bottom: 30px;
        }
        .client-info h3 {
            font-size: 16px;
            font-weight: 600;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table th, table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        table th {
            background-color: #f5f5f5;
            text-align: left;
            font-weight: 600;
            color: #444;
        }
        table td {
            color: #555;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-semibold { font-weight: 600; }
        .text-xl { font-size: 18px; }
        .text-sm { font-size: 12px; }
        .text-gray-800 { color: #333; }
        .text-gray-700 { color: #444; }
        .text-gray-600 { color: #555; }
        .text-gray-500 { color: #777; }
        .bg-gray-200 { background-color: #eee; }
        .border-b { border-bottom: 1px solid #eee; }
        .border-t-2 { border-top: 2px solid #ccc; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .mt-16 { margin-top: 64px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-8 { margin-bottom: 32px; }
        .pb-2 { padding-bottom: 8px; }
        .py-2 { padding-top: 8px; padding-bottom: 8px; }
        .total-table td {
            padding: 8px 0;
        }
        .total-table .label {
            font-weight: 600;
            color: #555;
        }
        .total-table .value {
            text-align: right;
        }
        .total-table .grand-total {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #ccc;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer-section {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- En-tête -->
        <table class="header-table">
            <tr>
                <td class="left-col">
                    <h1>FACTURE</h1>
                    <p>Référence: {{ $commande->paymentClient->monetico_order_id ?? $commande->id }}</p>
                    <p>Date: {{ $commande->created_at->format('d/m/Y') }}</p>
                </td>
                <td class="right-col">
                    <h2>HelloPassenger</h2>
                    <p>Service Consigne Bagages</p>
                    <p>contact@hellopassenger.com</p>
                </td>
            </tr>
        </table>

        <!-- Informations Client -->
        <div class="client-info">
            <h3>Facturé à :</h3>
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
        <table>
            <thead>
                <tr class="bg-gray-200">
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
        <table class="total-table">
            <tr>
                <td style="width: 67%;"></td> <!-- Colonne vide pour aligner à droite -->
                <td style="width: 33%;">
                    <table>
                        <tr>
                            <td class="label">Sous-total</td>
                            <td class="value">{{ number_format($commande->total_prix_ttc, 2, ',', ' ') }} €</td>
                        </tr>
                        <tr>
                            <td class="label">TVA (0%)</td>
                            <td class="value">0,00 €</td>
                        </tr>
                        <tr class="grand-total">
                            <td class="label">TOTAL</td>
                            <td class="value">{{ number_format($commande->total_prix_ttc, 2, ',', ' ') }} €</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Pied de page -->
        <div class="footer-section">
            <p>Merci pour votre confiance.</p>
            <p>HelloPassenger - SAS au capital de 1000€ - SIRET 123456789</p>
        </div>
    </div>
</body>
</html>
