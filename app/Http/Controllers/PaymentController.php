<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Commande;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    /**
     * Prépare les données de la commande et les stocke en session avant la redirection vers la page de paiement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function preparePayment(Request $request)
    {
        // Valider les données reçues du formulaire
        $validatedData = $request->validate([
            'airportId' => 'required|string',
            'dateDepot' => 'required|date',
            'heureDepot' => 'required|string',
            'dateRecuperation' => 'required|date',
            'heureRecuperation' => 'required|string',
            'baggages' => 'required|array',
            'baggages.*.type' => 'required|string',
            'baggages.*.quantity' => 'required|integer|min:1',
            'products' => 'required|array',
        ]);

        // Construire les commandeLignes pour l'API externe
        $commandeLignes = [];

        // Mapping for baggage types to product libelles
        $baggageTypeToLibelleMap = [
            'cabin' => 'Bagage en cabine',
            'soute' => 'Bagage en soute',
            'vestiaire' => 'Vestiaire',
            // Add other mappings as needed
        ];

        foreach ($validatedData['baggages'] as $baggage) {
            $productPrice = 0;
            $productId = null;
            $productLibelle = null;
            $serviceId = 'dfb8ac1b-8bb1-4957-afb4-1faedaf641b7';

            // Get the expected libelle from the map
            $expectedLibelle = $baggageTypeToLibelleMap[$baggage['type']] ?? null;

            if (is_null($expectedLibelle)) {
                throw new \Exception('Unknown baggage type: ' . $baggage['type']);
            }

            foreach ($validatedData['products'] as $product) {
                if ($product['libelle'] === $expectedLibelle) { // <--- Modified comparison
                    $productPrice = $product['prixUnitaire'];
                    $productId = $product['id'];
                    $productLibelle = $product['libelle'];
                    break;
                }
            }

            if (is_null($productId) || is_null($productLibelle)) {
                throw new \Exception('Product details not found for expected libelle: ' . $expectedLibelle);
            }

            $commandeLignes[] = [
                "idProduit" => $productId,
                "idService" => $serviceId,
                "dateDebut" => $validatedData['dateDepot'] . 'T' . $validatedData['heureDepot'] . ':00.000Z',
                "dateFin" => $validatedData['dateRecuperation'] . 'T' . $validatedData['heureRecuperation'] . ':00.000Z',
                "prixTTC" => ($productPrice * $baggage['quantity']),
                "quantite" => $baggage['quantity'],
                "libelleProduit" => $productLibelle
            ];
        }

        // Récupérer les informations du client connecté (modèle Client)
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour continuer.');
        }
        $clientData = [
            "email" => $user->email,
            "telephone" => $user->telephone, // Utilisation directe du champ telephone du modèle Client
            "nom" => $user->nom, // Utilisation directe du champ nom du modèle Client
            "prenom" => $user->prenom, // Utilisation directe du champ prenom du modèle Client
            "civilite" => "M.", // À adapter si vous avez ce champ dans le formulaire
            "nomSociete" => null,
            "adresse" => "123 Rue de l'Exemple", // À adapter si vous avez ce champ dans le formulaire
            "complementAdresse" => null,
            "ville" => "Paris", // À adapter si vous avez ce champ dans le formulaire
            "codePostal" => "75001", // À adapter si vous avez ce champ dans le formulaire
            "pays" => "France"
        ];

        $commandeData = [
            'idPlateforme' => $validatedData['airportId'],
            'commandeLignes' => $commandeLignes,
            'client' => $clientData,
            'total_prix_ttc' => array_reduce($commandeLignes, function ($sum, $item) {
                return $sum + $item['prixTTC']; // Ici, on somme directement le prixTTC déjà calculé
            }, 0),
        ];

        // Stocker les données de la commande en session
        Session::put('commande_en_cours', $commandeData);

        // Rediriger vers la page de paiement
        return redirect()->route('payment');
    }

    /**
     * Traite le paiement simulé et enregistre la commande.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayment(Request $request)
    {
        // Récupérer les données de la commande depuis la session
        $commandeData = Session::get('commande_en_cours');

        if (!$commandeData) {
            return redirect()->route('form-consigne')->with('error', 'Aucune commande en cours. Veuillez recommencer.');
        }

        $idPlateforme = $commandeData['idPlateforme'];
        $commandeLignes = $commandeData['commandeLignes'];
        $clientData = $commandeData['client'];
        $totalPrixTTC = $commandeData['total_prix_ttc'];

        try {
            // Appel à l'API externe
            $response = Http::post("https://api.example.com/plateforme/{$idPlateforme}/commande", [
                'commandeLignes' => $commandeLignes,
                'client' => $clientData,
                'totalPrixTTC' => $totalPrixTTC
            ]);

            $apiResult = $response->json();

            if ($response->successful() && $apiResult['statut'] === 1) {
                // Succès de l'API externe, enregistrer dans notre base de données
                $commande = Commande::create([
                    'user_id' => Auth::id(),
                    'id_api_commande' => $apiResult['content'] ?? null,
                    'id_plateforme' => $idPlateforme,
                    'client_email' => $clientData['email'],
                    'client_telephone' => $clientData['telephone'],
                    'client_nom' => $clientData['nom'],
                    'client_prenom' => $clientData['prenom'],
                    'client_civilite' => $clientData['civilite'],
                    'client_nom_societe' => $clientData['nomSociete'],
                    'client_adresse' => $clientData['adresse'],
                    'client_complement_adresse' => $clientData['complementAdresse'],
                    'client_ville' => $clientData['ville'],
                    'client_codePostal' => $clientData['codePostal'],
                    'client_pays' => $clientData['pays'],
                    'total_prix_ttc' => $totalPrixTTC,
                    'statut' => 'completed',
                    'details_commande_lignes' => $commandeLignes,
                ]);

                Session::forget('commande_en_cours');
                return redirect()->route('mes.reservations')->with('success', 'Votre commande a été passée avec succès !');

            } else {
                $errorMessage = $apiResult['message'] ?? 'Erreur inconnue lors de la commande via l\'API externe.';
                return back()->with('error', 'Échec de la commande : ' . $errorMessage);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur technique est survenue : ' . $e->getMessage());
        }
    }
}
