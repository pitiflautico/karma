<?php

use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/invoice/{invoice}/pdf', function (Invoice $invoice) {
    $invoice->load(['client', 'organization', 'items']);

    try {
        $pdf = Pdf::loadView('invoices.template', ['invoice' => $invoice])
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
        $filename = "invoice-" . str_replace(['/', '\\'], '-', $invoice->prefix . '-' . $invoice->number) . ".pdf";
        return $pdf->stream($filename);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('invoice.pdf.view')->middleware(['auth', 'signed']);
