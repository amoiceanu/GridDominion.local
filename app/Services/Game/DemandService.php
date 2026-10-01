<?php
declare(strict_types=1);
namespace App\Services\Game;
final class DemandService
{
    private const CURVE = [0=>.55, 6=>.65, 9=>.85, 13=>.78, 18=>1.0, 22=>.72, 24=>.55];
    public function calculate(int $population, int $hour, float $industrial=0, float $weather=1, float $variation=1): float
    {
        return round((($population * .00072) + $industrial) * $this->hourFactor($hour) * $weather * $variation, 3);
    }
    public function hourFactor(int $hour): float
    {
        $hour=max(0,min(23,$hour)); $keys=array_keys(self::CURVE);
        foreach ($keys as $i=>$start) { $end=$keys[$i+1]??24; if($hour>=$start&&$hour<$end){$span=$end-$start; return self::CURVE[$start]+(self::CURVE[$end]-self::CURVE[$start])*(($hour-$start)/$span);} }
        return .55;
    }
}
