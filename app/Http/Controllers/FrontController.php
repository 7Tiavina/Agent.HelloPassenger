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
        return view('Front.formulaire-consigne');
    }


    public function showClientLogin()
    {
        return view('client.login'); // Ta vue dédiée si besoin, sinon intégrer modal existant
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
        
        Log::info('Appel à l\'API BDM pour les tarifs', ['data' => $validated]);

        try {
            $token = $this->getBdmToken();
            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->get(config('services.bdm.base_url') . "/api/plateforme/{$validated['idPlateforme']}/service/{$validated['idService']}/{$validated['duree']}/produits");
            
            Log::info('Réponse de l\'API BDM (tarifs)', ['status' => $response->status(), 'body' => $response->json()]);

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des tarifs', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des tarifs: ' . $e->getMessage()
            ], 500);
        }
    }
}
