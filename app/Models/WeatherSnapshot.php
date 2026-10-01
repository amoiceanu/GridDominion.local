<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WeatherSnapshot extends Model { protected $guarded=[]; protected function casts():array{return ['recorded_at'=>'datetime','solar_factor'=>'float','wind_factor'=>'float'];} }
