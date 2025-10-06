<?php

use App\Models\Organization;
use App\Models\User;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Setting;

test('organization model uses uuids', function () {
    $organization = Organization::factory()->create();

    expect($organization->id)->toBeInstanceOf(\Ramsey\Uuid\Lazy\LazyUuidFromString::class);
    expect(Str::isUuid((string) $organization->id))->toBeTrue();
});

test('organization has fillable attributes', function () {
    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'slug' => 'test-org',
    ]);

    expect($organization->name)->toBe('Test Organization');
    expect($organization->slug)->toBe('test-org');
});

test('organization has many users', function () {
    $organization = createTestOrganization();

    expect($organization->users())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('organization can have users', function () {
    $organization = createTestOrganization();
    $user = User::factory()->create(['organization_id' => $organization->id]);

    expect($organization->users)->toHaveCount(1);
    expect((string) $organization->users->first()->id)->toBe((string) $user->id);
});

test('organization has many clients', function () {
    $organization = createTestOrganization();

    expect($organization->clients())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('organization can have clients', function () {
    $organization = createTestOrganization();
    $client = Client::factory()->create(['organization_id' => $organization->id]);

    expect($organization->clients)->toHaveCount(1);
    expect((string) $organization->clients->first()->id)->toBe((string) $client->id);
});

test('organization has many invoices', function () {
    $organization = createTestOrganization();

    expect($organization->invoices())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('organization can have invoices', function () {
    $organization = createTestOrganization();
    $invoice = Invoice::factory()->create(['organization_id' => $organization->id]);

    expect($organization->invoices)->toHaveCount(1);
    expect((string) $organization->invoices->first()->id)->toBe((string) $invoice->id);
});

test('organization has many invoice items', function () {
    $organization = createTestOrganization();

    expect($organization->invoiceItems())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('organization can have invoice items', function () {
    $organization = createTestOrganization();
    $item = InvoiceItem::factory()->create(['organization_id' => $organization->id]);

    expect($organization->invoiceItems)->toHaveCount(1);
    expect((string) $organization->invoiceItems->first()->id)->toBe((string) $item->id);
});

test('organization has many settings', function () {
    $organization = createTestOrganization();

    expect($organization->settings())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('organization can have settings', function () {
    $organization = createTestOrganization();
    $setting = Setting::factory()->create(['organization_id' => $organization->id]);

    expect($organization->settings)->toHaveCount(1);
    expect((string) $organization->settings->first()->id)->toBe((string) $setting->id);
});

test('organization factory creates valid organization', function () {
    $organization = Organization::factory()->create();

    expect($organization)->toBeInstanceOf(Organization::class);
    expect($organization->id)->toBeInstanceOf(\Ramsey\Uuid\Lazy\LazyUuidFromString::class);
    expect($organization->name)->toBeString();
    expect($organization->slug)->toBeString();
});

test('organization relationships work correctly', function () {
    $organization = createTestOrganization();

    // Create related models
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $client = Client::factory()->create(['organization_id' => $organization->id]);
    $invoice = Invoice::factory()->create(['organization_id' => $organization->id]);
    $item = InvoiceItem::factory()->create(['organization_id' => $organization->id]);
    $setting = Setting::factory()->create(['organization_id' => $organization->id]);

    // Test relationships
    expect($organization->users->pluck('id')->contains($user->id))->toBeTrue();
    expect($organization->clients->pluck('id')->contains($client->id))->toBeTrue();
    expect($organization->invoices->pluck('id')->contains($invoice->id))->toBeTrue();
    expect($organization->invoiceItems->pluck('id')->contains($item->id))->toBeTrue();
    expect($organization->settings->pluck('id')->contains($setting->id))->toBeTrue();
});

test('organization can access related models with correct counts', function () {
    $organization = createTestOrganization();

    // Create multiple related models
    User::factory()->count(3)->create(['organization_id' => $organization->id]);
    Client::factory()->count(2)->create(['organization_id' => $organization->id]);
    Invoice::factory()->count(5)->create(['organization_id' => $organization->id]);

    expect($organization->users()->count())->toBe(3);
    expect($organization->clients()->count())->toBe(2);
    expect($organization->invoices()->count())->toBe(5);
});

test('organization slug is unique', function () {
    $org1 = Organization::factory()->create(['slug' => 'unique-slug']);
    $org2 = Organization::factory()->create(['slug' => 'another-slug']);

    expect($org1->slug)->not->toBe($org2->slug);
});

test('organization can be found by slug', function () {
    $organization = Organization::factory()->create(['slug' => 'test-slug']);

    $found = Organization::where('slug', 'test-slug')->first();

    expect($found)->not->toBeNull();
    expect((string) $found->id)->toBe((string) $organization->id);
});
