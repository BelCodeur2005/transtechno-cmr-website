<?php

namespace App\Mail;

use App\Models\Quotation;
use App\Support\DevisPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

/**
 * Envoi du devis (PDF en pièce jointe) au client.
 */
class DevisMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quotation $quotation,
        public ?string $customMessage = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre devis #' . $this->quotation->reference . ' — Transtechno Cameroun',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.devis');
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => DevisPdf::render($this->quotation)->output(),
                DevisPdf::filename($this->quotation),
            )->withMime('application/pdf'),
        ];
    }
}
