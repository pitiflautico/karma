<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'organization_id' => Organization::factory(),
            'company_name' => $this->faker->company(),
            'company_address' => $this->faker->address(),
            'company_phone' => $this->faker->phoneNumber(),
            'company_email' => $this->faker->companyEmail(),
            'company_website' => $this->faker->url(),
            'logo_path' => 'logos/default-logo.png',
            'vat_number' => $this->faker->numerify('VAT-###-###-###'),
            'invoice_prefix' => 'INV-',
            'invoice_sequence' => 1,
            'default_currency' => 'EUR',
            'tax_iva' => 21,
            'tax_irpf' => 15,
            'goal' => $this->faker->numberBetween(1000, 5000),
            'legal_text_invoice' => $this->faker->paragraph(3),
        ];
    }
}
