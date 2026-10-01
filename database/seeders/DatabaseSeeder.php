<?php
declare(strict_types=1);
namespace Database\Seeders;
use App\Enums\EventType;
use App\Enums\PlantType;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run():void
    {
        $user=User::create(['name'=>'Demo Grid Operator','email'=>'demo@griddominion.local','password'=>Hash::make('dominion')]);
        $time=CarbonImmutable::parse('2028-05-01 14:00:00');
        $game=$user->games()->create(['name'=>'Aurora Valley Grid','game_time'=>$time,'cash'=>10000000,'energy_price'=>91,'reliability'=>99.72,'satisfaction'=>86,'reputation'=>74]);
        $game->city()->create(['name'=>'Aurora City','population'=>25000,'base_demand_mw'=>18]);
        $sites=[
            ['North Ridge','hills',18,18,['wind']],['Granite Pass','hills',32,12,['wind']],['Sunplain East','field',76,23,['solar']],['Sunplain West','field',65,31,['solar']],
            ['Silver Bend','river',49,39,['hydro']],['Lower Falls','river',58,54,['hydro']],['Industrial Gate','industrial',83,57,['gas']],['Foundry Quarter','industrial',75,67,['gas']],
            ['West Suburbs','city_edge',22,63,['battery','solar']],['East Junction','city_edge',64,73,['battery']],['Pine Hollow','field',13,43,['wind','solar']],['South Exchange','city_edge',44,81,['battery','gas']],
        ];
        $locations=[];foreach($sites as $site)$locations[]=$game->locations()->create(['name'=>$site[0],'zone'=>$site[1],'x'=>$site[2],'y'=>$site[3],'allowed_types'=>$site[4]]);
        $gas=$locations[6];$gas->update(['is_occupied'=>true]);$game->plants()->create(['build_location_id'=>$gas->id,'name'=>'Gas Plant Alpha','type'=>PlantType::Gas,'capacity_mw'=>15,'current_output_mw'=>13.8,'operating_cost_per_mwh'=>78,'co2_g_kwh'=>490,'commissioned_at'=>$time->subYears(2)]);
        $solar=$locations[2];$solar->update(['is_occupied'=>true]);$game->plants()->create(['build_location_id'=>$solar->id,'name'=>'Solar Farm Alpha','type'=>PlantType::Solar,'capacity_mw'=>5,'current_output_mw'=>4.2,'operating_cost_per_mwh'=>8,'commissioned_at'=>$time->subYear()]);
        $battery=$locations[8];$battery->update(['is_occupied'=>true]);$game->batteries()->create(['build_location_id'=>$battery->id,'name'=>'West Grid Battery','capacity_mwh'=>10,'charge_mwh'=>6.4,'charge_rate_mw'=>4,'discharge_rate_mw'=>4]);
        $weather=$game->weatherSnapshots()->create(['recorded_at'=>$time,'condition'=>'Sunny','temperature'=>27,'wind_speed'=>18,'solar_factor'=>.92,'wind_factor'=>.56]);
        for($i=24;$i>=0;$i--){$at=$time->subHours($i);$hour=$at->hour;$d=round(18*(.62+.35*max(0,sin(M_PI*($hour-5)/18)))+sin($i)*.4,2);$p=round(13.8+5*max(0,sin(M_PI*($hour-6)/14)),2);$import=max(0,$d-$p);$export=max(0,$p-$d);$game->energySnapshots()->create(['recorded_at'=>$at,'demand_mw'=>$d,'production_mw'=>$p,'imported_mw'=>$import,'exported_mw'=>$export,'battery_mwh'=>max(1,min(10,6+$export-$import)),'energy_mix'=>['solar'=>max(0,$p-13.8),'wind'=>0,'gas'=>13.8,'hydro'=>0]]);$rev=$d*91;$expense=$p*54+$import*164;$game->financialSnapshots()->create(['recorded_at'=>$at,'revenue'=>$rev,'expenses'=>$expense,'profit'=>$rev-$expense,'cash_balance'=>10000000-($i*8000)]);$game->marketSnapshots()->create(['recorded_at'=>$at,'buy_price'=>164+sin($i)*8,'sell_price'=>82+cos($i)*5]);}
        $events=[
            [EventType::Infrastructure,'WELCOME TO GRID DOMINION','The city is growing. Keep the lights on, keep energy affordable, and build a profitable energy company.'],
            [EventType::CityGrowth,'CITY EXPANSION','A new residential district is planned. Expected demand increase: +4 MW in 8 months.'],
            [EventType::Weather,'WEATHER OUTLOOK','Strong winds expected tomorrow. Wind production could rise by 35%.'],
            [EventType::EnergyMarket,'MARKET BRIEF','Natural gas prices climbed 12% amid regional demand.'],
            [EventType::Infrastructure,'GRID ALERT','Peak demand is expected tonight between 18:00–21:00.'],
        ];
        foreach($events as $i=>$event)$game->events()->create(['type'=>$event[0],'title'=>$event[1],'description'=>$event[2],'starts_at'=>$time->subHours($i*5)]);
    }
}
