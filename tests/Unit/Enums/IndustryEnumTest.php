<?php

use App\Enums\Client\IndustryEnum;

test('industry enum has reasonable number of cases', function () {
    $cases = IndustryEnum::cases();
    expect(count($cases))->toBeGreaterThan(5);
});

test('industry enum contains technology', function () {
    expect(IndustryEnum::TECHNOLOGY)->toBeInstanceOf(IndustryEnum::class);
    expect(IndustryEnum::TECHNOLOGY->value)->toBe('technology');
});

test('industry enum values are strings', function () {
    foreach (IndustryEnum::cases() as $case) {
        expect($case->value)->toBeString();
        expect(strlen($case->value))->toBeGreaterThan(0);
    }
});

test('industry enum names are valid', function () {
    foreach (IndustryEnum::cases() as $case) {
        expect($case->name)->toBeString();
        expect(strlen($case->name))->toBeGreaterThan(0);
    }
});

test('industry enum can be created from valid values', function () {
    expect(IndustryEnum::from('technology'))->toBe(IndustryEnum::TECHNOLOGY);
});

test('industry enum throws exception for invalid value', function () {
    expect(fn() => IndustryEnum::from('Invalid Industry'))->toThrow(ValueError::class);
});
