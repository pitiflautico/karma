<?php

use App\Filament\Resources\ClientResource;
use App\Models\Client;

test('client resource has correct model', function () {
    expect(ClientResource::getModel())->toBe(Client::class);
});

test('client resource has correct navigation settings', function () {
    expect(ClientResource::getNavigationIcon())->toBe('heroicon-o-building-office');
    expect(ClientResource::getNavigationGroup())->toBe('Business');
    expect(ClientResource::getNavigationSort())->toBe(1);
});

test('client resource has correct pages', function () {
    $pages = ClientResource::getPages();

    expect($pages)->toHaveKey('index');
    expect($pages)->toHaveKey('create');
    expect($pages)->toHaveKey('view');
    expect($pages)->toHaveKey('edit');
});

test('client resource has correct relations', function () {
    $relations = ClientResource::getRelations();

    expect($relations)->toBeArray();
    expect(count($relations))->toBeGreaterThan(0); // Should have invoice relation manager
});

test('client resource has correct eloquent query', function () {
    $query = ClientResource::getEloquentQuery();

    expect($query)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
    expect($query->getModel()->getTable())->toBe('clients');
});

test('client resource can be instantiated', function () {
    $resource = new ClientResource();

    expect($resource)->toBeInstanceOf(ClientResource::class);
});
