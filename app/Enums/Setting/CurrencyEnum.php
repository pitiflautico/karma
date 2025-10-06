<?php

namespace App\Enums\Setting;

use App\Enums\Traits\NamesTrait;

/**
 * Enum for currencies
 */
enum CurrencyEnum: string
{
    use NamesTrait;

    case EUR = 'EUR';
    case USD = 'USD';
    case GBP = 'GBP';
    case JPY = 'JPY';
    case CAD = 'CAD';
    case AUD = 'AUD';
    case CHF = 'CHF';
    case CNY = 'CNY';
    case SEK = 'SEK';
    case NZD = 'NZD';

    /**
     * Returns a human-readable name for the enum value
     *
     * @return string
     */
    public function goodName(): string
    {
        return match ($this) {
            self::EUR => 'Euro (€)',
            self::USD => 'US Dollar ($)',
            self::GBP => 'British Pound (£)',
            self::JPY => 'Japanese Yen (¥)',
            self::CAD => 'Canadian Dollar (C$)',
            self::AUD => 'Australian Dollar (A$)',
            self::CHF => 'Swiss Franc (CHF)',
            self::CNY => 'Chinese Yuan (¥)',
            self::SEK => 'Swedish Krona (kr)',
            self::NZD => 'New Zealand Dollar (NZ$)',
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
            self::EUR->value => 'Euro (€)',
            self::USD->value => 'US Dollar ($)',
            self::GBP->value => 'British Pound (£)',
            self::JPY->value => 'Japanese Yen (¥)',
            self::CAD->value => 'Canadian Dollar (C$)',
            self::AUD->value => 'Australian Dollar (A$)',
            self::CHF->value => 'Swiss Franc (CHF)',
            self::CNY->value => 'Chinese Yuan (¥)',
            self::SEK->value => 'Swedish Krona (kr)',
            self::NZD->value => 'New Zealand Dollar (NZ$)',
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