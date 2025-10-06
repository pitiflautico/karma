<?php

use App\Models\InvoiceItem;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Organization;

test('invoice item model uses uuids', function () {
    $item = InvoiceItem::factory()->create();

    expect($item->id)->toBeInstanceOf(\Ramsey\Uuid\Lazy\LazyUuidFromString::class);
    expect(Str::isUuid((string) $item->id))->toBeTrue();
});

test('invoice item has fillable attributes', function () {
    $user = createTestUser();
    $invoice = createTestInvoice();
    $organization = createTestOrganization();

    $item = InvoiceItem::factory()->create([
        'user_id' => $user->id,
        'invoice_id' => $invoice->id,
        'organization_id' => $organization->id,
        'description' => 'Test Item',
        'quantity' => 2,
        'unit_price' => 5000, // 50.00 in cents
        'tax_rate' => 21,
        'irpf_rate' => 15,
    ]);

    expect($item->description)->toBe('Test Item');
    expect($item->quantity)->toBe(2);
    expect($item->unit_price)->toBe(5000);
    expect($item->tax_rate)->toBe(21);
    expect((float) $item->irpf_rate)->toBe(15.0);
});

test('invoice item casts attributes correctly', function () {
    $item = InvoiceItem::factory()->create([
        'quantity' => 5,
        'unit_price' => 10000,
        'price' => 50000,
        'tax_rate' => 21,
        'tax_total' => 10500,
        'irpf_rate' => 15.5,
        'irpf_total' => 8250,
        'total' => 60750,
        'total_line' => 60750,
    ]);

    expect($item->quantity)->toBeInt();
    expect($item->unit_price)->toBeInt();
    expect($item->price)->toBeInt();
    expect($item->tax_rate)->toBeInt();
    expect($item->tax_total)->toBeInt();
    expect(is_numeric($item->irpf_rate))->toBeTrue();
    expect($item->irpf_total)->toBeInt();
    expect($item->total)->toBeInt();
    expect($item->total_line)->toBeInt();
});

test('invoice item belongs to user', function () {
    $user = createTestUser();
    $item = InvoiceItem::factory()->create(['user_id' => $user->id]);

    expect($item->user)->toBeInstanceOf(User::class);
    expect((string) $item->user->id)->toBe((string) $user->id);
});

test('invoice item belongs to invoice', function () {
    $invoice = createTestInvoice();
    $item = InvoiceItem::factory()->create(['invoice_id' => $invoice->id]);

    expect($item->invoice)->toBeInstanceOf(Invoice::class);
    expect((string) $item->invoice->id)->toBe((string) $invoice->id);
});

test('invoice item belongs to organization', function () {
    $organization = createTestOrganization();
    $item = InvoiceItem::factory()->create(['organization_id' => $organization->id]);

    expect($item->organization)->toBeInstanceOf(Organization::class);
    expect((string) $item->organization->id)->toBe((string) $organization->id);
});

test('invoice item uses belongs to organization scope', function () {
    $item = InvoiceItem::factory()->create();

    expect(in_array('App\Models\Concerns\BelongsToOrganizationScope', class_uses_recursive($item)))->toBeTrue();
});

test('invoice item factory creates valid item', function () {
    $item = InvoiceItem::factory()->create();

    expect($item)->toBeInstanceOf(InvoiceItem::class);
    expect($item->id)->toBeInstanceOf(\Ramsey\Uuid\Lazy\LazyUuidFromString::class);
    expect($item->description)->toBeString();
    expect($item->quantity)->toBeInt();
    expect($item->unit_price)->toBeInt();
    expect($item->tax_rate)->toBeInt();
});




test('invoice item can be created with decimal irpf rate', function () {
    $item = InvoiceItem::factory()->create([
        'quantity' => 1,
        'unit_price' => 20000, // 200.00
        'tax_rate' => 10,
        'irpf_rate' => 7.5,
        'irpf_total' => 1500, // Manually set since factory doesn't calculate
    ]);

    expect(is_numeric($item->irpf_rate))->toBeTrue();
    expect($item->irpf_total)->toBe(1500); // 7.5% of 200.00 = 15.00
});

test('invoice item belongs to correct invoice relationship', function () {
    $invoice = createTestInvoice();
    $item = InvoiceItem::factory()->create(['invoice_id' => $invoice->id]);

    expect((string) $item->invoice->id)->toBe((string) $invoice->id);
    expect($invoice->items()->where('id', $item->id)->exists())->toBeTrue();
});
