<?php
declare(strict_types=1);
namespace App\Models;
use App\Enums\EventType;
use Illuminate\Database\Eloquent\Model;
class GameEvent extends Model { protected $guarded=[]; protected function casts():array{return ['type'=>EventType::class,'effects'=>'array','starts_at'=>'datetime','ends_at'=>'datetime','is_read'=>'boolean'];} }
