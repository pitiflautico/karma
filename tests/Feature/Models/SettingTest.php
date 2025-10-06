<?php

use App\Models\Setting;
use App\Models\User;
use App\Models\Organization;


test('setting has fillable attributes', function () {
    $user = createTestUser();
    $organization = createTestOrganization();

    $setting = Setting::factory()->create([
        'user_id' => $user->id,
        'organization_id' => $organization->id,
        'company_name' => 'Test Company',
        'company_email' => 'test@company.com',
        'vat_number' => 'ES12345678',
        'invoice_prefix' => 'INV',
        'default_currency' => 'EUR',
        'tax_iva' => 21,
        'tax_irpf' => 15,
    ]);

    expect($setting->company_name)->toBe('Test Company');
    expect($setting->company_email)->toBe('test@company.com');
    expect($setting->vat_number)->toBe('ES12345678');
    expect($setting->invoice_prefix)->toBe('INV');
    expect($setting->default_currency)->toBe('EUR');
    expect($setting->tax_iva)->toBe(21);
    expect($setting->tax_irpf)->toBe(15);
});

test('setting belongs to user', function () {
    $user = createTestUser();
    $setting = Setting::factory()->create(['user_id' => $user->id]);

    expect($setting->user)->toBeInstanceOf(User::class);
    expect((string) $setting->user->id)->toBe((string) $user->id);
});

test('setting belongs to organization', function () {
    $organization = createTestOrganization();
    $setting = Setting::factory()->create(['organization_id' => $organization->id]);

    expect($setting->organization)->toBeInstanceOf(Organization::class);
    expect((string) $setting->organization->id)->toBe((string) $organization->id);
});

test('setting uses belongs to organization scope', function () {
    $setting = Setting::factory()->create();

    expect(in_array('App\Models\Concerns\BelongsToOrganizationScope', class_uses_recursive($setting)))->toBeTrue();
});

test('setting factory creates valid setting', function () {
    $setting = Setting::factory()->create();

    expect($setting)->toBeInstanceOf(Setting::class);
    expect(Str::isUuid((string) $setting->id))->toBeTrue();
    expect($setting->company_name)->toBeString();
    expect(Str::isUuid((string) $setting->organization_id))->toBeTrue();
});

test('setting can store company information', function () {
    $setting = Setting::factory()->create([
        'company_name' => 'ACME Corp',
        'company_address' => '123 Main St, City, Country',
        'company_phone' => '+1234567890',
        'company_email' => 'info@acme.com',
        'company_website' => 'https://acme.com',
        'vat_number' => 'VAT123456',
    ]);

    expect($setting->company_name)->toBe('ACME Corp');
    expect($setting->company_address)->toBe('123 Main St, City, Country');
    expect($setting->company_phone)->toBe('+1234567890');
    expect($setting->company_email)->toBe('info@acme.com');
    expect($setting->company_website)->toBe('https://acme.com');
    expect($setting->vat_number)->toBe('VAT123456');
});

test('setting can store invoice configuration', function () {
    $setting = Setting::factory()->create([
        'invoice_prefix' => 'INV-2024-',
        'invoice_sequence' => 1001,
        'default_currency' => 'USD',
    ]);

    expect($setting->invoice_prefix)->toBe('INV-2024-');
    expect($setting->invoice_sequence)->toBe(1001);
    expect($setting->default_currency)->toBe('USD');
});

test('setting can store tax configuration', function () {
    $setting = Setting::factory()->create([
        'tax_iva' => 21,
        'tax_irpf' => 15.5,
    ]);

    expect($setting->tax_iva)->toBe(21);
    expect($setting->tax_irpf)->toBe(15.5);
});

test('setting can store pricing information', function () {
    $setting = Setting::factory()->create([
        'goal' => 50000,
    ]);

    expect($setting->goal)->toBe(50000);
});

test('setting can store legal text', function () {
    $legalText = 'Payment terms: 30 days from invoice date.';
    $setting = Setting::factory()->create([
        'legal_text_invoice' => $legalText,
    ]);

    expect($setting->legal_text_invoice)->toBe($legalText);
});

test('setting can store logo path', function () {
    $setting = Setting::factory()->create([
        'logo_path' => 'logos/company-logo.png',
    ]);

    expect($setting->logo_path)->toBe('logos/company-logo.png');
});

test('setting belongs to correct user relationship', function () {
    $user = createTestUser();
    $setting = Setting::factory()->create(['user_id' => $user->id]);

    expect((string) $setting->user->id)->toBe((string) $user->id);
    expect((string) $user->settings->id)->toBe((string) $setting->id);
});

test('setting belongs to correct organization relationship', function () {
    $organization = createTestOrganization();
    $setting = Setting::factory()->create(['organization_id' => $organization->id]);

    expect((string) $setting->organization->id)->toBe((string) $organization->id);
    expect($organization->settings->contains($setting))->toBeTrue();
});

test('setting can have default values', function () {
    $setting = new Setting();

    expect($setting->tax_iva)->toBeNull();
    expect($setting->tax_irpf)->toBeNull();
    expect($setting->default_currency)->toBeNull();
});

test('setting can be updated', function () {
    $setting = createTestSetting();

    $setting->update([
        'company_name' => 'Updated Company Name',
        'tax_iva' => 25,
    ]);

    expect($setting->fresh()->company_name)->toBe('Updated Company Name');
    expect($setting->fresh()->tax_iva)->toBe(25);
});
