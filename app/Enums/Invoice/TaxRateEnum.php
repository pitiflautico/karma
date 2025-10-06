<?php

namespace App\Enums\Invoice;

use App\Enums\Traits\NamesTrait;

/**
 * Enum for tax rates
 */
enum TaxRateEnum: string
{
    use NamesTrait;

    case ZERO = '0';
    case TEN = '10';
    case TWENTY_ONE = '21';

    /**
     * Returns a human-readable name for the enum value
     *
     * @return string
     */
    public function goodName(): string
    {
        return match ($this) {
            self::ZERO => '0%',
            self::TEN => '10%',
            self::TWENTY_ONE => '21%',
        };
    }

    /**
     * Returns an array mapping enum values to their labels
     *
     * @return array
     */
    public static function labels(): array
    {
        return [
            self::ZERO->value => '0%',
            self::TEN->value => '10%',
            self::TWENTY_ONE->value => '21%',
        ];
    }

    /**
     * Returns the label for the current enum value
     *
     * @return string
     */
    public function label(): string
    {
        return self::labels()[$this->value];
    }
}