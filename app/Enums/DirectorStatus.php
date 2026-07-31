<?php

namespace App\Enums;

enum DirectorStatus: string
{
    case ACTIVE   = 'Active';
    case ON_LEAVE = 'On Leave';
    case INACTIVE = 'Inactive';

    public function label(): string
    {
        return $this->value;
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE   => 'success',
            self::ON_LEAVE => 'warning',
            self::INACTIVE => 'gray',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
