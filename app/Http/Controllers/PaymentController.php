<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Commande;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; // Added

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
        Log::info('Entering preparePayment method.'); // Added very early log
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

        Log::info('Received validated data for payment preparation: ' . json_encode($validatedData));

        // Construire les commandeLignes pour l'API externe
        $commandeLignes = [];

        // Mapping for baggage types to product libelles
        $baggageTypeToLibelleMap = [
            'cabin' => 'Bagage cabine', // Corrected to match API response
            'soute' => 'Bagage soute',   // Corrected to match API response
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
        $user = Auth::guard('client')->user(); // Ensure we get the Client model
        if (!$user) {
            return redirect()->route('client.login')->with('error', 'Veuillez vous connecter pour continuer.');
        }
        // Re-fetch client data from database to ensure it's the latest
        $user = \App\Models\Client::find($user->id);
        if (!$user) {
            return redirect()->route('client.login')->with('error', 'Client introuvable après authentification.');
        }
        $clientData = [
            "email" => $user->email,
            "telephone" => $user->telephone, // Utilisation directe du champ telephone du modèle Client
            "nom" => $user->nom, // Utilisation directe du champ nom du modèle Client
            "prenom" => $user->prenom, // Utilisation directe du champ prenom du modèle Client
            "civilite" => $user->civilite ?? null, // À adapter si vous avez ce champ dans le formulaire
            "nomSociete" => null,
            "adresse" => $user->adresse ?? null, // À adapter si vous avez ce champ dans le formulaire
            "complementAdresse" => null,
            "ville" => $user->ville ?? null, // À adapter si vous avez ce champ dans le formulaire
            "codePostal" => $user->codePostal ?? null, // À adapter si vous avez ce champ dans le formulaire
            "pays" => $user->pays ?? null
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
        Log::info('Commande data stored in session: ' . json_encode($commandeData)); // Added log

        // Rediriger vers la page de paiement
        return redirect()->route('payment');
    }

    private function getBdmToken(): string
    {
        // Tente de récupérer le token depuis le cache
        return Cache::remember('bdm_api_token', 3300, function () {
            Log::info('Cache BDM token expiré. Demande d\'un nouveau token.');

            $response = Http::post(config('services.bdm.base_url') . '/User/Login', [
                'userName' => config('services.bdm.username'),
                'email' => config('services.bdm.email'),
                'password' => config('services.bdm.password'),
            ]);

            // Lance une exception si la requête HTTP elle-même échoue
            $response->throw();

            // Vérifie si le login a réussi selon la réponse de l'API
            if (!$response->json('isSucceed')) {
                Log::error('L\'API BDM a refusé la connexion.', ['response' => $response->json()]);
                throw new \Exception('Authentification API BDM échouée: L\'API a refusé la connexion.');
            }

            $token = $response->json('data.accessToken');

            if (!$token) {
                Log::error('Impossible de récupérer l\'accessToken depuis la réponse de l\'API BDM.', ['response' => $response->json()]);
                throw new \Exception('Authentification API BDM échouée: token manquant dans la réponse.');
            }
            
            Log::info('✅ AUTHENTIFICATION API BDM RÉUSSIE. Token obtenu.');
            Log::info('Nouveau token BDM obtenu et mis en cache.');
            return $token;
        });
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
        Log::info('Commande data retrieved from session: ' . json_encode($commandeData)); // Added log

        if (!$commandeData) {
            return redirect()->route('form-consigne')->with('error', 'Aucune commande en cours. Veuillez recommencer.');
        }

        $idPlateforme = $commandeData['idPlateforme'];
        $commandeLignes = $commandeData['commandeLignes'];
        $clientData = $commandeData['client'];
        $totalPrixTTC = $commandeData['total_prix_ttc'];

        try {
            // Appel à l\'API externe
            $token = $this->getBdmToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post(config('services.bdm.base_url') . "/api/plateforme/{$idPlateforme}/commande", [
                'CommandeLignes' => $commandeLignes, // Use exact key from API spec
                'Client' => $clientData, // Use exact key from API spec
            ]);

            Log::info('API Commande response: ' . $response->body());

            $apiResult = $response->json();

            if ($response->successful() && $apiResult['statut'] === 1) {
                // Succès de l\'API externe, enregistrer dans notre base de données
                $client = Auth::guard('client')->user();
                $commande = Commande::create([
                    'client_id' => $client->id,
                    'client_email' => $client->email,
                    'client_nom' => $client->nom,
                    'client_prenom' => $client->prenom,
                    'client_telephone' => $client->telephone,
                    'client_civilite' => $client->civilite,
                    'client_nom_societe' => $client->nomSociete,
                    'client_adresse' => $client->adresse,
                    'client_complement_adresse' => $client->complementAdresse,
                    'client_ville' => $client->ville,
                    'client_code_postal' => $client->codePostal,
                    'client_pays' => $client->pays,
                    'id_api_commande' => $apiResult['message'] ?? null, // UUID is in 'message' field
                    'id_plateforme' => $idPlateforme,
                    'total_prix_ttc' => $totalPrixTTC,
                    'statut' => 'completed',
                    'details_commande_lignes' => json_encode($commandeLignes), // Store as JSON
                    'invoice_content' => $apiResult['content'] ?? null, // Store the base64 content as invoice_content
                ]);

                Session::forget('commande_en_cours');
                Session::put('api_payment_result', $apiResult); // Store API result for success page
                Session::put('last_commande_id', $commande->id); // Store Commande ID for success page

                return response()->json([
                    'statut' => 1,
                    'message' => 'Votre commande a été passée avec succès !',
                    'redirect' => route('payment.success')
                ]);

            } else {
                Log::error('API Commande failed. Status: ' . $response->status() . ' Body: ' . $response->body());
                $errorMessage = $apiResult['message'] ?? 'Erreur inconnue lors de la commande via l\'API externe.';
                
                return response()->json([
                    'statut' => 0,
                    'message' => $errorMessage
                ], $response->status()); // Return appropriate HTTP status
            }

        } catch (\Exception $e) {
            Log::error('Technical error during payment processing: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'statut' => 0,
                'message' => 'Une erreur technique est survenue. Veuillez réessayer.'
            ], 500);
        }
    }

    public function showPaymentPage()
    {
        $user = Auth::guard('client')->user(); // Get the authenticated client
        if (!$user) {
            return redirect()->route('client.login')->with('error', 'Veuillez vous connecter pour accéder à la page de paiement.');
        }
        // Re-fetch client data from database to ensure it's the latest
        $user = \App\Models\Client::find($user->id);
        if (!$user) {
            return redirect()->route('client.login')->with('error', 'Client introuvable après authentification.');
        }

        // Ensure commandeData in session is updated with latest client info
        $commandeData = Session::get('commande_en_cours');
        if ($commandeData) {
            // Update client data within commandeData
            $commandeData['client'] = [
                "email" => $user->email,
                "telephone" => $user->telephone,
                "nom" => $user->nom,
                "prenom" => $user->prenom,
                "civilite" => $user->civilite ?? null,
                "nomSociete" => $user->nomSociete ?? null,
                "adresse" => $user->adresse ?? null,
                "complementAdresse" => $user->complementAdresse ?? null,
                "ville" => $user->ville ?? null,
                "codePostal" => $user->codePostal ?? null,
                "pays" => $user->pays ?? null
            ];
            Session::put('commande_en_cours', $commandeData);
            Log::info('Commande data in session updated with latest client info: ' . json_encode($commandeData));
        }

        return view('payment', compact('user')); // Pass client data to the view
    }

    public function showPaymentSuccess()
    {
        $apiResult = Session::get('api_payment_result');
        $lastCommandeId = Session::get('last_commande_id');

        if (!$apiResult || !$lastCommandeId) {
            // Optionally handle cases where session data is missing
            return redirect()->route('form-consigne')->with('error', 'La session de paiement a expiré. Veuillez réessayer.');
        }

        // Forget the session data after using it
        Session::forget(['api_payment_result', 'last_commande_id']);

        return view('payment-success', [
            'apiResult' => $apiResult,
            'lastCommandeId' => $lastCommandeId
        ]);
    }
}