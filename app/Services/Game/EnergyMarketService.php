<?php
declare(strict_types=1);
namespace App\Services\Game;
final class EnergyMarketService
{
    public function settle(float $net,float $buyPrice,float $sellPrice):array{return $net<0?['imported'=>abs($net),'exported'=>0.0,'cost'=>abs($net)*$buyPrice,'revenue'=>0.0]:['imported'=>0.0,'exported'=>$net,'cost'=>0.0,'revenue'=>$net*$sellPrice];}
}
