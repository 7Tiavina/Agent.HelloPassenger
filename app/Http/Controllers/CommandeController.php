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

        // --- 1. CONTENU TEXTE SIMPLE POUR LE PDF --- //
        $text = "Facture HelloPassenger\n\n";
        $text .= "Référence : " . ($commande->paymentClient->monetico_order_id ?? $commande->id) . "\n";
        $text .= "Client : " . $commande->nom . " " . $commande->prenom . "\n";
        $text .= "Email  : " . $commande->email . "\n";
        $text .= "Téléphone : " . $commande->telephone . "\n\n";
        $text .= "Détails de la commande :\n";
        $text .= "- Service : " . $commande->service . "\n";
        $text .= "- Prix : " . $commande->prix . " Rs\n";
        $text .= "- Date : " . $commande->created_at->format('d/m/Y H:i') . "\n\n";
        $text .= "Merci pour votre confiance.\nHelloPassenger";

        // --- 2. GENERATION PDF BRUT --- //
        $pdf = "%PDF-1.4
    1 0 obj
    << /Type /Catalog /Pages 2 0 R >>
    endobj
    2 0 obj
    << /Type /Pages /Kids [3 0 R] /Count 1 >>
    endobj
    3 0 obj
    << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>
    endobj
    4 0 obj
    << /Length " . strlen($text) . " >>
    stream
    BT
    /F1 12 Tf
    50 750 Td
    (" . str_replace(["\n", "(", ")"], [" ) Tj\n0 -15 Td (", "\\(", "\\)"], $text) . ") Tj
    ET
    endstream
    endobj
    5 0 obj
    << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
    endobj
    xref
    0 6
    0000000000 65535 f
    0000000010 00000 n
    0000000053 00000 n
    0000000102 00000 n
    0000000250 00000 n
    0000000400 00000 n
    trailer
    << /Size 6 /Root 1 0 R >>
    startxref
    520
    %%EOF";

        // Nom du fichier
        $reference = $commande->paymentClient->monetico_order_id ?? $commande->id;
        $fileName = "facture-$reference.pdf";

        // --- 3. RETOURNER LE PDF --- //
        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"$fileName\"");
    }

}

