<?php

use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Str;
use Filament\Panel;

test('user model uses uuids', function () {
    $user = User::factory()->create();

    expect($user->id)->toBeInstanceOf(\Ramsey\Uuid\Lazy\LazyUuidFromString::class);
    expect(Str::isUuid((string) $user->id))->toBeTrue();
    // HasUuids trait should handle incrementing and keyType automatically
    expect(in_array(\Illuminate\Database\Eloquent\Concerns\HasUuids::class, class_uses_recursive($user)))->toBeTrue();
});

test('user has fillable attributes', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'organization_id' => createTestOrganization()->id,
    ]);

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect($user->organization_id)->toBeInstanceOf(\Ramsey\Uuid\Lazy\LazyUuidFromString::class);
});

test('user has hidden attributes', function () {
    $user = User::factory()->create();

    $userArray = $user->toArray();

    expect($userArray)->not->toHaveKey('password');
    expect($userArray)->not->toHaveKey('remember_token');
});

test('user casts attributes correctly', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'last_login_at' => now(),
    ]);

    expect($user->email_verified_at)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($user->last_login_at)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($user->password)->toBeString(); // Should be hashed
});

test('user generates initials correctly', function () {
    $user = User::factory()->create(['name' => 'John Doe']);

    expect($user->initials())->toBe('JD');
});

test('user generates initials for multiple names', function () {
    $user = User::factory()->create(['name' => 'John Michael Doe']);

    expect($user->initials())->toBe('JMD');
});

test('user generates initials for single name', function () {
    $user = User::factory()->create(['name' => 'John']);

    expect($user->initials())->toBe('J');
});

test('user belongs to organization', function () {
    $organization = createTestOrganization();
    $user = User::factory()->create(['organization_id' => $organization->id]);

    expect($user->organization)->toBeInstanceOf(Organization::class);
    expect((string) $user->organization->id)->toBe((string) $organization->id);
});

test('user has settings relationship', function () {
    $user = createTestUser();

    expect($user->settings())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class);
});

test('user can access filament panel with verified email and cloudstudio domain', function () {
    $user = User::factory()->create([
        'email' => 'test@cloudstudio.es',
        'email_verified_at' => now(),
    ]);

    $panel = new Panel();

    expect($user->canAccessPanel($panel))->toBeTrue();
});

test('user cannot access filament panel without verified email', function () {
    $user = User::factory()->create([
        'email' => 'test@cloudstudio.es',
        'email_verified_at' => null,
    ]);

    $panel = new Panel();

    expect($user->canAccessPanel($panel))->toBeFalse();
});

test('user cannot access filament panel without cloudstudio domain', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => now(),
    ]);

    $panel = new Panel();

    expect($user->canAccessPanel($panel))->toBeFalse();
});


test('user uses belongs to organization scope', function () {
    $user = createTestUser();

    expect(in_array('App\Models\Concerns\BelongsToOrganizationScope', class_uses_recursive($user)))->toBeTrue();
});

test('user implements required interfaces', function () {
    $user = createTestUser();

    expect($user)->toBeInstanceOf(\Illuminate\Contracts\Auth\MustVerifyEmail::class);
    expect($user)->toBeInstanceOf(\Filament\Models\Contracts\FilamentUser::class);
});

test('user uses required traits', function () {
    $user = createTestUser();

    expect(in_array(\Spatie\Permission\Traits\HasRoles::class, class_uses_recursive($user)))->toBeTrue();
    expect(in_array(\Illuminate\Notifications\Notifiable::class, class_uses_recursive($user)))->toBeTrue();
    expect(in_array(\Illuminate\Database\Eloquent\Concerns\HasUuids::class, class_uses_recursive($user)))->toBeTrue();
});

test('user factory creates valid user', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class);
    expect($user->id)->toBeInstanceOf(\Ramsey\Uuid\Lazy\LazyUuidFromString::class);
    expect($user->name)->toBeString();
    expect($user->email)->toBeString();
    expect($user->organization_id)->toBeNull(); // Default factory creates users without organization
    expect($user->email_verified_at)->not->toBeNull(); // Factory creates verified users by default
});

test('user factory can create verified user', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    expect($user->email_verified_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

test('user can be created with specific organization', function () {
    $organization = createTestOrganization();
    $user = User::factory()->create([
        'organization_id' => $organization->id,
    ]);

    expect($user->organization_id)->toBe($organization->id);
});
