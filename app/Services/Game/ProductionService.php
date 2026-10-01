<?php
declare(strict_types=1);
namespace App\Services\Game;
use App\Enums\PlantType;
final class ProductionService
{
    public function calculate(PlantType $type,float $capacity,int $hour,float $solarFactor=1,float $windFactor=1):float
    {
        $factor=match($type){
            PlantType::Solar=>$this->solarFactor($hour)*$solarFactor,
            PlantType::Wind=>max(.05,min(1.15,$windFactor)),
            PlantType::Gas=>.92,
            PlantType::Hydro=>.86,
        };
        return round($capacity*min(1,$factor),3);
    }
    public function solarFactor(int $hour):float { if($hour<6||$hour>20)return 0; return max(0,sin(M_PI*($hour-6)/14)); }
}
