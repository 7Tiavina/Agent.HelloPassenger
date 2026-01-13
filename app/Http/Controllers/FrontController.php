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
use Illuminate\Support\Str; // Add this import
use Illuminate\Http\Client\Response;
use App\Services\BdmApiService; // Add this import

class FrontController extends Controller
{
    protected $bdmApiService;

    public function __construct(BdmApiService $bdmApiService)
    {
        $this->bdmApiService = $bdmApiService;
    }

    public function acceuil()
    {
        return view('Front.acceuil');
    }

    public function redirectForm(Request $request)
    {
        try {
            $responsePlateformes = $this->bdmApiService->getPlateformes();
            
            $plateformes = [];
            if (($responsePlateformes['statut'] ?? 0) === 1 && is_array($responsePlateformes['content'] ?? null)) {
                $plateformes = $responsePlateformes['content'];
            } else {
                Log::error("La réponse de l'API BDM pour les plateformes via BdmApiService est invalide.", ['response' => $responsePlateformes]);
                throw new \Exception("Réponse invalide de l'API pour les plateformes.");
            }

            // Prétraitement pour le paramètre d'URL 'airport'
            $selectedAirportId = null;
            $airportIdentifier = $request->query('airport');
            if ($airportIdentifier && !empty($plateformes)) {
                $airportIdentifierLower = strtolower($airportIdentifier);
                $airportMap = ['orly' => 'orly', 'cdg' => 'charles de gaulle'];

                foreach ($plateformes as $plateforme) {
                    $plateformeLibelleLower = strtolower($plateforme['libelle']);
                    // Vérifie si l'identifiant correspond à un ID directement
                    if ($airportIdentifier === $plateforme['id']) {
                        $selectedAirportId = $plateforme['id'];
                        break;
                    }
                    // Vérifie si l'identifiant est un alias (orly, cdg)
                    if (isset($airportMap[$airportIdentifierLower]) && str_contains($plateformeLibelleLower, $airportMap[$airportIdentifierLower])) {
                        $selectedAirportId = $plateforme['id'];
                        break;
                    }
                }
            }


            // Utilise le premier aéroport pour obtenir une liste de produits par défaut
            $firstPlateformeId = $plateformes[0]['id'] ?? null;
            $products = [];
            if ($firstPlateformeId) {
                $responseProducts = $this->bdmApiService->getProducts($firstPlateformeId);
                if (($responseProducts['statut'] ?? 0) === 1 && is_array($responseProducts['content'] ?? null)) {
                    $products = $responseProducts['content'];
                } else {
                    Log::error("La réponse de l'API BDM pour les produits via BdmApiService est invalide.", ['response' => $responseProducts]);
                    throw new \Exception("Réponse invalide de l'API pour les produits.");
                }
            }

        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des données pour le formulaire : " . $e->getMessage());
            return view('Front.formulaire-consigne', [
                'plateformes' => [],
                'products' => [],
                'selectedAirportId' => null, // Assurez-vous de le passer même en cas d'erreur
                'error' => "Impossible de charger les options de réservation pour le moment. Veuillez réessayer plus tard."
            ]);
        }

        return view('Front.formulaire-consigne', [
            'plateformes' => $plateformes,
            'products' => $products,
            'selectedAirportId' => $selectedAirportId
        ]);
    }

    // Note: The getPlateformes and getProducts methods are now handled by BdmApiService
    // and should no longer be present in FrontController directly.
    // The checkAvailability, getQuote, and getOptionsQuote methods below are correct.

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

    //Vérifie la disponibilité d'une plateforme à une date donnée.
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'idPlateforme' => 'required|string',
            'dateToCheck' => 'required|string',
        ]);
        
        Log::info('Appel à l\'API BDM pour la disponibilité via BdmApiService', ['data' => $validated]);

        try {
            $response = $this->bdmApiService->checkAvailability(
                $validated['idPlateforme'],
                $validated['dateToCheck']
            );
            
            return response()->json($response, 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification de la disponibilité via BdmApiService', ['error' => $e->getMessage()]);
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

        Log::info('Appel à l\'API BDM pour les tarifs et lieux via BdmApiService', ['data' => $validated]);

        try {
            $response = $this->bdmApiService->getQuote(
                $validated['idPlateforme'],
                $validated['idService'],
                $validated['duree']
            );
            
            return response()->json($response, 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des tarifs/lieux via BdmApiService', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur technique lors de la récupération des données : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les prix dynamiques pour les options Priority et Premium depuis l\'API BDM.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOptionsQuote(Request $request)
    {
        $validated = $request->validate([
            'idPlateforme' => 'required|string',
            'cartItems' => 'required|array',
            'guestEmail' => 'nullable|email',
            'dateDepot' => 'required|string',
            'heureDepot' => 'required|string',
            'dateRecuperation' => 'required|string',
            'heureRecuperation' => 'required|string',
            'globalProductsData' => 'required|array',
        ]);

        $idPlateforme = $validated['idPlateforme'];
        $cartItemsFromFrontend = $validated['cartItems'];
        $guestEmail = $validated['guestEmail'] ?? null;
        $dateDepot = $validated['dateDepot'];
        $heureDepot = $validated['heureDepot'];
        $dateRecuperation = $validated['dateRecuperation'];
        $heureRecuperation = $validated['heureRecuperation'];
        $globalProductsData = $validated['globalProductsData'];

        Log::info('FrontController::getOptionsQuote - Données de requête reçues', [
            'idPlateforme' => $idPlateforme,
            'cartItemsFromFrontend' => $cartItemsFromFrontend,
            'guestEmail' => $guestEmail,
            'dates' => "{$dateDepot} {$heureDepot} - {$dateRecuperation} {$heureRecuperation}",
        ]);

        $baggages = [];
        $consigneServiceId = 'dfb8ac1b-8bb1-4957-afb4-1faedaf641b7';
        foreach ($cartItemsFromFrontend as $item) {
            $productInGlobal = collect($globalProductsData)->firstWhere('id', $item['productId']);
            if ($productInGlobal) {
                $baggages[] = [
                    'productId' => $productInGlobal['id'],
                    'serviceId' => $consigneServiceId,
                    'dateDebut' => "{$dateDepot}T{$heureDepot}:00Z",
                    'dateFin' => "{$dateRecuperation}T{$heureRecuperation}:00Z",
                    'quantity' => $item['quantity'],
                ];
            }
        }
        
        try {
            // Call the service to discover available options and their prices
            $response = $this->bdmApiService->getCommandeOptionsQuote(
                $idPlateforme,
                $baggages,
                $guestEmail
            );
            Log::info('FrontController::getOptionsQuote - Réponse de BdmApiService::getCommandeOptionsQuote', ['response' => $response]);
    
            if ($response && ($response['statut'] ?? 0) === 1 && isset($response['content'])) {
                $priorityOption = null;
                $premiumOption = null;
    
                // Process the API response to find our specific options
                foreach ($response['content'] ?? [] as $optionItem) {
                    $normalizedLibelle = Str::upper($optionItem['libelle'] ?? '');

                    if (str_contains($normalizedLibelle, 'PRIORITY') && !str_contains($normalizedLibelle, 'CHECK-OUT')) {
                        $priorityOption = $optionItem;
                    } elseif (str_contains($normalizedLibelle, 'PREMIUM')) {
                        $premiumOption = $optionItem;
                    }
                }

                Log::info('FrontController::getOptionsQuote - Options extraites', [
                    'priority' => $priorityOption,
                    'premium' => $premiumOption
                ]);
    
                return response()->json([
                    'statut' => 1,
                    'message' => 'Prix des options récupérés avec succès',
                    'content' => [
                        'priority' => $priorityOption,
                        'premium' => $premiumOption,
                    ]
                ]);
            } else {
                Log::error('FrontController::getOptionsQuote - Échec de la récupération des prix des options via BDM API', ['response' => $response]);
                return response()->json([
                    'statut' => 0,
                    'message' => $response['message'] ?? 'Impossible de récupérer les prix des options pour le moment.'
                ], 500);
            }
    
        } catch (\Exception $e) {
            Log::error('FrontController::getOptionsQuote - Erreur lors de la récupération des prix des options : ' . $e->getMessage());
            return response()->json([
                'statut' => 0,
                'message' => 'Erreur technique lors de la récupération des prix des options.'
            ], 500);
        }
    }
}
