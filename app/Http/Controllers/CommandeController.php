<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Commande;
use Illuminate\Support\Facades\Response;

class CommandeController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        $commandes = Commande::where('client_id', $client->id)->latest()->get();

        return view('mes-reservations', compact('commandes'));
    }

    public function showInvoice($id)
    {
        try {
            $commande = Commande::with('paymentClient')->findOrFail($id); // Charger paymentClient pour la référence

            if (!$commande->invoice_content) {
                \Illuminate\Support\Facades\Log::warning("Facture Base64 non trouvée pour la commande ID: {$id}.");
                abort(404, 'Contenu de la facture introuvable.');
            }

            $pdfContent = base64_decode($commande->invoice_content);

            if ($pdfContent === false) {
                \Illuminate\Support\Facades\Log::error("Erreur lors du décodage Base64 pour la facture de la commande ID: {$id}.");
                abort(500, 'Erreur lors de la lecture de la facture.');
            }
            
            $reference = $commande->paymentClient->monetico_order_id ?? $commande->id;
            $fileName = "facture-HelloPassenger-{$reference}.pdf";

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $fileName . '"'); // inline pour l'aperçu

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Illuminate\Support\Facades\Log::warning("Commande ID: {$id} non trouvée lors de la tentative d'affichage de la facture.");
            abort(404, 'Commande introuvable.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur lors du service de la facture pour la commande ID: {$id}: " . $e->getMessage(), ['exception' => $e]);
            abort(500, 'Une erreur est survenue lors de la récupération de la facture.');
        }
    }


}