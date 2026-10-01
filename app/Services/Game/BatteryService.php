<?php
declare(strict_types=1);
namespace App\Services\Game;
final class BatteryService
{
    public function balance(float $net,float $charge,float $capacity,float $chargeRate,float $dischargeRate):array
    {
        if($net>0){$stored=min($net,$chargeRate,max(0,$capacity-$charge));return ['charge'=>round($charge+$stored,3),'net'=>round($net-$stored,3),'flow'=>round($stored,3)];}
        if($net<0){$released=min(abs($net),$dischargeRate,$charge);return ['charge'=>round($charge-$released,3),'net'=>round($net+$released,3),'flow'=>round(-$released,3)];}
        return ['charge'=>$charge,'net'=>0.0,'flow'=>0.0];
    }
}
