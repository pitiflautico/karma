<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Create a test user with specific role
 */
function createTestUser(?string $role = null): \App\Models\User
{
    $user = \App\Models\User::factory()->create();

    if ($role) {
        $user->assignRole($role);
    }

    return $user;
}

/**
 * Create a test organization
 */
function createTestOrganization(): \App\Models\Organization
{
    return \App\Models\Organization::factory()->create();
}

/**
 * Create a test client
 */
function createTestClient(): \App\Models\Client
{
    return \App\Models\Client::factory()->create();
}

/**
 * Create a test invoice
 */
function createTestInvoice(): \App\Models\Invoice
{
    return \App\Models\Invoice::factory()->create();
}

/**
 * Create a test setting
 */
function createTestSetting(): \App\Models\Setting
{
    return \App\Models\Setting::factory()->create();
}

/**
 * Act as authenticated user
 */
function actingAs(?\App\Models\User $user = null): \Illuminate\Testing\TestResponse
{
    $user = $user ?? createTestUser();
    return test()->actingAs($user, 'web');
}

/**
 * Assert that a model has the expected attributes
 */
function assertModelHasAttributes($model, array $attributes): void
{
    foreach ($attributes as $attribute => $value) {
        expect($model->{$attribute})->toBe($value);
    }
}

/**
 * Assert that a collection contains expected items
 */
function assertCollectionContains($collection, array $expectedItems): void
{
    foreach ($expectedItems as $item) {
        expect($collection)->toContain($item);
    }
}
