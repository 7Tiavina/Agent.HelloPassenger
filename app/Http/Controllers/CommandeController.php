<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Commande; // N'oubliez pas d'importer le modèle Commande
use Illuminate\Support\Facades\Response;

class CommandeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $commandes = Commande::where('user_id', $user->id)->latest()->get();

        return view('mes-reservations', compact('commandes'));
    }

    public function downloadInvoice($id)
    {
        $commande = Commande::findOrFail($id);

        // Vérifier que l'utilisateur authentifié a le droit de voir cette facture (optionnel mais recommandé)
        // if ($commande->client_id !== Auth::guard('client')->id()) {
        //     abort(403, 'Accès non autorisé.');
        // }

        if (empty($commande->invoice_content)) {
            abort(404, 'Contenu de la facture non trouvé.');
        }

        // Décoder le contenu qui est en base64
        $pdfContent = base64_decode($commande->invoice_content);

        $fileName = 'facture-' . $commande->id . '.pdf';

        return Response::make($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
