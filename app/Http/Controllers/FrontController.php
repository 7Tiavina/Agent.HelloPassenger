<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


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

    //Vérifie la disponibilité d'une plateforme à une date donnée.
    
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'idPlateforme' => 'required|string',
            'dateToCheck' => 'required|string',
        ]);
        
        Log::info('Appel à l\'API BDM pour la disponibilité', ['data' => $validated]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.bdm.api_token'),
            ])->get(config('services.bdm.base_url') . "/plateforme/{$validated['idPlateforme']}/date/{$validated['dateToCheck']}");
            
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
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.bdm.api_token'),
            ])->get(config('services.bdm.base_url') . "/plateforme/{$validated['idPlateforme']}/service/{$validated['idService']}/{$validated['duree']}/produits");
            
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
