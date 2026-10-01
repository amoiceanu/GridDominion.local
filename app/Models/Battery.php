<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Battery extends Model { protected $guarded=[]; protected function casts():array{return ['capacity_mwh'=>'float','charge_mwh'=>'float','charge_rate_mw'=>'float','discharge_rate_mw'=>'float','auto_mode'=>'boolean'];} public function game():BelongsTo{return $this->belongsTo(Game::class);} public function location():BelongsTo{return $this->belongsTo(BuildLocation::class,'build_location_id');} }
