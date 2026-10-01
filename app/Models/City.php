<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class City extends Model { protected $guarded=[]; protected function casts(): array { return ['base_demand_mw'=>'float','industrial_demand_mw'=>'float']; } public function game(): BelongsTo{return $this->belongsTo(Game::class);} }
