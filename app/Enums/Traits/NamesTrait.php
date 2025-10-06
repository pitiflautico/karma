<?php

namespace App\Enums\Traits;

use InvalidArgumentException;

/**
 * Trait for enum utilities
 */
trait NamesTrait
{
    /**
     * Get the enum by name
     *
     * @return self
     */
    public static function getByName(string $name): self
    {
        $cases = self::cases();

        foreach ($cases as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        throw new InvalidArgumentException('Invalid name');
    }

    /**
     * Search values by given names
     *
     * @return array|null
     */
    public static function searchValuesByName(string $name): ?array
    {
        $cases = self::cases();

        $result = collect($cases)->filter(function ($item) use ($name) {
            return stristr(strtolower($item->name), strtolower($name)) !== false;
        })->map(fn($item) => $item->value)->values()->toArray();

        return count($result) > 0 ? $result : null;
    }

    /**
     * Get the enum value by name
     *
     * @param  string  $name
     * @return mixed
     */
    public static function searchValueByName(string $name): mixed
    {
        return collect(self::searchValuesByName($name))->first();
    }

    /**
     * Search by good name
     *
     * @param  string  $name
     * @return mixed
     */
    public static function searchByGoodName(string $name): mixed
    {
        return collect(self::cases())
            ->filter(fn($item) => stristr($item->goodName(), strtolower($name)) !== false)
            ->first();
    }

    /**
     * Check if is valid value
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid(mixed $value): bool
    {
        $cases = self::cases();

        foreach ($cases as $case) {
            if ($case->value === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the enum names
     *
     * @return array
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the enum values
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get random case
     *
     * @return self
     */
    public static function random(): self
    {
        $cases = self::cases();

        return $cases[array_rand($cases)];
    }

    /**
     * Format the enum name value
     *
     * @return string
     */
    public function goodName(): string
    {
        return str($this->name)->replace('_', ' ')->title();
    }

    /**
     * Get the enum label (for filament)
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->goodName();
    }

    /**
     * Get the enum label (for filament)
     *
     * @return string
     */
    public function getSlug(): ?string
    {
        return str($this->goodName())->slug();
    }

    /**
     * Enum to array
     *
     * @return string
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }

    /**
     * Return the enum as list
     *
     * @return array
     */
    public static function toList(): array
    {
        $cases = self::cases();

        $list = [];

        foreach ($cases as $case) {
            $list[$case->value] = $case->goodName();
        }

        return $list;
    }

    /**
     * Check if the given value exists in the enum
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function exists($value): bool
    {
        return in_array($value, self::values(), true);
    }

    /**
     * Get random value
     *
     * @return string|int
     */
    public static function randomValue(): string|int
    {
        $cases = self::cases();

        return $cases[array_rand($cases)]->value;
    }

    /**
     * Get the default color for badge display
     *
     * @return string
     */
    public function getColor(): string
    {
        return 'primary';
    }
}
