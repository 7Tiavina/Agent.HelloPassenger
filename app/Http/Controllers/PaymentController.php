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
use Illuminate\Support\Facades\Validator; // Added for guest validation

class PaymentController extends Controller
{
    /**
     * Prépare les données de la commande et les stocke en session avant la redirection vers la page de paiement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function preparePayment(Request $request)
    {
        try {
            Log::info('Entering preparePayment method.', ['request_data' => $request->all()]);

            $validatedData = $request->validate([
                'airportId' => 'required|string',
                'airportName' => 'required|string',
                'dateDepot' => 'required|date',
                'heureDepot' => 'required|string',
                'dateRecuperation' => 'required|date',
                'heureRecuperation' => 'required|string',
                'baggages' => 'required|array',
                'products' => 'required|array',
                'options' => 'nullable|array',
                'options.*.id' => 'required|string',
                'options.*.libelle' => 'required|string',
                'options.*.prix' => 'required|numeric',
                'options.*.details' => 'nullable|array',
            ]);

            $serviceId = 'dfb8ac1b-8bb1-4957-afb4-1faedaf641b7';
            $baggageTypeToLibelleMap = [
                'accessory' => 'Accessoires', 'cabin' => 'Bagage cabine', 'hold' => 'Bagage soute',
                'special' => 'Bagage spécial', 'cloakroom' => 'Vestiaire',
            ];

            $commandeLignes = [];
            $premiumDetails = null; // Will store premium option's details

            // 1. Process Baggages
            foreach ($validatedData['baggages'] as $baggage) {
                $expectedLibelle = $baggageTypeToLibelleMap[$baggage['type']] ?? null;
                if (!$expectedLibelle) throw new \Exception('Unknown baggage type: ' . $baggage['type']);
                $productDetails = collect($validatedData['products'])->firstWhere('libelle', $expectedLibelle);
                if (!$productDetails) throw new \Exception('Product details not found for: ' . $expectedLibelle);

                $commandeLignes[] = [
                    "idProduit" => $productDetails['id'], "idService" => $serviceId,
                    "dateDebut" => $validatedData['dateDepot'] . 'T' . $validatedData['heureDepot'] . ':00.000Z',
                    "dateFin" => $validatedData['dateRecuperation'] . 'T' . $validatedData['heureRecuperation'] . ':00.000Z',
                    "prixTTC" => ($productDetails['prixUnitaire'] * $baggage['quantity']), "quantite" => $baggage['quantity'],
                    "libelleProduit" => $productDetails['libelle']
                ];
            }

            // 2. Process Options
            if (!empty($validatedData['options'])) {
                foreach ($validatedData['options'] as $selectedOption) {
                    // If this is the premium option, store its details separately
                    if (str_contains($selectedOption['libelle'], 'Premium')) {
                        $premiumDetails = $selectedOption['details'] ?? null;
                    }

                    // Add the option line WITHOUT the details object to commandeLignes
                    $commandeLignes[] = [
                        "idProduit" => $selectedOption['id'], "idService" => $serviceId,
                        "dateDebut" => $validatedData['dateDepot'] . 'T' . $validatedData['heureDepot'] . ':00.000Z',
                        "dateFin" => $validatedData['dateRecuperation'] . 'T' . $validatedData['heureRecuperation'] . ':00.000Z',
                        "prixTTC" => $selectedOption['prix'], "quantite" => 1,
                        "libelleProduit" => $selectedOption['libelle'],
                        "is_option" => true
                    ];
                }
            }

            // 3. Create commandeInfos from extracted premium details
            // Initialize with default empty values to ensure the object structure is always correct
            $commandeInfos = (object) [
                'modeTransport' => '',
                'lieu' => '',
                'commentaires' => '',
            ];

            if ($premiumDetails && !empty($premiumDetails)) {
                $details = $premiumDetails;
                $commentairesArray = [];
                $directionText = ($details['direction'] ?? '') === 'terminal_to_agence' ? 'Récupération bagages' : 'Restitution bagages';
                $commentairesArray[] = "Type de service: " . $directionText;

                $commandeInfos->modeTransport = $details['modeTransport'] ?? $directionText; // Prioritize dedicated field if ever added
                
                $isArrivalFlow = isset($details['flight_number_arrival']);
                $locationKey = $isArrivalFlow ? 'pickup_location_arrival' : 'restitution_location_departure';
                $flightNumberKey = $isArrivalFlow ? 'flight_number_arrival' : 'flight_number_departure';
                $timeKey = $isArrivalFlow ? 'pickup_time_arrival' : 'restitution_time_departure';
                $instructionsKey = $isArrivalFlow ? 'instructions_arrival' : 'instructions_departure';

                $commandeInfos->lieu = $details[$locationKey] ?? 'Non spécifié';

                if (!empty($details[$flightNumberKey])) $commentairesArray[] = "Numéro de vol: " . $details[$flightNumberKey];
                if (!empty($details[$timeKey])) {
                    $timeLabel = $isArrivalFlow ? 'Heure de prise en charge' : 'Heure de restitution';
                    $commentairesArray[] = "$timeLabel: " . $details[$timeKey];
                }
                if (!empty($details[$instructionsKey])) $commentairesArray[] = "Informations complémentaires: " . $details[$instructionsKey];
                
                $commandeInfos->commentaires = implode('; ', $commentairesArray);
            }

            // 4. Prepare Client and Final Command Data
            $user = Auth::guard('client')->user();
            $clientData = $this->getClientData($user, $request->input('guest_email'));
            if (!$clientData) return response()->json(['message' => 'Client non identifié.'], 401);

            $commandeData = [
                'idPlateforme' => $validatedData['airportId'],
                'airportName' => $validatedData['airportName'],
                'commandeLignes' => $commandeLignes,
                'commandeInfos' => $commandeInfos, // Add commandeInfos to the session
                'client' => $clientData,
                'total_prix_ttc' => array_reduce($commandeLignes, fn($sum, $item) => $sum + ($item['prixTTC'] ?? 0), 0),
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

        Session::put('guest_customer_details', $validated);

        $commandeData = Session::get('commande_en_cours');
        if ($commandeData && isset($commandeData['client']['is_guest']) && $commandeData['client']['is_guest']) {
            $commandeData['client'] = array_merge($commandeData['client'], $validated);
            Session::put('commande_en_cours', $commandeData);
        }

        return response()->json(['success' => true, 'message' => 'Guest information updated in session.']);
    }

    private function getBdmToken(): string
    {
        return Cache::remember('bdm_api_token', 3300, function () {
            Log::info('Cache BDM token expir%c3%a9. Demande d%c3%a0 un nouveau token.');
            $response = Http::post(config('services.bdm.base_url') . '/User/Login', [
                'userName' => config('services.bdm.username'),
                'email' => config('services.bdm.email'),
                'password' => config('services.bdm.password'),
            ]);
            $response->throw();
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

        $customerEmail = $commandeData['client']['email'] ?? null;
        $customerFirstName = $commandeData['client']['prenom'] ?? null;
        $customerLastName = $commandeData['client']['nom'] ?? null;

        if ((!$customerEmail || !$customerFirstName || !$customerLastName) && Auth::guard('client')->check()) {
            $authenticatedUser = Auth::guard('client')->user();
            $customerEmail = $customerEmail ?? $authenticatedUser->email;
            $customerFirstName = $customerFirstName ?? $authenticatedUser->prenom;
            $customerLastName = $customerLastName ?? $authenticatedUser->nom;
        }
        if (!$customerEmail || !$customerFirstName || !$customerLastName) {
            Log::error('Monetico redirection failed: Missing customer email, first name or last name.', ['commandeData' => $commandeData]);
            return null;
        }

        $payload = [
            'shopId' => config('monetico.login'), 'amount' => (int)($commandeData['total_prix_ttc'] * 100), 'currency' => 'EUR',
            'orderId' => $orderId,
            'customer' => ['email' => $customerEmail, 'firstName' => $customerFirstName, 'lastName' => $customerLastName],
            'paymentMethod' => ['type' => 'Card'],
            'urls' => [
                'success' => route('payment.success'), 'error' => route('payment.error'),
                'cancel' => route('payment.cancel'), 'return' => route('payment.return'),
            ],
        ];

        Log::info('Calling Monetico CreatePayment API with correct Basic Auth.');
        $response = Http::withHeaders(['Authorization' => 'Basic ' . base64_encode(config('monetico.login') . ':' . config('monetico.secret_key')), 
            'Content-Type' => 'application/json', 'Accept' => 'application/json',
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
            $user = (object) $clientDataFromSession;
            Log::debug('[showPaymentPage] Guest user data from session: ', ['clientDataFromSession' => $clientDataFromSession, 'userObject' => $user]);
            Log::info('[showPaymentPage] Handling guest user from session.', ['data' => $user]);
        } else {
            $user = Auth::guard('client')->user();
            if (!$user) {
                Log::error('[showPaymentPage] Authenticated client not found via Auth::guard. Redirecting to form-consigne.');
                return redirect()->route('form-consigne')->with('error', 'Erreur interne: Client authentifié introuvable. Veuillez vous reconnecter.');
            }
            Log::info('[showPaymentPage] Handling authenticated user from Auth::guard.', ['data' => $user]);
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
            $commandeData['client'] = (array) $user;
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

        if (!isset($clientData['email']) && Auth::guard('client')->check()) {
            $authenticatedUser = Auth::guard('client')->user();
            $clientData['email'] = $authenticatedUser->email;
            $clientData = array_merge($clientData, (array) $authenticatedUser);
        }

        if (!isset($clientData['email'])) {
            Log::error('paymentSuccess failed: Client email not available in session or from authenticated user.', ['commandeData' => $commandeData]);
            return redirect()->route('form-consigne')->with('error', 'Erreur: Email client non disponible pour finaliser la commande.');
        }

        $commandeInfos = $commandeData['commandeInfos'] ?? new \stdClass();
        $totalPrixTTC = $commandeData['total_prix_ttc'];

        try {
            $token = $this->getBdmToken();

            $lignesProduits = [];
            $lignesOptions = [];
            foreach ($commandeLignes as $ligne) {
                if (isset($ligne['is_option']) && $ligne['is_option']) {
                    unset($ligne['is_option']);
                    $lignesOptions[] = $ligne;
                } else {
                    $lignesProduits[] = $ligne;
                }
            }
            
            $payload = [
                'commandeLignes' => $lignesProduits,
                'commandeOptions' => $lignesOptions,
                'client' => $clientData,
                'commandeInfos' => $commandeInfos,
            ];

            Log::info('Données envoyées à l\'API Commande BDM:', ['payload' => $payload]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json',
            ])->post(config('services.bdm.base_url') . "/api/plateforme/{$idPlateforme}/commande", $payload);

            Log::info('API Commande response: ' . $response->body());

            $apiResult = $response->json();

            if ($response->successful() && isset($apiResult['statut']) && $apiResult['statut'] === 1) {
                
                $clientId = null;
                if (isset($clientData['is_guest']) && $clientData['is_guest']) {
                    $client = \App\Models\Client::updateOrCreate(
                        ['email' => $clientData['email']],
                        array_merge($clientData, ['password_hash' => \App\Models\Client::where('email', $clientData['email'])->value('password_hash') ?? bcrypt('')])
                    );
                    $clientId = $client->id;
                } else {
                    $client = Auth::guard('client')->user();
                    $clientId = $client ? $client->id : null;
                }

                if (!$clientId) {
                    Log::error('API Commande success but could not determine client ID.');
                    return redirect()->route('form-consigne')->with('error', 'Impossible de déterminer le client pour la commande.');
                }

                $commande = Commande::create([
                    'client_id' => $clientId, 'client_email' => $clientData['email'], 'client_nom' => $clientData['nom'],
                    'client_prenom' => $clientData['prenom'], 'client_telephone' => $clientData['telephone'],
                    'client_civilite' => $clientData['civilite'] ?? null, 'client_nom_societe' => $clientData['nomSociete'] ?? null,
                    'client_adresse' => $clientData['adresse'] ?? null, 'client_complement_adresse' => $clientData['complementAdresse'] ?? null,
                    'client_ville' => $clientData['ville'] ?? null, 'client_code_postal' => $clientData['codePostal'] ?? null,
                    'client_pays' => $clientData['pays'] ?? null, 'id_api_commande' => $apiResult['message'] ?? null,
                    'id_plateforme' => $idPlateforme, 'total_prix_ttc' => $totalPrixTTC, 'statut' => 'completed',
                    'details_commande_lignes' => json_encode($commandeLignes),
                    'invoice_content' => isset($apiResult['content']) ? json_encode($apiResult['content']) : null,
                ]);

                PaymentClient::create([
                    'client_id' => $clientId, 'commande_id' => $commande->id,
                    'monetico_order_id' => $request->input('orderId', Session::get('monetico_order_id')),
                    'monetico_transaction_id' => $request->input('transactionId'),
                    'amount' => $commande->total_prix_ttc * 100, 'currency' => 'EUR', 'status' => 'paid',
                    'payment_method' => $request->input('brand'), 'raw_response' => json_encode($request->all()),
                ]);

                Session::forget(['commande_en_cours', 'monetico_order_id']);
                Session::put('api_payment_result', $apiResult);
                Session::put('last_commande_id', $commande->id);

                /*
                // --- ENVOI DE L'EMAIL DE CONFIRMATION AVEC FACTURE PDF ---
                try {
                    // Générer le PDF
                    $pdf = PDF::loadView('invoices.default', compact('commande'));
                    
                    // Sauvegarder le PDF temporairement
                    $reference = $commande->paymentClient->monetico_order_id ?? $commande->id;
                    $fileName = 'facture-' . $reference . '.pdf';
                    
                    // Assurez-vous que le répertoire 'temp' existe
                    if (!Storage::exists('temp')) {
                        Storage::makeDirectory('temp');
                    }

                    $pdfPath = Storage::path('temp/' . $fileName);
                    $pdf->save($pdfPath);

                    // Envoyer l'e-mail
                    Mail::to($commande->client_email)->send(new OrderConfirmationMail($commande, $pdfPath));
                    Log::info('Email de confirmation de commande envoyé à ' . $commande->client_email);

                    // Supprimer le fichier PDF temporaire
                    Storage::delete('temp/' . $fileName);

                } catch (\Exception $mailException) {
                    Log::error('Erreur lors de l\'envoi de l\'e-mail de confirmation: ' . $mailException->getMessage(), ['exception' => $mailException]);
                    // Continuer le processus même si l\'e-mail échoue
                }
                // --- FIN ENVOI EMAIL ---
                */

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

        // Fetch the Commande object with its paymentClient to get the monetico_order_id
        $commande = Commande::with('paymentClient')->find($lastCommandeId);

        if (!$commande) {
            return redirect()->route('form-consigne')->with('error', 'Commande introuvable.');
        }

        // Forget the session data after using it
        Session::forget(['api_payment_result', 'last_commande_id']);

        return view('payment-success', [
            'apiResult' => $apiResult,
            'lastCommandeId' => $lastCommandeId,
            'commande' => $commande, // Pass the full commande object
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

    public function clearGuestSession(Request $request)
    {
        Session::forget('commande_en_cours');
        Session::forget('guest_customer_details');
        Session::forget('monetico_order_id'); // Ajout pour vider l\'ID de commande Monetico
        Log::info('Guest session data cleared successfully.');
        return response()->json(['success' => true, 'message' => 'Guest session data cleared.']);
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

    // Helper method to consolidate client data retrieval
    private function getClientData($user, $guestEmail) {
        if ($user) {
            return [
                "email" => $user->email, "telephone" => $user->telephone, "nom" => $user->nom,
                "prenom" => $user->prenom, "civilite" => $user->civilite ?? null, "nomSociete" => null,
                "adresse" => $user->adresse ?? null, "complementAdresse" => null, "ville" => $user->ville ?? null,
                "codePostal" => $user->codePostal ?? null, "pays" => $user->pays ?? null,
                "is_guest" => false
            ];
        }

        if ($guestEmail) {
            \Illuminate\Support\Facades\Validator::make(['guest_email' => $guestEmail], ['guest_email' => 'required|email|max:255'])->validate();
            $persistentGuestDetails = Session::get('guest_customer_details', []);
            return array_merge([
                "email" => $guestEmail, "telephone" => null, "nom" => 'Invité',
                "prenom" => 'Client', "civilite" => null, "nomSociete" => null,
                "adresse" => null, "complementAdresse" => null, "ville" => null,
                "codePostal" => null, "pays" => null,
                "is_guest" => true
            ], $persistentGuestDetails);
        }
        
        return null;
    }
}