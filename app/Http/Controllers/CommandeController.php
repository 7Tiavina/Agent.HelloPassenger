<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Commande;
use Illuminate\Support\Facades\Response;

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
     * Pour l'instant, affiche simplement la facture HTML.
     * Pour générer un PDF, un package comme barryvdh/laravel-dompdf serait nécessaire.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function downloadInvoice($id)
    {
        $commande = Commande::findOrFail($id);

        // Optionnel : vérifier les droits
        // if ($commande->client_id !== Auth::guard('client')->id()) {
        //     abort(403, 'Accès non autorisé.');
        // }

        // Pour une vraie génération PDF, il faudrait installer un package et faire :
        // $pdf = \PDF::loadView('invoices.default', compact('commande'));
        // return $pdf->download('facture-'.$commande->id.'.pdf');

        // En attendant, on affiche la facture HTML.
        return view('invoices.default', compact('commande'));
    }
}

