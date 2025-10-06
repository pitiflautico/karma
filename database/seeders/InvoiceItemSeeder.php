<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Seeder;

class InvoiceItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all invoices
        $invoices = Invoice::all();

        if ($invoices->isNotEmpty()) {
            // Create invoice items for each invoice
            $invoices->each(function ($invoice) {
                // Create 1-5 invoice items for each invoice
                $totalAmount = 0;

                for ($i = 0; $i < rand(1, 5); $i++) {
                    $quantity = fake()->numberBetween(1, 10);
                    $unitPrice = fake()->numberBetween(5000, 100000); // In cents
                    $taxRate = fake()->randomElement([0, 10, 20, 21]); // In percentage
                    $totalLine = $quantity * $unitPrice * (1 + ($taxRate / 100));

                    InvoiceItem::create([
                        'user_id' => $invoice->user_id,
                        'invoice_id' => $invoice->id,
                        'organization_id' => $invoice->organization_id,
                        'description' => fake()->sentence(),
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'tax_rate' => $taxRate,
                        'total_line' => (int)$totalLine,
                    ]);

                    $totalAmount += (int)$totalLine;
                }

                // Update the invoice total
                $invoice->update(['total' => $totalAmount]);
            });
        }
    }
}
