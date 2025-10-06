<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvoiceItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InvoiceItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $unitPrice = fake()->numberBetween(5000, 100000); // In cents
        $taxRate = fake()->randomElement([0, 10, 20, 21]); // In percentage
        $totalLine = $quantity * $unitPrice * (1 + ($taxRate / 100));

        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'invoice_id' => Invoice::factory(),
            'organization_id' => Organization::factory(),
            'description' => fake()->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'total_line' => (int)$totalLine,
        ];
    }
}
