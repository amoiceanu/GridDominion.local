<?php
declare(strict_types=1);
namespace App\Services\Game;
final class CityGrowthService
{
    public function expansion(int $population,int $monthsElapsed):?array
    {
        if($monthsElapsed<=0||$monthsElapsed%6!==0)return null; $projects=[['New residential district',3000,2],['New factory',1200,7],['Shopping center',1800,3],['Data center',600,12]]; $p=$projects[$monthsElapsed/6%count($projects)]; return ['title'=>$p[0],'population'=>$p[1],'demand'=>$p[2]];
    }
}
