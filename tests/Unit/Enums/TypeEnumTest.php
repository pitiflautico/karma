<?php

use App\Enums\Client\TypeEnum;

test('type enum has correct cases', function () {
    expect(TypeEnum::cases())->toHaveCount(4);

    expect(TypeEnum::INDIVIDUAL)->toBeInstanceOf(TypeEnum::class);
    expect(TypeEnum::COMPANY)->toBeInstanceOf(TypeEnum::class);
    expect(TypeEnum::ENTERPRISE)->toBeInstanceOf(TypeEnum::class);
});

test('type enum has correct values', function () {
    expect(TypeEnum::INDIVIDUAL->value)->toBe('individual');
    expect(TypeEnum::COMPANY->value)->toBe('company');
    expect(TypeEnum::ENTERPRISE->value)->toBe('enterprise');
});

test('type enum can be created from value', function () {
    expect(TypeEnum::from('individual'))->toBe(TypeEnum::INDIVIDUAL);
    expect(TypeEnum::from('company'))->toBe(TypeEnum::COMPANY);
    expect(TypeEnum::from('enterprise'))->toBe(TypeEnum::ENTERPRISE);
});

test('type enum throws exception for invalid value', function () {
    expect(fn() => TypeEnum::from('invalid'))->toThrow(ValueError::class);
});

test('type enum values are strings', function () {
    foreach (TypeEnum::cases() as $case) {
        expect($case->value)->toBeString();
        expect(strlen($case->value))->toBeGreaterThan(0);
    }
});
