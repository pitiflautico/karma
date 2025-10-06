<?php

use App\Models\Invoice;
use App\Models\User;
use App\Models\Client;
use App\Models\Organization;
use App\Models\InvoiceItem;
use App\Enums\Invoice\StatusEnum;
use Carbon\Carbon;

test('invoice model uses uuids', function () {
    $invoice = Invoice::factory()->create();

    expect($invoice->id)->toBeInstanceOf(\Ramsey\Uuid\Lazy\LazyUuidFromString::class);
    expect(Str::isUuid((string) $invoice->id))->toBeTrue();
});

test('invoice has fillable attributes', function () {
    $user = createTestUser();
    $client = createTestClient();
    $organization = createTestOrganization();

    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'organization_id' => $organization->id,
        'prefix' => 'INV',
        'number' => 1001,
        'date_issued' => now(),
        'due_date' => now()->addDays(30),
        'status' => StatusEnum::DRAFT->value,
    ]);

    expect($invoice->prefix)->toBe('INV');
    expect($invoice->number)->toBe(1001);
    expect($invoice->status)->toBe(StatusEnum::DRAFT);
});

test('invoice casts attributes correctly', function () {
    $invoice = Invoice::factory()->create([
        'date_issued' => now(),
        'due_date' => now()->addDays(30),
        'total' => 10000, // 100.00 in cents
        'status' => StatusEnum::SENT->value,
        'paid_at' => now(),
        'number' => 1001,
    ]);

    expect($invoice->date_issued)->toBeInstanceOf(Carbon::class);
    expect($invoice->due_date)->toBeInstanceOf(Carbon::class);
    expect($invoice->paid_at)->toBeInstanceOf(Carbon::class);
    expect($invoice->total)->toBeInt();
    expect($invoice->status)->toBeInstanceOf(StatusEnum::class);
    expect($invoice->number)->toBeInt();
});

test('invoice belongs to user', function () {
    $user = createTestUser();
    $invoice = Invoice::factory()->create(['user_id' => $user->id]);

    expect($invoice->user)->toBeInstanceOf(User::class);
    expect((string) $invoice->user->id)->toBe((string) $user->id);
});

test('invoice belongs to client', function () {
    $client = createTestClient();
    $invoice = Invoice::factory()->create(['client_id' => $client->id]);

    expect($invoice->client)->toBeInstanceOf(Client::class);
    expect((string) $invoice->client->id)->toBe((string) $client->id);
});

test('invoice belongs to organization', function () {
    $organization = createTestOrganization();
    $invoice = Invoice::factory()->create(['organization_id' => $organization->id]);

    expect($invoice->organization)->toBeInstanceOf(Organization::class);
    expect((string) $invoice->organization->id)->toBe((string) $organization->id);
});

test('invoice has many items', function () {
    $invoice = createTestInvoice();

    expect($invoice->items())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('invoice can have items', function () {
    $invoice = createTestInvoice();
    $item = InvoiceItem::factory()->create(['invoice_id' => $invoice->id]);

    expect($invoice->items)->toHaveCount(1);
    expect((string) $invoice->items->first()->id)->toBe((string) $item->id);
});

test('invoice uses soft deletes', function () {
    $invoice = createTestInvoice();

    $invoice->delete();

    expect($invoice->trashed())->toBeTrue();

    $invoice->restore();

    expect($invoice->trashed())->toBeFalse();
});

test('invoice uses belongs to organization scope', function () {
    $invoice = createTestInvoice();

    expect(in_array('App\Models\Concerns\BelongsToOrganizationScope', class_uses_recursive($invoice)))->toBeTrue();
});

test('invoice generates invoice number correctly', function () {
    $invoice = Invoice::factory()->create([
        'prefix' => 'INV',
        'number' => 1001,
    ]);

    expect($invoice->invoice_number)->toBe('1001/INV');
});

test('invoice generates invoice number without prefix', function () {
    $invoice = Invoice::factory()->create([
        'prefix' => '',
        'number' => 1001,
    ]);

    expect($invoice->invoice_number)->toBe('1001');
});

test('invoice generates next number correctly', function () {
    // Clear existing invoices
    Invoice::query()->delete();

    $nextNumber = Invoice::getNextNumber('TEST');
    expect($nextNumber)->toBe(1);

    Invoice::factory()->create([
        'prefix' => 'TEST',
        'number' => 5,
    ]);

    $nextNumber = Invoice::getNextNumber('TEST');
    expect($nextNumber)->toBe(6);
});

test('invoice auto generates number on creation', function () {
    $invoice = Invoice::factory()->create([
        'prefix' => 'AUTO',
        // Don't set number
    ]);

    expect($invoice->number)->toBeGreaterThan(0);
});

test('invoice auto generates number with existing invoices', function () {
    Invoice::factory()->create([
        'prefix' => 'SEQ',
        'number' => 10,
    ]);

    $invoice = Invoice::factory()->create([
        'prefix' => 'SEQ',
        // Don't set number
    ]);

    expect($invoice->number)->toBeGreaterThan(10);
});

test('invoice can be created with draft status', function () {
    $invoice = Invoice::factory()->create([
        'status' => StatusEnum::DRAFT->value,
    ]);

    expect($invoice->status)->toBe(StatusEnum::DRAFT);
});

test('invoice can be created with sent status', function () {
    $invoice = Invoice::factory()->create([
        'status' => StatusEnum::SENT->value,
    ]);

    expect($invoice->status)->toBe(StatusEnum::SENT);
});

test('invoice can be created with paid status', function () {
    $invoice = Invoice::factory()->create([
        'status' => StatusEnum::PAID->value,
        'paid_at' => now(),
    ]);

    expect($invoice->status)->toBe(StatusEnum::PAID);
    expect($invoice->paid_at)->toBeInstanceOf(Carbon::class);
});

test('invoice factory creates valid invoice', function () {
    $invoice = Invoice::factory()->create();

    expect($invoice)->toBeInstanceOf(Invoice::class);
    expect($invoice->id)->toBeInstanceOf(\Ramsey\Uuid\Lazy\LazyUuidFromString::class);
    expect($invoice->number)->toBeInt();
    expect($invoice->total)->toBeInt();
    expect($invoice->status)->toBeInstanceOf(StatusEnum::class);
});

test('invoice can be soft deleted and restored', function () {
    $invoice = createTestInvoice();

    // Soft delete
    $invoice->delete();
    expect(Invoice::find($invoice->id))->toBeNull();
    expect(Invoice::withTrashed()->find($invoice->id))->not->toBeNull();

    // Restore
    $invoice->restore();
    expect(Invoice::find($invoice->id))->not->toBeNull();
});

test('invoice withTrashed scope works', function () {
    $invoice = createTestInvoice();
    $invoiceId = $invoice->id;
    $invoice->delete();

    $trashedInvoices = Invoice::withTrashed()->get();
    $activeInvoices = Invoice::withoutTrashed()->get();

    expect($trashedInvoices->pluck('id')->contains($invoiceId))->toBeTrue();
    expect($activeInvoices->pluck('id')->contains($invoiceId))->toBeFalse();
});

test('invoice onlyTrashed scope works', function () {
    $invoice = createTestInvoice();
    $invoiceId = $invoice->id;
    $invoice->delete();

    $trashedInvoices = Invoice::onlyTrashed()->get();
    $activeInvoices = Invoice::all();

    expect($trashedInvoices->pluck('id')->contains($invoiceId))->toBeTrue();
    expect($activeInvoices->pluck('id')->contains($invoiceId))->toBeFalse();
});
