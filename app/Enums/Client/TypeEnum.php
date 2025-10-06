<?php

namespace App\Enums\Client;

use App\Enums\Traits\NamesTrait;

/**
 * Enum for client types
 */
enum TypeEnum: string
{
    use NamesTrait;

    case INDIVIDUAL = 'individual';
    case COMPANY = 'company';
    case NONPROFIT = 'nonprofit';
    case ENTERPRISE = 'enterprise';

    /**
     * Returns a human-readable name for the enum value
     *
     * @return string
     */
    public function goodName(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'Individual',
            self::COMPANY => 'Company',
            self::NONPROFIT => 'Non-profit',
            self::ENTERPRISE => 'Enterprise',
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
            self::INDIVIDUAL->value => 'Individual',
            self::COMPANY->value => 'Company',
            self::NONPROFIT->value => 'Non-profit',
            self::ENTERPRISE->value => 'Enterprise',
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
