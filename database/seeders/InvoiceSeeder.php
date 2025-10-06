<?php

namespace Database\Seeders;

use App\Enums\Invoice\StatusEnum;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all clients
        $clients = Client::all();

        if ($clients->isNotEmpty()) {
            // Create invoices for each client
            $clients->each(function ($client) {
                // Create 1-5 invoices for each client
                for ($i = 0; $i < rand(1, 5); $i++) {
                    $dateIssued = fake()->dateTimeBetween('-6 months', 'now');
                    $dueDate = (clone $dateIssued)->modify('+30 days');

                    Invoice::factory()->create([
                        'user_id' => $client->user_id,
                        'client_id' => $client->id,
                        'organization_id' => $client->organization_id,
                        'prefix' => 'J-',
                        'date_issued' => $dateIssued,
                        'due_date' => $dueDate,
                        'total' => 0, // Will be calculated after adding invoice items
                        'status' => fake()->randomElement([
                            StatusEnum::DRAFT->value,
                            StatusEnum::SENT->value,
                            StatusEnum::PAID->value,
                            StatusEnum::OVERDUE->value,
                        ]),
                    ]);
                }
            });
        }
    }
}
