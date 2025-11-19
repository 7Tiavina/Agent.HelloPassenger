<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment; // Import pour les pièces jointes
use App\Models\Commande; // Import du modèle Commande

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $commande;
    public $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(Commande $commande, string $pdfPath)
    {
        $this->commande = $commande;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $reference = $this->commande->paymentClient->monetico_order_id ?? $this->commande->id;
        return new Envelope(
            subject: 'Confirmation de votre commande n° ' . $reference . ' - HelloPassenger',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order_confirmation',
            with: [
                'commande' => $this->commande,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $reference = $this->commande->paymentClient->monetico_order_id ?? $this->commande->id;
        return [
            Attachment::fromPath($this->pdfPath)
                      ->as('facture-' . $reference . '.pdf')
                      ->withMime('application/pdf'),
        ];
    }
}
