<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Commande;
use App\Models\PaymentClient; // Ajout du nouveau modèle
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
            Log::info('Entering preparePayment method.', ['request_data' => $request->all()]);

            $validatedData = $request->validate([
                'airportId' => 'required|string',
                'dateDepot' => 'required|date',
                'heureDepot' => 'required|string',
                'dateRecuperation' => 'required|date',
                'heureRecuperation' => 'required|string',
                'baggages' => 'required|array',
                'products' => 'required|array',
                'options' => 'nullable|array',
                'options.*.id' => 'required|string',
                'options.*.lieu_id' => 'nullable|string',
                'options.*.informations_complementaires' => 'nullable|string|max:255',
                'options.*.commentaire' => 'nullable|string',
            ]);

            // --- Server-side definitions for security ---
            $serviceId = 'dfb8ac1b-8bb1-4957-afb4-1faedaf641b7';
            $staticOptions = [
                'opt_priority' => ['prixUnitaire' => 15, 'libelle' => 'Service Priority'],
                'opt_premium' => ['prixUnitaire' => 25, 'libelle' => 'Service Premium'],
            ];
            $baggageTypeToLibelleMap = [
                'accessory' => 'Accessoires',
                'cabin' => 'Bagage cabine',
                'hold' => 'Bagage soute',
                'special' => 'Bagage spécial',
                'cloakroom' => 'Vestiaire',
            ];
            // ------------------------------------------

            $commandeLignes = [];

            // 1. Process Baggages
            foreach ($validatedData['baggages'] as $baggage) {
                $expectedLibelle = $baggageTypeToLibelleMap[$baggage['type']] ?? null;
                if (!$expectedLibelle) throw new \Exception('Unknown baggage type: ' . $baggage['type']);

                $productDetails = collect($validatedData['products'])->firstWhere('libelle', $expectedLibelle);
                if (!$productDetails) throw new \Exception('Product details not found for: ' . $expectedLibelle);

                $commandeLignes[] = [
                    "idProduit" => $productDetails['id'],
                    "idService" => $serviceId,
                    "dateDebut" => $validatedData['dateDepot'] . 'T' . $validatedData['heureDepot'] . ':00.000Z',
                    "dateFin" => $validatedData['dateRecuperation'] . 'T' . $validatedData['heureRecuperation'] . ':00.000Z',
                    "prixTTC" => ($productDetails['prixUnitaire'] * $baggage['quantity']),
                    "quantite" => $baggage['quantity'],
                    "libelleProduit" => $productDetails['libelle']
                ];
            }

            // 2. Process Options
            if (!empty($validatedData['options'])) {
                foreach ($validatedData['options'] as $selectedOption) {
                    $optionId = $selectedOption['id'];
                    if (!isset($staticOptions[$optionId])) {
                        throw new \Exception('Invalid option ID provided: ' . $optionId);
                    }
                    
                    $optionDetails = $staticOptions[$optionId];

                    $commandeLignes[] = [
                        "idProduit" => $optionId, // Use the static ID for the command
                        "idService" => $serviceId,
                        "dateDebut" => $validatedData['dateDepot'] . 'T' . $validatedData['heureDepot'] . ':00.000Z',
                        "dateFin" => $validatedData['dateRecuperation'] . 'T' . $validatedData['heureRecuperation'] . ':00.000Z',
                        "prixTTC" => $optionDetails['prixUnitaire'],
                        "quantite" => 1,
                        "libelleProduit" => $optionDetails['libelle'],
                        "idLieu" => $selectedOption['lieu_id'] ?? null,
                        "informationsComplementaires" => $selectedOption['informations_complementaires'] ?? null,
                        "commentaire" => $selectedOption['commentaire'] ?? null,
                    ];
                }
            }

            // 3. Prepare final command data
            $user = Auth::guard('client')->user();

            if ($user) {
                // Authenticated user flow
                $clientRecord = \App\Models\Client::find($user->id);
                if (!$clientRecord) {
                    return response()->json(['message' => 'Client authentifié introuvable.'], 404);
                }
                $clientData = [
                    "email" => $clientRecord->email, "telephone" => $clientRecord->telephone, "nom" => $clientRecord->nom,
                    "prenom" => $clientRecord->prenom, "civilite" => $clientRecord->civilite ?? null, "nomSociete" => null,
                    "adresse" => $clientRecord->adresse ?? null, "complementAdresse" => null, "ville" => $clientRecord->ville ?? null,
                    "codePostal" => $clientRecord->codePostal ?? null, "pays" => $clientRecord->pays ?? null,
                    "is_guest" => false
                ];
            } else {
                // Guest user flow
                $guestEmail = $request->input('guest_email');
                if (!$guestEmail) {
                    return response()->json(['message' => 'Unauthenticated'], 401);
                }
                
                \Illuminate\Support\Facades\Validator::make(['guest_email' => $guestEmail], [
                    'guest_email' => 'required|email|max:255'
                ])->validate();

                $clientData = [
                    "email" => $guestEmail, "telephone" => null, "nom" => 'Invité',
                    "prenom" => 'Client', "civilite" => null, "nomSociete" => null,
                    "adresse" => null, "complementAdresse" => null, "ville" => null,
                    "codePostal" => null, "pays" => null,
                    "is_guest" => true
                ];
            }

            $commandeData = [
                'idPlateforme' => $validatedData['airportId'],
                'commandeLignes' => $commandeLignes,
                'client' => $clientData,
                'total_prix_ttc' => array_reduce($commandeLignes, fn($sum, $item) => $sum + $item['prixTTC'], 0),
            ];

            Session::put('commande_en_cours', $commandeData);
            Log::info('Commande data stored in session.', ['data' => $commandeData]);

            return response()->json(['message' => 'Commande préparée avec succès.', 'redirect_url' => route('payment')]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in preparePayment', ['errors' => $e->errors()]);
            return response()->json(['message' => 'Les données fournies sont invalides.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in preparePayment', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Une erreur interne est survenue lors de la préparation du paiement.'], 500);
        }
    }

    public function updateGuestInfoInSession(Request $request)
    {
        $validated = $request->validate([
            'telephone' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'civilite' => 'nullable|string',
            'nomSociete' => 'nullable|string',
            'adresse' => 'required|string',
            'complementAdresse' => 'nullable|string',
            'ville' => 'required|string',
            'codePostal' => 'required|string',
            'pays' => 'required|string',
        ]);

        $commandeData = Session::get('commande_en_cours');

        if (!$commandeData || !isset($commandeData['client']['is_guest']) || !$commandeData['client']['is_guest']) {
            return response()->json(['success' => false, 'message' => 'Not a guest session.'], 403);
        }

        // Update the client data within the session
        $commandeData['client']['telephone'] = $validated['telephone'];
        $commandeData['client']['nom'] = $validated['nom'];
        $commandeData['client']['prenom'] = $validated['prenom'];
        $commandeData['client']['civilite'] = $validated['civilite'];
        $commandeData['client']['nomSociete'] = $validated['nomSociete'];
        $commandeData['client']['adresse'] = $validated['adresse'];
        $commandeData['client']['complementAdresse'] = $validated['complementAdresse'];
        $commandeData['client']['ville'] = $validated['ville'];
        $commandeData['client']['codePostal'] = $validated['codePostal'];
        $commandeData['client']['pays'] = $validated['pays'];

        Session::put('commande_en_cours', $commandeData);

        return response()->json(['success' => true, 'message' => 'Guest information updated in session.']);
    }

    private function getBdmToken(): string
    {
        // Tente de rcuprer le token depuis le cache
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
        if (!$commandeData || !isset($commandeData['client'])) {
            Log::error('[showPaymentPage] CRITICAL: Commande data or client info NOT FOUND in session. Aborting.');
            return redirect()->route('form-consigne')->with('error', 'Votre session de commande a expiré ou est invalide. Veuillez recommencer.');
        }

        $clientDataFromSession = $commandeData['client'];
        $isGuest = $clientDataFromSession['is_guest'] ?? false;
        $user = null;

        if ($isGuest) {
            // For guests, use the data directly from the session.
            // Cast to object so the view can access properties like $user->email
            $user = (object) $clientDataFromSession;
            Log::info('[showPaymentPage] Handling guest user from session.', ['data' => $user]);
        } else {
            // For authenticated users, fetch the full model from the DB.
            $user = \App\Models\Client::where('email', $clientDataFromSession['email'])->first();
            if (!$user) {
                Log::error('[showPaymentPage] Client record not found in DB for email from session: ' . $clientDataFromSession['email']);
                return redirect()->route('form-consigne')->with('error', 'Erreur interne: Client introuvable pour la session de commande.');
            }
            Log::info('[showPaymentPage] Handling authenticated user from DB.', ['data' => $user]);
        }
        
        $isProfileComplete = true;
        $requiredFields = ['telephone'];
        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                $isProfileComplete = false;
                break;
            }
        }

        $formToken = null;

        if ($isProfileComplete) {
            Log::info('[showPaymentPage] Client profile is complete (or sufficient for guest). Proceeding to get formToken.');
            $commandeData['client'] = (array) $user; // Recast object to array for session consistency
            Session::put('commande_en_cours', $commandeData);
            Log::info('[showPaymentPage] Session data updated with latest client info.');

            $formToken = $this->redirectToMonetico();
            if ($formToken) {
                Log::info('[showPaymentPage] SUCCESS: formToken received.');
            } else {
                Log::error('[showPaymentPage] FAILURE: Did not receive formToken.');
                return redirect()->route('form-consigne')->with('error', 'Erreur lors de l\initiation du paiement.');
            }
        } else {
            Log::warning('[showPaymentPage] Profile for client ' . ($user->email ?? '') . ' is incomplete. Displaying form for completion.');
        }

        return view('payment', compact('user', 'formToken', 'isProfileComplete', 'isGuest'));
    }

    public function paymentSuccess(Request $request)
    {
        $commandeData = Session::get('commande_en_cours');
        if (!$commandeData) {
            return redirect()->route('form-consigne')->with('error', 'Aucune commande en cours. Veuillez recommencer.');
        }

        $idPlateforme = $commandeData['idPlateforme'];
        $commandeLignes = $commandeData['commandeLignes'];
        $clientData = $commandeData['client'];
        $totalPrixTTC = $commandeData['total_prix_ttc'];

        try {
            $token = $this->getBdmToken();

            $lignesProduits = [];
            $lignesOptions = [];
            foreach ($commandeLignes as $ligne) {
                if ($this->isUuid($ligne['idProduit'])) {
                    $lignesProduits[] = $ligne;
                } else {
                    $lignesOptions[] = $ligne;
                }
            }

            // Préparation du payload pour l'API BDM
            $payload = [
                'CommandeLignes' => $lignesProduits,
                'CommandeOptions' => $lignesOptions,
                'Client' => $clientData,
                'CommandeInfos' => [
                    'idPlateforme' => $idPlateforme,
                    'totalPrixTTC' => $totalPrixTTC,
                    // Vous pouvez ajouter d'autres informations générales de la commande ici si nécessaire
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post(config('services.bdm.base_url') . "/api/plateforme/{$idPlateforme}/commande", $payload);

            Log::info('API Commande response: ' . $response->body());

            $apiResult = $response->json();

            if ($response->successful() && isset($apiResult['statut']) && $apiResult['statut'] === 1) {
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

                PaymentClient::create([
                    'client_id' => $client->id,
                    'commande_id' => $commande->id,
                    'monetico_order_id' => $request->input('orderId', Session::get('monetico_order_id')),
                    'monetico_transaction_id' => $request->input('transactionId'),
                    'amount' => $commande->total_prix_ttc * 100,
                    'currency' => 'EUR',
                    'status' => 'paid',
                    'payment_method' => $request->input('brand'),
                    'raw_response' => json_encode($request->all()),
                ]);

                Session::forget('commande_en_cours');
                Session::forget('monetico_order_id');
                Session::put('api_payment_result', $apiResult);
                Session::put('last_commande_id', $commande->id);

                return redirect()->route('payment.success.show');
            } else {
                Log::error('API Commande failed. Status: ' . $response->status() . ' Body: ' . $response->body());
                $errorMessage = $apiResult['message'] ?? 'Erreur inconnue lors de la communication avec le service de réservation.';
                return redirect()->route('form-consigne')->with('error', 'Votre paiement a été accepté, mais une erreur est survenue lors de la finalisation de votre réservation. Veuillez contacter le support. Détails : ' . $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Technical error during payment processing: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('form-consigne')->with('error', 'Une erreur technique est survenue après le paiement. Veuillez contacter le support.');
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

    /**
     * Check if a string is a valid UUID.
     *
     * @param string $uuid
     * @return boolean
     */
    private function isUuid($uuid)
    {
        if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid) !== 1)) {
            return false;
        }
        return true;
    }
}
