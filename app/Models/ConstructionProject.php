<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ConstructionProject extends Model { protected $guarded=[]; protected function casts():array{return ['started_at'=>'datetime','completes_at'=>'datetime','cost'=>'float'];} public function game():BelongsTo{return $this->belongsTo(Game::class);} public function location():BelongsTo{return $this->belongsTo(BuildLocation::class,'build_location_id');} }
