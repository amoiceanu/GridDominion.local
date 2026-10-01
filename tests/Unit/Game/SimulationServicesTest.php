<?php
declare(strict_types=1);
namespace Tests\Unit\Game;
use App\Enums\PlantType;
use App\Services\Game\BatteryService;
use App\Services\Game\CityGrowthService;
use App\Services\Game\ConstructionService;
use App\Services\Game\DemandService;
use App\Services\Game\EnergyMarketService;
use App\Services\Game\FinancialService;
use App\Services\Game\ProductionService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SimulationServicesTest extends TestCase
{
    #[Test] public function demand_follows_population_and_peak_curve():void{$s=new DemandService();self::assertGreaterThan($s->calculate(25000,0),$s->calculate(25000,18));self::assertSame(18.0,$s->calculate(25000,18));}
    #[Test] public function solar_is_zero_at_night_and_peaks_near_midday():void{$s=new ProductionService();self::assertSame(0.0,$s->calculate(PlantType::Solar,5,2));self::assertGreaterThan(4,$s->calculate(PlantType::Solar,5,13));}
    #[Test] public function wind_output_respects_weather_factor():void{$s=new ProductionService();self::assertSame(2.0,$s->calculate(PlantType::Wind,8,12,1,.25));self::assertSame(8.0,$s->calculate(PlantType::Wind,8,12,1,1.1));}
    #[Test] public function gas_generation_is_reliable():void{self::assertSame(23.0,(new ProductionService())->calculate(PlantType::Gas,25,2));}
    #[Test] public function battery_charges_with_surplus():void{$r=(new BatteryService())->balance(12,20,40,10,10);self::assertSame(30.0,$r['charge']);self::assertSame(2.0,$r['net']);}
    #[Test] public function battery_discharges_into_deficit():void{$r=(new BatteryService())->balance(-8,20,40,10,10);self::assertSame(12.0,$r['charge']);self::assertSame(0.0,$r['net']);}
    #[Test] public function market_imports_when_net_is_negative():void{$r=(new EnergyMarketService())->settle(-7,168,82);self::assertSame(7.0,$r['imported']);self::assertSame(1176.0,$r['cost']);}
    #[Test] public function market_exports_when_net_is_positive():void{$r=(new EnergyMarketService())->settle(6,168,82);self::assertSame(6.0,$r['exported']);self::assertSame(492.0,$r['revenue']);}
    #[Test] public function finance_subtracts_import_and_operating_costs():void{$r=(new FinancialService())->calculate(42,91,0,1200,1176);self::assertSame(1446.0,$r['profit']);}
    #[Test] public function city_growth_occurs_on_six_month_intervals():void{$s=new CityGrowthService();self::assertNull($s->expansion(25000,5));self::assertNotNull($s->expansion(25000,6));}
    #[Test] public function construction_progress_reaches_completion():void{$s=new ConstructionService();$start=new \DateTimeImmutable('2028-01-01');$end=new \DateTimeImmutable('2028-04-01');self::assertSame(100,$s->progressPercent($start,$end,$end));}
    #[Test] public function a_power_shortage_is_exposed_as_an_import():void{$market=(new EnergyMarketService())->settle(18-26,160,80);self::assertSame(8.0,$market['imported']);self::assertSame(0.0,$market['exported']);}
}
