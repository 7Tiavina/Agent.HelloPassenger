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
    public ?string $invoiceBase64; // Renommé et rendu nullable

    /**
     * Create a new message instance.
     */
    public function __construct(Commande $commande, ?string $invoiceBase64 = null) // Accepte Base64, nullable
    {
        $this->commande = $commande;
        $this->invoiceBase64 = $invoiceBase64;
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
        if (!$this->invoiceBase64) {
            \Illuminate\Support\Facades\Log::debug('No invoiceBase64 found to attach for command ' . ($this->commande->id ?? 'unknown'));
            return []; // Pas de facture à attacher
        }

        \Illuminate\Support\Facades\Log::debug('Attempting to attach invoice (first 100 chars): ' . substr($this->invoiceBase64, 0, 100));
        \Illuminate\Support\Facades\Log::debug('Invoice Base64 length to attach: ' . strlen($this->invoiceBase64));

        $reference = $this->commande->paymentClient->monetico_order_id ?? $this->commande->id;
        return [
            Attachment::fromData(fn () => base64_decode($this->invoiceBase64))
                      ->as('facture-' . $reference . '.pdf')
                      ->withMime('application/pdf'),
        ];
    }
}
