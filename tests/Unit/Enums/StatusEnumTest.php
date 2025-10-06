<?php

use App\Enums\Invoice\StatusEnum;

test('status enum has correct cases', function () {
    expect(StatusEnum::cases())->toHaveCount(4);

    expect(StatusEnum::DRAFT)->toBeInstanceOf(StatusEnum::class);
    expect(StatusEnum::SENT)->toBeInstanceOf(StatusEnum::class);
    expect(StatusEnum::PAID)->toBeInstanceOf(StatusEnum::class);
    expect(StatusEnum::OVERDUE)->toBeInstanceOf(StatusEnum::class);
});

test('status enum has correct values', function () {
    expect(StatusEnum::DRAFT->value)->toBe('draft');
    expect(StatusEnum::SENT->value)->toBe('sent');
    expect(StatusEnum::PAID->value)->toBe('paid');
    expect(StatusEnum::OVERDUE->value)->toBe('overdue');
});

test('status enum can be created from value', function () {
    expect(StatusEnum::from('draft'))->toBe(StatusEnum::DRAFT);
    expect(StatusEnum::from('sent'))->toBe(StatusEnum::SENT);
    expect(StatusEnum::from('paid'))->toBe(StatusEnum::PAID);
    expect(StatusEnum::from('overdue'))->toBe(StatusEnum::OVERDUE);
});

test('status enum throws exception for invalid value', function () {
    expect(fn() => StatusEnum::from('invalid'))->toThrow(ValueError::class);
});

test('status enum can be compared', function () {
    $status = StatusEnum::DRAFT;

    expect($status)->toBe(StatusEnum::DRAFT);
    expect($status === StatusEnum::DRAFT)->toBeTrue();
    expect($status === StatusEnum::SENT)->toBeFalse();
});

test('status enum can be used in match expressions', function () {
    $status = StatusEnum::PAID;

    $result = match ($status) {
        StatusEnum::DRAFT => 'draft',
        StatusEnum::SENT => 'sent',
        StatusEnum::PAID => 'paid',
        StatusEnum::OVERDUE => 'overdue',
    };

    expect($result)->toBe('paid');
});

test('status enum values are strings', function () {
    foreach (StatusEnum::cases() as $case) {
        expect($case->value)->toBeString();
    }
});

test('status enum names are valid', function () {
    foreach (StatusEnum::cases() as $case) {
        expect($case->name)->toBeString();
        expect(strlen($case->name))->toBeGreaterThan(0);
    }
});
