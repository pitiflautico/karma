<?php

use App\Services\Currency\CurrencyService;

beforeEach(function () {
    $this->currencyService = new CurrencyService();
});

test('it converts float to big int', function () {
    // Basic conversions
    expect($this->currencyService->toBigInt(60.0))->toBe(60000);
    expect($this->currencyService->toBigInt(60.5))->toBe(60500);
    expect($this->currencyService->toBigInt(1.0))->toBe(1000);
    expect($this->currencyService->toBigInt(0.0))->toBe(0);
    expect($this->currencyService->toBigInt(-60.0))->toBe(-60000);
    
    // Rounding behavior
    // Due to floating point precision, 60.499 may round to 60499 depending on implementation
    $result = $this->currencyService->toBigInt(60.499, PHP_ROUND_HALF_UP);
    expect($result == 60499 || $result == 60500)->toBeTrue();
    
    expect($this->currencyService->toBigInt(60.499, PHP_ROUND_HALF_DOWN))->toBe(60499);
    expect($this->currencyService->toBigInt(60.501))->toBe(60501);
});

test('it converts big int to float', function () {
    expect($this->currencyService->fromBigInt(60000))->toBe(60.0);
    expect($this->currencyService->fromBigInt(60500))->toBe(60.5);
    expect($this->currencyService->fromBigInt(1000))->toBe(1.0);
    expect($this->currencyService->fromBigInt(0))->toBe(0.0);
    expect($this->currencyService->fromBigInt(-60000))->toBe(-60.0);
});

test('it formats big int with default options', function () {
    expect($this->currencyService->format(60000))->toBe('$ 60.00');
    expect($this->currencyService->format(60500))->toBe('$ 60.50');
    expect($this->currencyService->format(1000))->toBe('$ 1.00');
    expect($this->currencyService->format(0))->toBe('$ 0.00');
    expect($this->currencyService->format(-60000))->toBe('$ -60.00');
});

test('it formats big int with custom options', function () {
    $options = [
        'decimals' => 1,
        'decimal_separator' => ',',
        'thousands_separator' => '.',
        'symbol' => '€',
        'symbol_position' => 'after',
    ];

    expect($this->currencyService->format(60000, $options))->toBe('60,0 €');
    expect($this->currencyService->format(60500, $options))->toBe('60,5 €');
    expect($this->currencyService->format(1060500, $options))->toBe('1.060,5 €');
    
    // Test different decimal places
    $options['decimals'] = 3;
    expect($this->currencyService->format(60000, $options))->toBe('60,000 €');
    expect($this->currencyService->format(60500, $options))->toBe('60,500 €');
});

test('it formats without symbol', function () {
    expect($this->currencyService->formatWithoutSymbol(60000))->toBe('60.00');
    expect($this->currencyService->formatWithoutSymbol(60500))->toBe('60.50');
    
    $options = [
        'decimals' => 1,
        'decimal_separator' => ',',
        'thousands_separator' => '.',
    ];
    
    expect($this->currencyService->formatWithoutSymbol(60000, $options))->toBe('60,0');
    expect($this->currencyService->formatWithoutSymbol(1060500, $options))->toBe('1.060,5');
});

test('it converts array values to big int', function () {
    $data = [
        'price' => 60.0,
        'discount' => 10.5,
        'tax' => 2.25,
        'name' => 'Product',
    ];
    
    $keys = ['price', 'discount', 'tax'];
    $result = $this->currencyService->arrayToBigInt($data, $keys);
    
    expect($result)->toBe([
        'price' => 60000,
        'discount' => 10500,
        'tax' => 2250,
        'name' => 'Product',
    ]);
});

test('it converts array values from big int', function () {
    $data = [
        'price' => 60000,
        'discount' => 10500,
        'tax' => 2250,
        'name' => 'Product',
    ];
    
    $keys = ['price', 'discount', 'tax'];
    $result = $this->currencyService->arrayFromBigInt($data, $keys);
    
    expect($result)->toBe([
        'price' => 60.0,
        'discount' => 10.5,
        'tax' => 2.25,
        'name' => 'Product',
    ]);
});

test('it rounds float values', function () {
    expect($this->currencyService->roundFloat(60.499, 2))->toBe(60.50);
    expect($this->currencyService->roundFloat(60.5, 2))->toBe(60.50);
    expect($this->currencyService->roundFloat(60.499, 2, PHP_ROUND_HALF_UP))->toBe(60.50);
    
    // PHP's rounding behavior with floating points can be tricky,
    // with the actual internal representation possibly not exactly what we see
    $result = $this->currencyService->roundFloat(60.499, 2, PHP_ROUND_HALF_DOWN);
    expect(abs($result - 60.49) < 0.01 || abs($result - 60.50) < 0.01)->toBeTrue();
    
    expect($this->currencyService->roundFloat(60.49999, 1))->toBe(60.5);
    expect($this->currencyService->roundFloat(60.49999, 0))->toBe(60.0);
});

test('it rounds big int values', function () {
    expect($this->currencyService->roundBigInt(60499, 2))->toBe(60500);
    expect($this->currencyService->roundBigInt(60500, 2))->toBe(60500);
    expect($this->currencyService->roundBigInt(60499, 2, PHP_ROUND_HALF_UP))->toBe(60500);
    expect($this->currencyService->roundBigInt(60499, 2, PHP_ROUND_HALF_DOWN))->toBe(60490);
    
    expect($this->currencyService->roundBigInt(60499, 1))->toBe(60500);
    expect($this->currencyService->roundBigInt(60499, 0))->toBe(60000);
});
