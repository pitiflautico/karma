<?php

use App\Enums\Client\CountryEnum;

test('country enum has correct number of cases', function () {
    $cases = CountryEnum::cases();
    expect(count($cases))->toBeGreaterThan(20); // Should have many countries
});

test('country enum contains spain', function () {
    expect(CountryEnum::ES)->toBeInstanceOf(CountryEnum::class);
    expect(CountryEnum::ES->value)->toBe('ES');
});

test('country enum contains united states', function () {
    expect(CountryEnum::US)->toBeInstanceOf(CountryEnum::class);
    expect(CountryEnum::US->value)->toBe('US');
});

test('country enum has good name method', function () {
    expect(CountryEnum::ES->goodName())->toBe('Spain');
    expect(CountryEnum::US->goodName())->toBe('United States');
    expect(CountryEnum::GB->goodName())->toBe('United Kingdom');
});

test('country enum has label method', function () {
    expect(CountryEnum::ES->label())->toBe('Spain');
    expect(CountryEnum::US->label())->toBe('United States');
});

test('country enum labels array contains all cases', function () {
    $labels = CountryEnum::labels();

    expect($labels)->toBeArray();
    expect(count($labels))->toBe(count(CountryEnum::cases()));

    expect($labels['ES'])->toBe('Spain');
    expect($labels['US'])->toBe('United States');
});

test('country enum can be created from valid values', function () {
    expect(CountryEnum::from('ES'))->toBe(CountryEnum::ES);
    expect(CountryEnum::from('US'))->toBe(CountryEnum::US);
});

test('country enum throws exception for invalid value', function () {
    expect(fn() => CountryEnum::from('INVALID'))->toThrow(ValueError::class);
});

test('country enum values are two letter codes', function () {
    foreach (CountryEnum::cases() as $case) {
        expect(strlen($case->value))->toBe(2);
        expect($case->value)->toMatch('/^[A-Z]{2}$/');
    }
});

test('country enum all cases have good names', function () {
    foreach (CountryEnum::cases() as $case) {
        expect($case->goodName())->toBeString();
        expect(strlen($case->goodName()))->toBeGreaterThan(0);
    }
});
