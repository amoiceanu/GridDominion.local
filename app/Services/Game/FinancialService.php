<?php
declare(strict_types=1);
namespace App\Services\Game;
final class FinancialService
{
    public function calculate(float $served,float $cityPrice,float $exportRevenue,float $operatingCosts,float $importCost):array{$revenue=$served*$cityPrice+$exportRevenue;$expenses=$operatingCosts+$importCost;$profit=$revenue-$expenses;return ['revenue'=>round($revenue,2),'expenses'=>round($expenses,2),'profit'=>round($profit,2)];}
}
