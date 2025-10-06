<?php

use App\Filament\Resources\SettingResource;
use App\Models\Setting;

test('setting resource has correct model', function () {
    expect(SettingResource::getModel())->toBe(Setting::class);
});

test('setting resource has correct navigation settings', function () {
    expect(SettingResource::getNavigationIcon())->toBe('heroicon-o-cog-6-tooth');
    expect(SettingResource::getNavigationGroup())->toBe('System');
    expect(SettingResource::getNavigationSort())->toBe(99);
});

test('setting resource has correct pages', function () {
    $pages = SettingResource::getPages();

    expect($pages)->toHaveKey('index');
    expect($pages)->toHaveKey('create');
    expect($pages)->toHaveKey('view');
    expect($pages)->toHaveKey('edit');
});

test('setting resource has correct relations', function () {
    $relations = SettingResource::getRelations();

    expect($relations)->toBeArray();
});

test('setting resource has correct eloquent query', function () {
    $query = SettingResource::getEloquentQuery();

    expect($query)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
    expect($query->getModel()->getTable())->toBe('settings');
});

test('setting resource can be instantiated', function () {
    $resource = new SettingResource();

    expect($resource)->toBeInstanceOf(SettingResource::class);
});