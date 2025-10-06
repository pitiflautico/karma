<?php

namespace App\Enums\Client;

use App\Enums\Traits\NamesTrait;

/**
 * Enum for client industries
 */
enum IndustryEnum: string
{
    use NamesTrait;

    case TECHNOLOGY = 'technology';
    case HEALTHCARE = 'healthcare';
    case FINANCE = 'finance';
    case EDUCATION = 'education';
    case RETAIL = 'retail';
    case MANUFACTURING = 'manufacturing';
    case CONSULTING = 'consulting';
    case MEDIA = 'media';
    case CONSTRUCTION = 'construction';
    case HOSPITALITY = 'hospitality';
    case NONPROFIT = 'nonprofit';
    case OTHER = 'other';

    /**
     * Returns a human-readable name for the enum value
     *
     * @return string
     */
    public function goodName(): string
    {
        return match ($this) {
            self::TECHNOLOGY => 'Technology',
            self::HEALTHCARE => 'Healthcare',
            self::FINANCE => 'Finance',
            self::EDUCATION => 'Education',
            self::RETAIL => 'Retail',
            self::MANUFACTURING => 'Manufacturing',
            self::CONSULTING => 'Consulting',
            self::MEDIA => 'Media',
            self::CONSTRUCTION => 'Construction',
            self::HOSPITALITY => 'Hospitality',
            self::NONPROFIT => 'Non-profit',
            self::OTHER => 'Other',
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
            self::TECHNOLOGY->value => 'Technology',
            self::HEALTHCARE->value => 'Healthcare',
            self::FINANCE->value => 'Finance',
            self::EDUCATION->value => 'Education',
            self::RETAIL->value => 'Retail',
            self::MANUFACTURING->value => 'Manufacturing',
            self::CONSULTING->value => 'Consulting',
            self::MEDIA->value => 'Media',
            self::CONSTRUCTION->value => 'Construction',
            self::HOSPITALITY->value => 'Hospitality',
            self::NONPROFIT->value => 'Non-profit',
            self::OTHER->value => 'Other',
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