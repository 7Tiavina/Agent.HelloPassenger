<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de votre commande HelloPassenger</title>
</head>
<body>
    <p>Bonjour {{ $commande->client_prenom }} {{ $commande->client_nom }},</p>

    <p>Nous vous confirmons la bonne réception de votre commande n° <strong>{{ $commande->paymentClient->monetico_order_id ?? $commande->id }}</strong>.</p>

    <p>Vous trouverez ci-joint votre facture au format PDF.</p>

    <p>Détails de votre commande :</p>
    <ul>
        <li>Date de commande : {{ $commande->created_at->format('d/m/Y H:i') }}</li>
        <li>Total : {{ number_format($commande->total_prix_ttc, 2, ',', ' ') }} €</li>
        <!-- Ajoutez d'autres détails si nécessaire -->
    </ul>

    <p>Merci de faire confiance à HelloPassenger.</p>
    <p>Cordialement,</p>
    <p>L'équipe HelloPassenger</p>
</body>
</html>
