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
        $commande = Commande::findOrFail($id);
        return view('invoices.default', compact('commande'));
    }

    public function downloadInvoice($id)
    {
        $commande = Commande::findOrFail($id);

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

        $reference = $commande->paymentClient->monetico_order_id ?? $commande->id;
        $fileName = "facture-$reference.pdf";

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"$fileName\"");
    }
}