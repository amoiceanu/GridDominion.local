<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class BuildLocation extends Model { protected $guarded=[]; protected function casts():array{return ['allowed_types'=>'array','is_occupied'=>'boolean','x'=>'float','y'=>'float'];} public function game():BelongsTo{return $this->belongsTo(Game::class);} public function plant():HasOne{return $this->hasOne(PowerPlant::class);} public function battery():HasOne{return $this->hasOne(Battery::class);} public function project():HasOne{return $this->hasOne(ConstructionProject::class);} }
