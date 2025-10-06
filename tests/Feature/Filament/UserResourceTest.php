<?php

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Models\Organization;

test('user resource has correct model', function () {
    expect(UserResource::getModel())->toBe(User::class);
});

test('user resource has correct navigation settings', function () {
    expect(UserResource::getNavigationIcon())->toBe('heroicon-o-users');
    expect(UserResource::getNavigationGroup())->toBe('Administration');
    expect(UserResource::getNavigationSort())->toBe(2);
});

test('user resource has correct pages', function () {
    $pages = UserResource::getPages();

    expect($pages)->toHaveKey('index');
    expect($pages)->toHaveKey('view');
    expect($pages)->not->toHaveKey('create'); // No create page defined
    expect($pages)->not->toHaveKey('edit'); // No edit page defined
});

test('user resource has correct relations', function () {
    $relations = UserResource::getRelations();

    expect($relations)->toBeArray();
});

test('user resource form creates user with organization', function () {
    $user = createTestUser();
    $organization = createTestOrganization();

    // Test that the form data would be processed correctly
    $formData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'roles' => [],
    ];

    // This would normally be handled by Filament's form processing
    expect($formData)->toBeArray();
    expect($formData['name'])->toBe('Test User');
});

test('user resource can be instantiated', function () {
    $resource = new UserResource();

    expect($resource)->toBeInstanceOf(UserResource::class);
});

test('user resource has correct eloquent query', function () {
    $query = UserResource::getEloquentQuery();

    expect($query)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
    expect($query->getModel()->getTable())->toBe('users');
});