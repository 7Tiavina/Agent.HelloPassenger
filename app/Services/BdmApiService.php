<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; // Add this import

class BdmApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.bdm.base_url');
    }

    /**
     * Récupère un token d'authentification pour l'API BDM, en le mettant en cache.
     * @return string
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getAuthToken(): string
    {
        // Tente de récupérer le token depuis le cache
        return Cache::remember('bdm_api_token', 3300, function () {
            Log::info('Cache BDM token expiré. Demande d\'un nouveau token.');

            $response = Http::post($this->baseUrl . '/User/Login', [
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
     * Récupère toutes les plateformes (aéroports) depuis l'API BDM pour le service de consigne.
     * @return array
     * @throws \Exception
     */
    public function getPlateformes(): ?array
    {
        $serviceId = 'dfb8ac1b-8bb1-4957-afb4-1faedaf641b7'; // ID du service de consigne
        Log::info("Récupération de la liste des plateformes pour le service {$serviceId}.");
        try {
            $token = $this->getAuthToken();
            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->get("{$this->baseUrl}/api/service/{$serviceId}/plateformes");

            $response->throw();

            return $response->json(); // Return full JSON response

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
    public function getProducts(string $idPlateforme): ?array
    {
        $serviceId = 'dfb8ac1b-8bb1-4957-afb4-1faedaf641b7'; // ID du service de consigne
        $defaultDuration = 1; // Durée par défaut en minutes pour obtenir la liste
        Log::info("Récupération des produits pour la plateforme {$idPlateforme} et le service {$serviceId}.");
        try {
            $token = $this->getAuthToken();
            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->get("{$this->baseUrl}/api/plateforme/{$idPlateforme}/service/{$serviceId}/{$defaultDuration}/produits");

            $response->throw();

            return $response->json(); // Return full JSON response

        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des produits BDM.", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Vérifie la disponibilité d'une plateforme à une date donnée.
     * @param string $idPlateforme
     * @param string $dateToCheck
     * @return array|null
     * @throws \Exception
     */
    public function checkAvailability(string $idPlateforme, string $dateToCheck): ?array
    {
        Log::info('Appel à l\'API BDM pour la disponibilité', ['idPlateforme' => $idPlateforme, 'dateToCheck' => $dateToCheck]);

        try {
            $token = $this->getAuthToken();
            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->get("{$this->baseUrl}/api/plateforme/{$idPlateforme}/date/{$dateToCheck}");
            
            Log::info('Réponse de l\'API BDM (disponibilité)', ['status' => $response->status(), 'body' => $response->json()]);

            $response->throw(); // Lance une exception pour les erreurs HTTP

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification de la disponibilité via BDM API', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Récupère les tarifs (produits) et les lieux pour une plateforme, un service et une durée donnés.
     * @param string $idPlateforme
     * @param string $idService
     * @param int $duree
     * @return array|null
     * @throws \Exception
     */
    public function getQuote(string $idPlateforme, string $idService, int $duree): ?array
    {
        Log::info('Appel à l\'API BDM pour les tarifs et lieux', ['idPlateforme' => $idPlateforme, 'idService' => $idService, 'duree' => $duree]);

        try {
            $token = $this->getAuthToken();
            $baseUrl = $this->baseUrl;

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
                throw new \Exception('Erreur lors de la communication avec le service de réservation.');
            }

            $productsResult = $productsResponse->json();
            $lieuxResult = $lieuxResponse->json();

            // Vérifier si le statut interne de l'API BDM indique un échec
            if (($productsResult['statut'] ?? 0) !== 1 || ($lieuxResult['statut'] ?? 0) !== 1) {
                Log::error("Réponse API BDM avec un statut d'échec dans le pool getQuote.", [
                    'products_response' => $productsResult,
                    'lieux_response' => $lieuxResult,
                ]);
                throw new \Exception("Les données de réservation n'ont pas pu être chargées entièrement.");
            }

            // Si tout réussit, on construit la réponse
            return [
                'statut' => 1,
                'message' => 'Données récupérées',
                'content' => [
                    'products' => $productsResult['content'] ?? [],
                    'lieux' => $lieuxResult['content'] ?? [],
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des tarifs/lieux via BDM API', ['error' => $e->getMessage()]);
            throw $e;
        }
    }


    /**
     * Effectue une requête POST à l'API BDM pour obtenir les prix des options.
     *
     * @param string $idPlateforme L'ID de la plateforme (aéroport).
     * @param array $baggages Les lignes de commande pour les bagages.
     * @param array $options Les options à évaluer (ex: Priority, Premium).
     * @param string $guestEmail L'email de l'invité, si disponible.
     * @return array|null Les prix des options ou null en cas d'erreur.
     */
    public function getCommandeOptionsQuote(string $idPlateforme, array $baggages, array $options, ?string $guestEmail = null): ?array
    {
        $url = "{$this->baseUrl}/api/plateforme/{$idPlateforme}/commande/options?lg=fr";

        // Construire commandeLignes à partir des baggages
        $commandeLignes = array_map(function($baggage) {
            return [
                "idProduit" => $baggage['productId'], // Assurez-vous que l'ID produit est le bon pour l'API BDM
                "idService" => $baggage['serviceId'] ?? "dfb8ac1b-8bb1-4957-afb4-1faedaf641b7", // ID Service consigne
                "dateDebut" => $baggage['dateDebut'],
                "dateFin" => $baggage['dateFin'],
                "prixTTC" => 0, // Sera calculé par BDM
                "prixTTCAvantRemise" => 0,
                "tauxRemise" => 0,
                "quantite" => $baggage['quantity']
            ];
        }, $baggages);

        // Construire commandeOptions à partir des options demandées (Priority, Premium)
        $commandeOptions = array_map(function($option) use ($baggages) {
            // Pour obtenir le prix des options, nous devons envoyer au moins les dates des bagages
            // Utilisons la première date de bagage disponible comme référence si aucune option spécifique n'est passée
            $dateDebut = $baggages[0]['dateDebut'] ?? now()->toIso8601String();
            $dateFin = $baggages[0]['dateFin'] ?? now()->addDay()->toIso8601String();

            return [
                "idProduit" => $option['productId'], // ID Produit pour Priority/Premium
                "idService" => $option['serviceId'] ?? "dfb8ac1b-8bb1-4957-afb4-1faedaf641b7", // ID Service consigne
                "dateDebut" => $dateDebut,
                "dateFin" => $dateFin,
                "prixTTC" => 0, // Sera calculé par BDM
                "prixTTCAvantRemise" => 0,
                "tauxRemise" => 0,
                "quantite" => 1 // Les options sont généralement à l'unité
            ];
        }, $options);

        // Données client minimales pour la requête de devis d'options
        $clientData = [
            "email" => $guestEmail ?? "temp@hellopassenger.com", // Utiliser un email invité ou par défaut
            "telephone" => "0000000000",
            "nom" => "Passager",
            "prenom" => "Temp",
            "civilite" => "M.",
            "nomSociete" => "",
            "adresse" => "Adresse inconnue",
            "complementAdresse" => "",
            "ville" => "Ville inconnue",
            "codePostal" => "00000",
            "pays" => "FRA"
        ];
        
        // Assurez-vous que l'email est valide, car l'API BDM renvoie 400 sinon
        if (!filter_var($clientData['email'], FILTER_VALIDATE_EMAIL)) {
            $clientData['email'] = "temp@hellopassenger.com";
        }


        $payload = [
            "commandeLignes" => $commandeLignes,
            "commandeOptions" => $commandeOptions,
            "commandeInfos" => [
                "modeTransport" => "Inconnu",
                "lieu" => "Inconnu",
                "commentaires" => "Devis options"
            ],
            "client" => $clientData
        ];

        Log::info('BdmApiService::getCommandeOptionsQuote - Payload envoyé à l\'API BDM', ['payload' => $payload]);

        try {
            $token = $this->getAuthToken(); // Get token dynamically
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            Log::info('BdmApiService::getCommandeOptionsQuote - Réponse brute de l\'API BDM', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            else {
                Log::error('Erreur API BDM lors de la récupération des prix des options', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'payload' => $payload
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception lors de l\'appel API BDM pour les prix des options', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            return null;
        }
    }
}