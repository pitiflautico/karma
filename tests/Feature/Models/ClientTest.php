<?php

use App\Models\Client;
use App\Models\User;
use App\Models\Organization;
use App\Models\Invoice;
use App\Enums\Client\CountryEnum;
use App\Enums\Client\IndustryEnum;
use App\Enums\Client\TypeEnum;

test('client model uses uuids', function () {
    $client = Client::factory()->create();

    expect($client->id)->toBeInstanceOf(\Ramsey\Uuid\Lazy\LazyUuidFromString::class);
    expect(Str::isUuid((string) $client->id))->toBeTrue();
});

test('client has fillable attributes', function () {
    $user = createTestUser();
    $organization = createTestOrganization();

    $client = Client::factory()->create([
        'user_id' => $user->id,
        'organization_id' => $organization->id,
        'company_name' => 'Test Company',
        'contact_person' => 'John Doe',
        'email' => 'john@testcompany.com',
        'phone' => '+1234567890',
        'currency' => 'EUR',
        'country' => CountryEnum::ES->value,
        'industry' => IndustryEnum::TECHNOLOGY->value,
        'client_type' => TypeEnum::COMPANY->value,
    ]);

    expect($client->company_name)->toBe('Test Company');
    expect($client->contact_person)->toBe('John Doe');
    expect($client->email)->toBe('john@testcompany.com');
    expect($client->phone)->toBe('+1234567890');
    expect($client->currency)->toBe('EUR');
});

test('client belongs to user', function () {
    $user = createTestUser();
    $client = Client::factory()->create(['user_id' => $user->id]);

    expect($client->user)->toBeInstanceOf(User::class);
    expect((string) $client->user->id)->toBe((string) $user->id);
});

test('client belongs to organization', function () {
    $organization = createTestOrganization();
    $client = Client::factory()->create(['organization_id' => $organization->id]);

    expect($client->organization)->toBeInstanceOf(Organization::class);
    expect((string) $client->organization->id)->toBe((string) $organization->id);
});

test('client has many invoices', function () {
    $client = createTestClient();

    expect($client->invoices())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('client can have invoices', function () {
    $client = createTestClient();
    $invoice = Invoice::factory()->create(['client_id' => $client->id]);

    expect($client->invoices)->toHaveCount(1);
    expect((string) $client->invoices->first()->id)->toBe((string) $invoice->id);
});

test('client uses soft deletes', function () {
    $client = createTestClient();

    $client->delete();

    expect($client->trashed())->toBeTrue();

    $client->restore();

    expect($client->trashed())->toBeFalse();
});

test('client uses belongs to organization scope', function () {
    $client = createTestClient();

    expect(in_array('App\Models\Concerns\BelongsToOrganizationScope', class_uses_recursive($client)))->toBeTrue();
});

test('client factory creates valid client', function () {
    $client = Client::factory()->create();

    expect($client)->toBeInstanceOf(Client::class);
    expect($client->id)->toBeInstanceOf(\Ramsey\Uuid\Lazy\LazyUuidFromString::class);
    expect($client->company_name)->toBeString();
    expect($client->email)->toBeString();
    expect($client->organization_id)->toBeInstanceOf(\Ramsey\Uuid\Lazy\LazyUuidFromString::class);
});

test('client can be created with specific country enum', function () {
    $client = Client::factory()->create([
        'country' => CountryEnum::ES->value,
    ]);

    expect($client->country)->toBe(CountryEnum::ES->value);
});

test('client can be created with specific industry enum', function () {
    $client = Client::factory()->create([
        'industry' => IndustryEnum::TECHNOLOGY->value,
    ]);

    expect($client->industry)->toBe(IndustryEnum::TECHNOLOGY->value);
});

test('client can be created with specific client type enum', function () {
    $client = Client::factory()->create([
        'client_type' => TypeEnum::COMPANY->value,
    ]);

    expect($client->client_type)->toBe(TypeEnum::COMPANY->value);
});

test('client casts client_type correctly', function () {
    $client = Client::factory()->create([
        'client_type' => TypeEnum::INDIVIDUAL->value,
    ]);

    expect($client->client_type)->toBeString();
    expect($client->client_type)->toBe(TypeEnum::INDIVIDUAL->value);
});

test('client can be soft deleted and restored', function () {
    $client = createTestClient();

    // Soft delete
    $client->delete();
    expect(Client::find($client->id))->toBeNull();
    expect(Client::withTrashed()->find($client->id))->not->toBeNull();

    // Restore
    $client->restore();
    expect(Client::find($client->id))->not->toBeNull();
});

test('client withTrashed scope works', function () {
    $client = createTestClient();
    $clientId = $client->id;
    $client->delete();

    $trashedClients = Client::withTrashed()->get();
    $activeClients = Client::withoutTrashed()->get();

    expect($trashedClients->pluck('id')->contains($clientId))->toBeTrue();
    expect($activeClients->pluck('id')->contains($clientId))->toBeFalse();
});

test('client onlyTrashed scope works', function () {
    $client = createTestClient();
    $clientId = $client->id;
    $client->delete();

    $trashedClients = Client::onlyTrashed()->get();
    $activeClients = Client::all();

    expect($trashedClients->pluck('id')->contains($clientId))->toBeTrue();
    expect($activeClients->pluck('id')->contains($clientId))->toBeFalse();
});
