<?php
declare(strict_types=1);
namespace App\Services\Game;
use App\Models\Game;
use App\Models\WeatherSnapshot;
use Illuminate\Support\Facades\DB;

final class GameEngine
{
    public function __construct(private DemandService $demand,private ProductionService $production,private BatteryService $battery,private EnergyMarketService $market,private FinancialService $finance,private WeatherService $weather,private ConstructionService $construction,private CityGrowthService $growth,private EventService $events){}

    public function tick(Game $game):Game
    {
        return DB::transaction(function()use($game):Game{
            $game->refresh(); if($game->is_paused)return $game;
            $game->game_time=$game->game_time->copy()->addHour(); $game->save();
            $game->load(['city','plants','batteries','projects.location']);
            $weather=$this->currentWeather($game); $hour=$game->game_time->hour;
            $demand=$this->demand->calculate($game->city->population,$hour,$game->city->industrial_demand_mw,$this->weatherDemandFactor($weather->condition),mt_rand(96,104)/100);
            $mix=['solar'=>0.0,'wind'=>0.0,'gas'=>0.0,'hydro'=>0.0]; $operating=0.0;
            foreach($game->plants as $plant){$output=$plant->is_active?$this->production->calculate($plant->type,$plant->capacity_mw,$hour,$weather->solar_factor,$weather->wind_factor):0;$plant->update(['current_output_mw'=>$output]);$mix[$plant->type->value]+=$output;$operating+=$output*$plant->operating_cost_per_mwh*($plant->type->value==='gas'?$game->gas_multiplier:1);}
            $production=array_sum($mix); $net=$production-$demand; $batteryFlow=0.0;
            foreach($game->batteries as $unit){if(!$unit->auto_mode)continue;$b=$this->battery->balance($net,$unit->charge_mwh,$unit->capacity_mwh,$unit->charge_rate_mw,$unit->discharge_rate_mw);$unit->update(['charge_mwh'=>$b['charge']]);$net=$b['net'];$batteryFlow+=$b['flow'];}
            $buy=round($game->energy_price*1.72+mt_rand(-8,14),2); $sell=round($game->energy_price*.9+mt_rand(-4,5),2); $trade=$this->market->settle($net,$buy,$sell); $financial=$this->finance->calculate($demand,$game->energy_price,$trade['revenue'],$operating,$trade['cost']);
            $game->cash+=$financial['profit']; if($trade['imported']>0){$game->blackout_hours++;$game->reliability=max(0,$game->reliability-.018);$game->satisfaction=max(0,$game->satisfaction-.025);$game->reputation=max(0,$game->reputation-.012);}else{$game->reliability=min(100,$game->reliability+.003);}
            $game->energy_price=max(58,min(142,$game->energy_price+mt_rand(-30,30)/100)); $game->save();
            $game->energySnapshots()->create(['recorded_at'=>$game->game_time,'demand_mw'=>$demand,'production_mw'=>$production,'imported_mw'=>$trade['imported'],'exported_mw'=>$trade['exported'],'battery_mwh'=>$game->batteries->sum('charge_mwh'),'energy_mix'=>$mix]);
            $game->financialSnapshots()->create(['recorded_at'=>$game->game_time,'revenue'=>$financial['revenue'],'expenses'=>$financial['expenses'],'profit'=>$financial['profit'],'cash_balance'=>$game->cash]);
            $game->marketSnapshots()->create(['recorded_at'=>$game->game_time,'buy_price'=>$buy,'sell_price'=>$sell]);
            foreach($game->projects as $project){$progress=$this->construction->progressPercent($project->started_at,$project->completes_at,$game->game_time);$project->update(['progress'=>$progress]);if($progress>=100){$this->construction->complete($project);$game->events()->create(['type'=>'INFRASTRUCTURE','title'=>'CONSTRUCTION COMPLETE','description'=>$project->location->name.' is now supplying the grid.','starts_at'=>$game->game_time]);}}
            $this->applyCityGrowth($game); $this->events->maybeGenerate($game); return $game->fresh();
        });
    }

    private function currentWeather(Game $game):WeatherSnapshot
    {
        $latest=$game->weatherSnapshots()->latest('recorded_at')->first();
        if(!$latest||$latest->recorded_at->toDateString()!==$game->game_time->toDateString()){$w=$this->weather->generate();return $game->weatherSnapshots()->create(['recorded_at'=>$game->game_time,'condition'=>$w['condition'],'temperature'=>$w['temp'],'wind_speed'=>$w['wind'],'solar_factor'=>$w['solar'],'wind_factor'=>$w['windFactor']]);} return $latest;
    }
    private function weatherDemandFactor(string $condition):float{return match($condition){'Sunny'=>1.06,'Storm'=>1.11,'Calm'=>1.02,default=>1.0};}
    private function applyCityGrowth(Game $game):void
    {
        if($game->game_time->day!==1||$game->game_time->hour!==0)return;
        $months=($game->game_time->year-2028)*12+$game->game_time->month-5;if($months<6||$months<=$game->city->growth_points)return;
        $expansion=$this->growth->expansion($game->city->population,$months);if(!$expansion)return;
        $game->city->increment('population',$expansion['population']);$game->city->increment('industrial_demand_mw',$expansion['demand']);$game->city->update(['growth_points'=>$months]);
        $game->events()->create(['type'=>'CITY_GROWTH','title'=>strtoupper($expansion['title']).' ONLINE','description'=>number_format($expansion['population']).' new residents and +'.$expansion['demand'].' MW of permanent demand joined the grid.','starts_at'=>$game->game_time]);
    }
}
