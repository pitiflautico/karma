<?php

use App\Enums\Setting\CurrencyEnum;

test('currency enum has correct cases', function () {
    $cases = CurrencyEnum::cases();
    expect(count($cases))->toBeGreaterThan(3); // Should have EUR, USD, GBP at minimum
});

test('currency enum contains euro', function () {
    expect(CurrencyEnum::EUR)->toBeInstanceOf(CurrencyEnum::class);
    expect(CurrencyEnum::EUR->value)->toBe('EUR');
});

test('currency enum contains usd', function () {
    expect(CurrencyEnum::USD)->toBeInstanceOf(CurrencyEnum::class);
    expect(CurrencyEnum::USD->value)->toBe('USD');
});

test('currency enum can be created from value', function () {
    expect(CurrencyEnum::from('EUR'))->toBe(CurrencyEnum::EUR);
    expect(CurrencyEnum::from('USD'))->toBe(CurrencyEnum::USD);
});

test('currency enum throws exception for invalid value', function () {
    expect(fn() => CurrencyEnum::from('INVALID'))->toThrow(ValueError::class);
});

test('currency enum values are valid currency codes', function () {
    foreach (CurrencyEnum::cases() as $case) {
        expect(strlen($case->value))->toBe(3);
        expect($case->value)->toMatch('/^[A-Z]{3}$/');
    }
});

test('currency enum names are valid', function () {
    foreach (CurrencyEnum::cases() as $case) {
        expect($case->name)->toBeString();
        expect(strlen($case->name))->toBeGreaterThan(0);
    }
});
