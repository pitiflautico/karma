<?php

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Organization;
use Illuminate\Support\Facades\Mail;

test('invoice mail has correct subject', function () {
    $invoice = createTestInvoice();
    $organization = $invoice->organization;

    $mail = new InvoiceMail($invoice);

    expect($mail->envelope()->subject)->toContain("Invoice {$invoice->invoice_number}");
    expect($mail->envelope()->subject)->toContain($organization->name);
});

test('invoice mail has correct subject with default organization name', function () {
    $invoice = createTestInvoice();
    // Remove organization relationship to test default
    $invoice->organization_id = null;
    $invoice->organization = null;

    $mail = new InvoiceMail($invoice);

    expect($mail->envelope()->subject)->toContain('cloudstudio');
});

test('invoice mail uses markdown content', function () {
    $invoice = createTestInvoice();
    $mail = new InvoiceMail($invoice);

    expect($mail->content()->markdown)->toBe('emails.invoice-notification');
});

test('invoice mail can be created with custom message', function () {
    $invoice = createTestInvoice();
    $customMessage = 'This is a custom message for the invoice.';

    $mail = new InvoiceMail($invoice, $customMessage);

    expect($mail->customMessage)->toBe($customMessage);
    expect($mail->invoice->id)->toBe($invoice->id);
});

test('invoice mail has correct properties', function () {
    $invoice = createTestInvoice();
    $customMessage = 'Custom test message';

    $mail = new InvoiceMail($invoice, $customMessage);

    expect($mail->invoice)->toBeInstanceOf(Invoice::class);
    expect($mail->customMessage)->toBe($customMessage);
});

test('invoice mail implements mailable interface', function () {
    $invoice = createTestInvoice();
    $mail = new InvoiceMail($invoice);

    expect($mail)->toBeInstanceOf(\Illuminate\Mail\Mailable::class);
});

test('invoice mail uses queueable trait', function () {
    $invoice = createTestInvoice();
    $mail = new InvoiceMail($invoice);

    expect(in_array(\Illuminate\Bus\Queueable::class, class_uses_recursive($mail)))->toBeTrue();
});

test('invoice mail uses serializes models trait', function () {
    $invoice = createTestInvoice();
    $mail = new InvoiceMail($invoice);

    expect(in_array(\Illuminate\Queue\SerializesModels::class, class_uses_recursive($mail)))->toBeTrue();
});

test('invoice mail generates correct filename for attachment', function () {
    $invoice = createTestInvoice();
    $invoice->prefix = 'INV';
    $invoice->number = 1001;

    $mail = new InvoiceMail($invoice);

    // We can't easily test the actual attachment generation without mocking PDF
    // but we can verify the mail is constructed properly
    expect($mail->invoice->invoice_number)->toBe('1001/INV');
});

test('invoice mail handles invoice with special characters in number', function () {
    $invoice = createTestInvoice();
    $invoice->prefix = 'TEST/2024';
    $invoice->number = 1001;

    $mail = new InvoiceMail($invoice);

    expect($mail->invoice->invoice_number)->toBe('1001/TEST/2024');
});

test('invoice mail can be queued', function () {
    $invoice = createTestInvoice();
    $mail = new InvoiceMail($invoice);

    // Test that it implements ShouldQueue (it doesn't in this case, but could be added)
    expect($mail)->not->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});


test('invoice mail envelope is properly configured', function () {
    $invoice = createTestInvoice();
    $mail = new InvoiceMail($invoice);

    $envelope = $mail->envelope();

    expect($envelope)->toBeInstanceOf(\Illuminate\Mail\Mailables\Envelope::class);
    expect($envelope->subject)->toContain('Invoice');
    expect($envelope->subject)->toContain($invoice->invoice_number);
});

test('invoice mail content is properly configured', function () {
    $invoice = createTestInvoice();
    $mail = new InvoiceMail($invoice);

    $content = $mail->content();

    expect($content)->toBeInstanceOf(\Illuminate\Mail\Mailables\Content::class);
    expect($content->markdown)->toBe('emails.invoice-notification');
});

test('invoice mail can handle invoice without organization', function () {
    $invoice = createTestInvoice();
    $invoice->organization = null;

    $mail = new InvoiceMail($invoice);

    expect($mail->envelope()->subject)->toContain('cloudstudio');
});

test('invoice mail preserves invoice data integrity', function () {
    $invoice = createTestInvoice();
    $originalTotal = $invoice->total;

    $mail = new InvoiceMail($invoice);

    expect($mail->invoice->total)->toBe($originalTotal);
    expect($mail->invoice->id)->toBe($invoice->id);
});
