<?php

namespace Database\Factories;

use App\Enums\Invoice\StatusEnum;
use App\Models\Organization;
use App\Models\User;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateIssued = fake()->dateTimeBetween('-6 months', 'now');
        $dueDate = (clone $dateIssued)->modify('+30 days');

        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'organization_id' => Organization::factory(),
            'prefix' => fake()->randomElement(['', 'INV-', 'J-', '2025-']),
            'number' => fake()->numberBetween(1, 1000),
            'date_issued' => $dateIssued,
            'due_date' => $dueDate,
            'total' => fake()->numberBetween(50000, 1000000), // In cents
            'status' => fake()->randomElement([
                StatusEnum::DRAFT->value,
                StatusEnum::SENT->value,
                StatusEnum::PAID->value,
                StatusEnum::OVERDUE->value,
            ]),
        ];
    }
}
