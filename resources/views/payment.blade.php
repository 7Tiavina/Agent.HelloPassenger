@php Log::info('[VIEW] Rendering payment.blade.php START'); @endphp
@php
    // Récupérer les données de la commande depuis la session
    $commandeData = Session::get('commande_en_cours');
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Paiement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Etape 1: Charger la librairie JS de Monetico et la clé publique -->
    <script
        src="https://api.gateway.monetico-retail.com/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
        kr-public-key="43559169:testpublickey_TpUnzWl3wta3iKfuUeeYylRCWZ99SwdFKQktpbbxaOdxz"
        kr-post-url-success="{{ route('payment.success') }}"
        kr-post-url-refused="{{ route('payment.error') }}"
        kr-post-url-canceled="{{ route('payment.cancel') }}">
    </script>

    <!-- Etape 2: Charger un thème (optionnel mais recommandé) -->
    <link rel="stylesheet" href="https://api.gateway.monetico-retail.com/static/js/krypton-client/V4.0/ext/neon-reset.min.css">
    <script src="https://api.gateway.monetico-retail.com/static/js/krypton-client/V4.0/ext/neon.js"></script>

</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 order-md-2 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Votre Commande</span>
                    <span class="badge bg-secondary rounded-pill">{{ count($commandeData['commandeLignes']) }}</span>
                </h4>
                <ul class="list-group mb-3">
                    @foreach($commandeData['commandeLignes'] as $ligne)
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">{{ $ligne['libelleProduit'] }}</h6>
                                <small class="text-muted">Quantité: {{ $ligne['quantite'] }}</small>
                            </div>
                            <span class="text-muted">{{ number_format($ligne['prixTTC'], 2, ',', ' ') }} €</span>
                        </li>
                    @endforeach
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Total (EUR)</span>
                        <strong>{{ number_format($commandeData['total_prix_ttc'], 2, ',', ' ') }} €</strong>
                    </li>
                </ul>
            </div>

            <div class="col-md-6 order-md-1">
                <h4 class="mb-3">Finaliser le paiement</h4>
                <p>Veuillez renseigner vos informations de paiement ci-dessous.</p>
                
                <!-- Etape 3: Le conteneur du formulaire de paiement -->
                <!-- Le `formToken` est passé par le contrôleur et injecté ici -->
                <div class="kr-smart-form" kr-form-token="{{ $formToken }}"></div>

            </div>
        </div>

        <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">&copy; 2025 HelloPassenger</p>
        </footer>
    </div>
</body>
</html>