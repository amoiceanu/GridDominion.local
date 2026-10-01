<?php

declare(strict_types=1);

namespace App\Enums;

enum PlantType: string
{
    case Solar = 'solar';
    case Wind = 'wind';
    case Gas = 'gas';
    case Hydro = 'hydro';

    public function label(): string { return match ($this) { self::Solar => 'Solar Farm', self::Wind => 'Wind Farm', self::Gas => 'Gas Power Plant', self::Hydro => 'Hydro Plant' }; }
    public function capacity(): float { return match ($this) { self::Solar => 5, self::Wind => 8, self::Gas => 25, self::Hydro => 30 }; }
    public function cost(): int { return match ($this) { self::Solar => 750000, self::Wind => 1200000, self::Gas => 3500000, self::Hydro => 6000000 }; }
    public function buildMonths(): int { return match ($this) { self::Solar => 3, self::Wind => 5, self::Gas => 8, self::Hydro => 12 }; }
    public function operatingCost(): float { return match ($this) { self::Solar => 8, self::Wind => 10, self::Gas => 78, self::Hydro => 5 }; }
    public function co2(): int { return $this === self::Gas ? 490 : 0; }
}
