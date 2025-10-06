<?php

namespace App\Enums\Invoice;

use App\Enums\Traits\NamesTrait;

/**
 * Enum for invoice statuses
 */
enum StatusEnum: string
{
    use NamesTrait;

    case DRAFT = 'draft';
    case SENT = 'sent';
    case PAID = 'paid';
    case OVERDUE = 'overdue';

    /**
     * Returns a human-readable name for the enum value
     *
     * @return string
     */
    public function goodName(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::PAID => 'Paid',
            self::OVERDUE => 'Overdue',
        };
    }

    /**
     * Returns an array of all enum values
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Returns an array mapping enum values to their labels
     *
     * @return array
     */
    public static function labels(): array
    {
        return [
            self::DRAFT->value => 'Draft',
            self::SENT->value => 'Sent',
            self::PAID->value => 'Paid',
            self::OVERDUE->value => 'Overdue',
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

    /**
     * Returns the color for the current enum value
     *
     * @return string
     */
    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SENT => 'info',
            self::PAID => 'success',
            self::OVERDUE => 'danger',
        };
    }
}
