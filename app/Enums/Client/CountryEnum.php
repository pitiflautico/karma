<?php

namespace App\Enums\Client;

use App\Enums\Traits\NamesTrait;

/**
 * Enum for countries
 */
enum CountryEnum: string
{
    use NamesTrait;

    case US = 'US';
    case CA = 'CA';
    case GB = 'GB';
    case FR = 'FR';
    case DE = 'DE';
    case IT = 'IT';
    case ES = 'ES';
    case PT = 'PT';
    case NL = 'NL';
    case BE = 'BE';
    case SE = 'SE';
    case NO = 'NO';
    case DK = 'DK';
    case FI = 'FI';
    case AU = 'AU';
    case NZ = 'NZ';
    case JP = 'JP';
    case CN = 'CN';
    case IN = 'IN';
    case BR = 'BR';
    case MX = 'MX';
    case AR = 'AR';
    case CL = 'CL';
    case CO = 'CO';
    case ZA = 'ZA';
    case NG = 'NG';
    case EG = 'EG';
    case RU = 'RU';
    case KR = 'KR';

    /**
     * Returns a human-readable name for the enum value
     *
     * @return string
     */
    public function goodName(): string
    {
        return match ($this) {
            self::US => 'United States',
            self::CA => 'Canada',
            self::GB => 'United Kingdom',
            self::FR => 'France',
            self::DE => 'Germany',
            self::IT => 'Italy',
            self::ES => 'Spain',
            self::PT => 'Portugal',
            self::NL => 'Netherlands',
            self::BE => 'Belgium',
            self::SE => 'Sweden',
            self::NO => 'Norway',
            self::DK => 'Denmark',
            self::FI => 'Finland',
            self::AU => 'Australia',
            self::NZ => 'New Zealand',
            self::JP => 'Japan',
            self::CN => 'China',
            self::IN => 'India',
            self::BR => 'Brazil',
            self::MX => 'Mexico',
            self::AR => 'Argentina',
            self::CL => 'Chile',
            self::CO => 'Colombia',
            self::ZA => 'South Africa',
            self::NG => 'Nigeria',
            self::EG => 'Egypt',
            self::RU => 'Russia',
            self::KR => 'South Korea',
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
            self::US->value => 'United States',
            self::CA->value => 'Canada',
            self::GB->value => 'United Kingdom',
            self::FR->value => 'France',
            self::DE->value => 'Germany',
            self::IT->value => 'Italy',
            self::ES->value => 'Spain',
            self::PT->value => 'Portugal',
            self::NL->value => 'Netherlands',
            self::BE->value => 'Belgium',
            self::SE->value => 'Sweden',
            self::NO->value => 'Norway',
            self::DK->value => 'Denmark',
            self::FI->value => 'Finland',
            self::AU->value => 'Australia',
            self::NZ->value => 'New Zealand',
            self::JP->value => 'Japan',
            self::CN->value => 'China',
            self::IN->value => 'India',
            self::BR->value => 'Brazil',
            self::MX->value => 'Mexico',
            self::AR->value => 'Argentina',
            self::CL->value => 'Chile',
            self::CO->value => 'Colombia',
            self::ZA->value => 'South Africa',
            self::NG->value => 'Nigeria',
            self::EG->value => 'Egypt',
            self::RU->value => 'Russia',
            self::KR->value => 'South Korea',
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