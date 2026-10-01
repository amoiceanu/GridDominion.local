<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EnergySnapshot extends Model { protected $guarded=[]; protected function casts():array{return ['recorded_at'=>'datetime','demand_mw'=>'float','production_mw'=>'float','imported_mw'=>'float','exported_mw'=>'float','battery_mwh'=>'float','energy_mix'=>'array'];} }
