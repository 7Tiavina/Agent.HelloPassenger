<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Commande;
use Illuminate\Support\Facades\Response;
use PDF; // Ajouter cette ligne pour importer la façade PDF

class CommandeController extends Controller
{
    /**
     * Affiche la liste des commandes pour le client authentifié.
     */
    public function index()
    {
        $client = Auth::guard('client')->user();
        $commandes = Commande::where('client_id', $client->id)->latest()->get();

        return view('mes-reservations', compact('commandes'));
    }

    /**
     * Affiche la facture HTML pour une commande donnée.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showInvoice($id)
    {
        $commande = Commande::findOrFail($id);

        // Optionnel : vérifier que le client a le droit de voir cette facture
        // if ($commande->client_id !== Auth::guard('client')->id()) {
        //     abort(403, 'Accès non autorisé.');
        // }

        return view('invoices.default', compact('commande'));
    }

    /**
     * Permet de télécharger la facture.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadInvoice($id)
    {
        $commande = Commande::findOrFail($id);

        // Optionnel : vérifier les droits
        // if ($commande->client_id !== Auth::guard('client')->id()) {
        //     abort(403, 'Accès non autorisé.');
        // }

        // Récupérer la référence de la commande pour le nom du fichier
        $reference = $commande->paymentClient->monetico_order_id ?? $commande->id;
        $fileName = 'facture-' . $reference . '.pdf';

        // Générer le PDF à partir de la vue
        $pdf = PDF::loadView('invoices.default', compact('commande'));

        // Retourner le PDF en téléchargement
        return $pdf->download($fileName);
    }
}

