<?php
declare(strict_types=1);
namespace App\Models;
use App\Enums\PlantType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PowerPlant extends Model { protected $guarded=[]; protected function casts(): array{return ['type'=>PlantType::class,'capacity_mw'=>'float','current_output_mw'=>'float','operating_cost_per_mwh'=>'float','is_active'=>'boolean','commissioned_at'=>'datetime'];} public function game(): BelongsTo{return $this->belongsTo(Game::class);} public function location(): BelongsTo{return $this->belongsTo(BuildLocation::class,'build_location_id');} }
