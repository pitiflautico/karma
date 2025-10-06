<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Invoice $invoice,
        public string $customMessage = ''
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $companyName = $this->invoice->organization->name ?? 'cloudstudio';

        return new Envelope(
            subject: "📄 Invoice {$this->invoice->invoice_number} from {$companyName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Generate PDF
        $pdf = Pdf::loadView('invoices.template', ['invoice' => $this->invoice])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'encoding' => 'UTF-8',
                'defaultFontEncoding' => 'UTF-8',
                'dpi' => 150,
                'enable_font_subsetting' => false,
                'isFontSubsettingEnabled' => false,
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
                'defaultMediaType' => 'print',
                'isCssFloatEnabled' => true,
            ]);

        $filename = "invoice-" . str_replace(['/', '\\'], '-', $this->invoice->invoice_number) . ".pdf";

        return [
            Attachment::fromData(fn() => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }
}
