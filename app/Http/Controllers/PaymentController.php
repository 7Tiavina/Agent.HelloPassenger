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
     * Pr%c3%a8pare les donn%c3%a9es de la commande et les stocke en session avant la redirection vers la page de paiement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function preparePayment(Request $request)
    {
        try {
            Log::info('Entering preparePayment method.');
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

            $commandeLignes = [];

            // CORRECTED: Keys now match frontend data-type values ('hold', 'cloakroom')
            $baggageTypeToLibelleMap = [
                'cabin' => 'Bagage cabine',
                'hold' => 'Bagage soute',
                'cloakroom' => 'Vestiaire',
            ];

            foreach ($validatedData['baggages'] as $baggage) {
                $productPrice = 0;
                $productId = null;
                $productLibelle = null;
                $serviceId = 'dfb8ac1b-8bb1-4957-afb4-1faedaf641b7';

                $expectedLibelle = $baggageTypeToLibelleMap[$baggage['type']] ?? null;

                if (is_null($expectedLibelle)) {
                    // This will now be caught by the try-catch block
                    throw new \Exception('Unknown baggage type: ' . $baggage['type']);
                }

                foreach ($validatedData['products'] as $product) {
                    if ($product['libelle'] === $expectedLibelle) {
                        $productPrice = $product['prixUnitaire'];
                        $productId = $product['id'];
                        $productLibelle = $product['libelle'];
                        break;
                    }
                }

                if (is_null($productId) || is_null($productLibelle)) {
                    // This will also be caught
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

            $user = Auth::guard('client')->user();
            if (!$user) {
                return response()->json(['message' => 'Client non authentifié.'], 401);
            }
            
            $user = \App\Models\Client::find($user->id);
            if (!$user) {
                return response()->json(['message' => 'Client introuvable après authentification.'], 404);
            }

            $clientData = [
                "email" => $user->email, "telephone" => $user->telephone, "nom" => $user->nom,
                "prenom" => $user->prenom, "civilite" => $user->civilite ?? null, "nomSociete" => null,
                "adresse" => $user->adresse ?? null, "complementAdresse" => null, "ville" => $user->ville ?? null,
                "codePostal" => $user->codePostal ?? null, "pays" => $user->pays ?? null
            ];

            $commandeData = [
                'idPlateforme' => $validatedData['airportId'],
                'commandeLignes' => $commandeLignes,
                'client' => $clientData,
                'total_prix_ttc' => array_reduce($commandeLignes, function ($sum, $item) {
                    return $sum + $item['prixTTC'];
                }, 0),
            ];

            Session::put('commande_en_cours', $commandeData);
            Log::info('Commande data stored in session: ' . json_encode($commandeData));

            $responsePayload = ['message' => 'Commande préparée avec succès.', 'redirect_url' => route('payment')];
            Log::info('[preparePayment] Sending JSON response to browser: ' . json_encode($responsePayload));

            // Instead of redirecting, return a success JSON response
            return response()->json($responsePayload);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Laravel handles this for AJAX, but we catch it to be explicit
            Log::error('Validation failed in preparePayment: ' . $e->getMessage());
            return response()->json(['message' => 'Les données fournies sont invalides.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Catch any other errors and return a JSON response
            Log::error('Error in preparePayment: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return response()->json(['message' => 'Une erreur interne est survenue lors de la préparation du paiement.'], 500);
        }
    }

    private function getBdmToken(): string
    {
        // Tente de r%c3%a9cup%c3%a9rer le token depuis le cache
        return Cache::remember('bdm_api_token', 3300, function () {
            Log::info('Cache BDM token expir%c3%a9. Demande d%c3%a0 un nouveau token.');

            $response = Http::post(config('services.bdm.base_url') . '/User/Login', [
                'userName' => config('services.bdm.username'),
                'email' => config('services.bdm.email'),
                'password' => config('services.bdm.password'),
            ]);

            // Lance une exception si la requ%c3%aate HTTP elle-m%c3%aame %c3%a9choue
            $response->throw();

            // V%c3%a9rifie si le login a r%c3%a9ussi selon la r%c3%a9ponse de l'API
            if (!$response->json('isSucceed')) {
                Log::error('L%c3%a0API BDM a refus%c3%a9 la connexion.', ['response' => $response->json()]);
                throw new \Exception('Authentification API BDM %c3%a9chou%c3%a9e: L%c3%a0API a refus%c3%a9 la connexion.');
            }

            $token = $response->json('data.accessToken');

            if (!$token) {
                Log::error('Impossible de r%c3%a9cup%c3%a9rer l%c3%a0accessToken depuis la r%c3%a9ponse de l%c3%a0API BDM.', ['response' => $response->json()]);
                throw new \Exception('Authentification API BDM %c3%a9chou%c3%a9e: token manquant dans la r%c3%a9ponse.');
            }
            
            Log::info('✅ AUTHENTIFICATION API BDM R%c3%a9USSIE. Token obtenu.');
            Log::info('Nouveau token BDM obtenu et mis en cache.');
            return $token;
        });
    }

    public function redirectToMonetico()
    {
        Log::info('Entering redirectToMonetico method with Basic Auth as per documentation.');
        $commandeData = session('commande_en_cours');

        if (!$commandeData) {
            Log::error('Monetico redirection failed: Commande data not found in session.');
            return null;
        }

        $orderId = 'CMD-' . uniqid();
        Session::put('monetico_order_id', $orderId);

        // 1. Préparer les données de la requête
        $payload = [
            'shopId' => config('monetico.login'),
            'amount' => (int)($commandeData['total_prix_ttc'] * 100),
            'currency' => 'EUR',
            'orderId' => $orderId,
            'customer' => [
                'email' => $commandeData['client']['email'],
                'firstName' => $commandeData['client']['prenom'],
                'lastName' => $commandeData['client']['nom'],
            ],
            'paymentMethod' => ['type' => 'Card'],
            'urls' => [
                'success' => route('payment.success'),
                'error' => route('payment.error'),
                'cancel' => route('payment.cancel'),
                'return' => route('payment.return'),
            ],
        ];

        // 2. Créer la chaîne d'authentification Basic avec `login` et `secret_key`
        $authString = base64_encode(config('monetico.login') . ':' . config('monetico.secret_key'));

        // 3. Appeler l'API avec l'en-tête Authorization: Basic
        Log::info('Calling Monetico CreatePayment API with correct Basic Auth.');
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $authString,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(config('monetico.base_url') . '/Charge/CreatePayment', $payload);

        Log::info('Monetico API response (Basic Auth flow): ' . $response->body());

        if ($response->successful()) {
            $paymentData = $response->json();
            if (isset($paymentData['answer']['formToken'])) {
                return $paymentData['answer']['formToken'];
            }
        } else {
            Log::error('Monetico API error (Basic Auth flow): ' . $response->body());
            return null;
        }
    }

    public function showPaymentPage()
    {
        Log::info('----------------------------------------------------');
        Log::info('[showPaymentPage] START - Handling /payment route.');

        $commandeData = Session::get('commande_en_cours');
        if (!$commandeData) {
            Log::error('[showPaymentPage] CRITICAL: commande_en_cours NOT FOUND in session. Aborting.');
            return redirect()->route('form-consigne')->with('error', 'Votre session a expiré. Veuillez recommencer.');
        }
        Log::info('[showPaymentPage] Session data found for commande_en_cours.');

        $user = Auth::guard('client')->user(); // Get the authenticated client
        if (!$user) {
            Log::error('[showPaymentPage] CRITICAL: Client is not authenticated. Aborting.');
            return redirect()->route('client.login')->with('error', 'Veuillez vous connecter pour accéder à la page de paiement.');
        }
        
        $user = \App\Models\Client::find($user->id);
        if (!$user) {
            Log::error('[showPaymentPage] CRITICAL: Authenticated client not found in database. Aborting.');
            return redirect()->route('client.login')->with('error', 'Client introuvable après authentification.');
        }
        Log::info('[showPaymentPage] Authenticated client found: ' . $user->email);

        // Ensure commandeData in session is updated with latest client info
        $commandeData['client'] = [
            "email" => $user->email, "telephone" => $user->telephone, "nom" => $user->nom,
            "prenom" => $user->prenom, "civilite" => $user->civilite ?? null, "nomSociete" => $user->nomSociete ?? null,
            "adresse" => $user->adresse ?? null, "complementAdresse" => $user->complementAdresse ?? null, "ville" => $user->ville ?? null,
            "codePostal" => $user->codePostal ?? null, "pays" => $user->pays ?? null
        ];
        Session::put('commande_en_cours', $commandeData);
        Log::info('[showPaymentPage] Session data updated with latest client info.');

        Log::info('[showPaymentPage] Attempting to get Monetico formToken...');
        $formToken = $this->redirectToMonetico();

        if ($formToken) {
            Log::info('[showPaymentPage] SUCCESS: formToken received. Rendering payment view.');
            return view('payment', compact('user', 'formToken'));
        } else {
            Log::error('[showPaymentPage] FAILURE: Did not receive formToken. Redirecting with error.');
            return redirect()->route('form-consigne')->with('error', 'Erreur lors de l\'initialisation du paiement.');
        }
    }

    public function paymentSuccess(Request $request)
    {
        // Handle the successful payment return from Monetico
        // You can add logic here to verify the payment status if needed

        $commandeData = Session::get('commande_en_cours');
        if (!$commandeData) {
            return redirect()->route('form-consigne')->with('error', 'Aucune commande en cours. Veuillez recommencer.');
        }

        $idPlateforme = $commandeData['idPlateforme'];
        $commandeLignes = $commandeData['commandeLignes'];
        $clientData = $commandeData['client'];
        $totalPrixTTC = $commandeData['total_prix_ttc'];

        try {
            // Call the external API to finalize the order
            $token = $this->getBdmToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post(config('services.bdm.base_url') . "/api/plateforme/{$idPlateforme}/commande", [
                'CommandeLignes' => $commandeLignes,
                'Client' => $clientData,
            ]);

            Log::info('API Commande response: ' . $response->body());

            $apiResult = $response->json();

            if ($response->successful() && $apiResult['statut'] === 1) {
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
                    'id_api_commande' => $apiResult['message'] ?? null,
                    'id_plateforme' => $idPlateforme,
                    'total_prix_ttc' => $totalPrixTTC,
                    'statut' => 'completed',
                    'details_commande_lignes' => json_encode($commandeLignes),
                    'invoice_content' => $apiResult['content'] ?? null,
                ]);

                Session::forget('commande_en_cours');
                Session::put('api_payment_result', $apiResult);
                Session::put('last_commande_id', $commande->id);

                return redirect()->route('payment.success.show');
            } else {
                Log::error('API Commande failed. Status: ' . $response->status() . ' Body: ' . $response->body());
                $errorMessage = $apiResult['message'] ?? 'Erreur inconnue lors de la commande via l%c3%a0API externe.';
                return redirect()->route('payment')->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Technical error during payment processing: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('payment')->with('error', 'Une erreur technique est survenue. Veuillez r%c3%a9essayer.');
        }
    }

    public function handleIpn(Request $request)
    {
        // Handle the IPN from Monetico
        $data = $request->all();
        Log::info('Monetico IPN received: ' . json_encode($data));

        // Add your logic to process the IPN data, e.g., update order status

        return response()->json(['status' => 'success']);
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

    public function paymentError(Request $request)
    {
        Log::error('Payment failed or was rejected by Monetico.', $request->all());
        return redirect()->route('form-consigne')->with('error', 'Le paiement a échoué ou a été refusé. Veuillez réessayer ou contacter le support.');
    }

    public function paymentCancel(Request $request)
    {
        Log::info('Payment was cancelled by the user.', $request->all());
        return redirect()->route('form-consigne')->with('info', 'Vous avez annulé le processus de paiement.');
    }

    public function paymentReturn(Request $request)
    {
        // Most of the time, the 'return' URL is called for successful payments.
        // We redirect to the main success handler which contains the full logic to verify and save the command.
        return redirect()->route('payment.success', $request->query());
    }
}
