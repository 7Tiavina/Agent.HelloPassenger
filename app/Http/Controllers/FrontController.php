<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\Response;


class FrontController extends Controller
{
    public function acceuil()
    {
        return view('Front.acceuil');
    }

    public function redirectForm()
    {
        try {
            $plateformes = $this->getPlateformes();
            
            // Utilise le premier aéroport pour obtenir une liste de produits par défaut
            $firstPlateformeId = $plateformes[0]['id'] ?? null;
            $products = [];
            if ($firstPlateformeId) {
                $products = $this->getProducts($firstPlateformeId);
            }

        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des données pour le formulaire : " . $e->getMessage());
            return view('Front.formulaire-consigne', [
                'plateformes' => [],
                'products' => [],
                'error' => "Impossible de charger les options de réservation pour le moment. Veuillez réessayer plus tard."
            ]);
        }

        return view('Front.formulaire-consigne', [
            'plateformes' => $plateformes,
            'products' => $products
        ]);
    }

    /**
     * Récupère toutes les plateformes (aéroports) depuis l'API BDM pour le service de consigne.
     * @return array
     * @throws \Exception
     */
    public function getPlateformes(): array
    {
        $serviceId = 'dfb8ac1b-8bb1-4957-afb4-1faedaf641b7'; // ID du service de consigne
        Log::info("Récupération de la liste des plateformes pour le service {$serviceId}.");
        try {
            $token = $this->getBdmToken();
            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->get(config('services.bdm.base_url') . "/api/service/{$serviceId}/plateformes");

            $response->throw();

            if ($response->json('statut') === 1 && is_array($response->json('content'))) {
                Log::info("Plateformes récupérées avec succès.");
                return $response->json('content');
            } else {
                Log::error("La réponse de l'API BDM pour les plateformes est invalide.", ['response' => $response->json()]);
                throw new \Exception("Réponse invalide de l'API pour les plateformes.");
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des plateformes BDM.", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Récupère les produits (types de bagages) pour une plateforme donnée avec une durée par défaut.
     * @param string $idPlateforme
     * @return array
     * @throws \Exception
     */
    public function getProducts(string $idPlateforme): array
    {
        $serviceId = 'dfb8ac1b-8bb1-4957-afb4-1faedaf641b7'; // ID du service de consigne
        $defaultDuration = 1; // Durée par défaut en minutes pour obtenir la liste
        Log::info("Récupération des produits pour la plateforme {$idPlateforme} et le service {$serviceId}.");
        try {
            $token = $this->getBdmToken();
            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->get(config('services.bdm.base_url') . "/api/plateforme/{$idPlateforme}/service/{$serviceId}/{$defaultDuration}/produits");

            $response->throw();

            if ($response->json('statut') === 1 && is_array($response->json('content'))) {
                Log::info("Produits récupérés avec succès pour la plateforme {$idPlateforme}.");
                return $response->json('content');
            } else {
                Log::error("La réponse de l'API BDM pour les produits est invalide.", ['response' => $response->json()]);
                throw new \Exception("Réponse invalide de l'API pour les produits.");
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des produits BDM.", ['error' => $e->getMessage()]);
            throw $e;
        }
    }



    public function showClientLogin()
    {
        return view('client.login'); 
    }

    
    public function clientLogin(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        $client = Client::where('email', $request->email)->first();

        if ($client && Hash::check($request->password, $client->password_hash)) {
            Auth::guard('client')->login($client); // login via guard client
            $request->session()->regenerate();

            // Redirect to /link-form (route name: form-consigne)
            return redirect()->route('form-consigne');
        }

        // échec : on renvoie avec un flash pour afficher le modal d'erreur
        return back()->withInput($request->only('email'))->with('login_error', true);
    }

    public function clientLogout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('front.acceuil');
    }


    public function clientDashboard()
    {
        return view('client.dashboard', ['client' => Auth::guard('client')->user()]);
    }

    public function clientRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:clients,email',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'telephone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('from_register', true);
        }

        $client = Client::create([
            'email' => $request->email,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone ?? null,
            'password_hash' => Hash::make($request->password),
        ]);

        Auth::guard('client')->login($client);
        $request->session()->regenerate();

        return redirect()->route('form-consigne');
    }



//-------------------------------API-----BDM-----------------------------

    /**
     * Récupère un token d'authentification pour l'API BDM, en le mettant en cache.
     * @return string
     * @throws \Illuminate\Http\Client\RequestException
     */
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

    //Vérifie la disponibilité d'une plateforme à une date donnée.
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'idPlateforme' => 'required|string',
            'dateToCheck' => 'required|string',
        ]);
        
        Log::info('Appel à l\'API BDM pour la disponibilité', ['data' => $validated]);

        try {
            $token = $this->getBdmToken();
            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->get(config('services.bdm.base_url') . "/api/plateforme/{$validated['idPlateforme']}/date/{$validated['dateToCheck']}");
            
            Log::info('Réponse de l\'API BDM (disponibilité)', ['status' => $response->status(), 'body' => $response->json()]);

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification de la disponibilité', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification de la disponibilité: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les tarifs (produits) pour une plateforme, un service et une durée donnés.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuote(Request $request)
    {
        $validated = $request->validate([
            'idPlateforme' => 'required|string',
            'idService' => 'required|string',
            'duree' => 'required|integer|min:1',
        ]);

        Log::info('Appel à l\'API BDM pour les tarifs et lieux', ['data' => $validated]);

        try {
            $token = $this->getBdmToken();
            $baseUrl = config('services.bdm.base_url');
            $idPlateforme = $validated['idPlateforme'];
            $idService = $validated['idService'];
            $duree = $validated['duree'];

            $responses = Http::pool(fn ($pool) => [
                $pool->withToken($token)->withHeaders(['Accept' => 'application/json'])->get("{$baseUrl}/api/plateforme/{$idPlateforme}/service/{$idService}/{$duree}/produits"),
                $pool->withToken($token)->withHeaders(['Accept' => 'application/json'])->get("{$baseUrl}/api/plateforme/{$idPlateforme}/lieux"),
            ]);

            $productsResponse = $responses[0];
            $lieuxResponse = $responses[1];

            // Vérifier si l'un ou l'autre des appels a échoué au niveau HTTP
            if ($productsResponse->failed() || $lieuxResponse->failed()) {
                Log::error("Échec d'au moins un appel API BDM dans le pool getQuote.", [
                    'products_status' => $productsResponse->status(),
                    'products_body' => $productsResponse->body(),
                    'lieux_status' => $lieuxResponse->status(),
                    'lieux_body' => $lieuxResponse->body(),
                ]);
                return response()->json(['statut' => 0, 'message' => 'Erreur lors de la communication avec le service de réservation.'], 500);
            }

            $productsResult = $productsResponse->json();
            $lieuxResult = $lieuxResponse->json();

            // Vérifier si le statut interne de l'API BDM indique un échec
            if (($productsResult['statut'] ?? 0) !== 1 || ($lieuxResult['statut'] ?? 0) !== 1) {
                Log::error("Réponse API BDM avec un statut d'échec dans le pool getQuote.", [
                    'products_response' => $productsResult,
                    'lieux_response' => $lieuxResult,
                ]);
                return response()->json(['statut' => 0, 'message' => "Les données de réservation n'ont pas pu être chargées entièrement."], 422);
            }

            // Si tout réussit, on construit la réponse
            return response()->json([
                'statut' => 1,
                'message' => 'Données récupérées',
                'content' => [
                    'products' => $productsResult['content'] ?? [],
                    'lieux' => $lieuxResult['content'] ?? [],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des tarifs/lieux', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur technique lors de la récupération des données : ' . $e->getMessage()
            ], 500);
        }
    }
}
