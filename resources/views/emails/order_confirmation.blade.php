<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de votre commande HelloPassenger</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 90%; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        h1 { color: #1f2937; }
        strong { color: #FFC107; }
        .footer { margin-top: 20px; font-size: 0.9em; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Votre réservation est confirmée !</h1>
        
        <p>Bonjour {{ $commande->client_prenom }} {{ $commande->client_nom }},</p>

        <p>Nous vous remercions chaleureusement pour votre confiance et nous avons le plaisir de vous confirmer la bonne réception de votre commande n° <strong>{{ $commande->paymentClient->monetico_order_id ?? $commande->id }}</strong>.</p>

        <p>Vous trouverez en pièce jointe votre facture détaillée au format PDF.</p>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <h2>Détails de votre commande :</h2>
        <ul>
            <li><strong>Date de commande :</strong> {{ $commande->created_at->format('d/m/Y H:i') }}</li>
            <li><strong>Total payé :</strong> {{ number_format($commande->total_prix_ttc, 2, ',', ' ') }} €</li>
            <!-- Vous pouvez ajouter d'autres détails ici si vous le souhaitez -->
        </ul>

        <p>Toute l'équipe de HelloPassenger vous souhaite un excellent voyage !</p>
        
        <div class="footer">
            <p>Cordialement,</p>
            <p>L'équipe HelloPassenger</p>
        </div>
    </div>
</body>
</html>
