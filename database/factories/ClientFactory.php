<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'organization_id' => Organization::factory(),
            'company_name' => fake()->company(),
            'contact_person' => fake()->name(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => fake()->country(),
            'zip_code' => fake()->postcode(),
            'tax_id' => fake()->numerify('###-##-####'),
            'industry' => fake()->randomElement(['Technology', 'Healthcare', 'Finance', 'Education', 'Retail', 'Manufacturing']),
            'client_type' => fake()->randomElement(['Regular', 'Premium', 'Enterprise']),
            'notes' => fake()->paragraph(),
        ];
    }
}
