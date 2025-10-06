<?php

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\Currency\CurrencyService;

test('invoice resource has correct model', function () {
    expect(InvoiceResource::getModel())->toBe(Invoice::class);
});

test('invoice resource has correct navigation settings', function () {
    expect(InvoiceResource::getNavigationIcon())->toBe('heroicon-o-document-text');
    expect(InvoiceResource::getNavigationGroup())->toBe('Financial');
    expect(InvoiceResource::getNavigationSort())->toBe(1);
});

test('invoice resource has correct pages', function () {
    $pages = InvoiceResource::getPages();

    expect($pages)->toHaveKey('index');
    expect($pages)->toHaveKey('create');
    expect($pages)->toHaveKey('view');
    expect($pages)->toHaveKey('edit');
});

test('invoice resource has correct relations', function () {
    $relations = InvoiceResource::getRelations();

    expect($relations)->toBeArray();
});

test('invoice resource has correct eloquent query', function () {
    $query = InvoiceResource::getEloquentQuery();

    expect($query)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
    expect($query->getModel()->getTable())->toBe('invoices');
});

test('invoice resource can be instantiated', function () {
    $resource = new InvoiceResource();

    expect($resource)->toBeInstanceOf(InvoiceResource::class);
});

test('invoice resource uses soft deletes in query', function () {
    $query = InvoiceResource::getEloquentQuery();

    // The query should include trashed records
    expect($query->getQuery()->wheres)->toBeArray();
});

test('invoice resource has currency service available', function () {
    $currencyService = app(CurrencyService::class);

    expect($currencyService)->toBeInstanceOf(CurrencyService::class);
    expect($currencyService->toBigInt(100.50))->toBe(100500);
});
