<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MarketSnapshot extends Model { protected $guarded=[]; protected function casts():array{return ['recorded_at'=>'datetime','buy_price'=>'float','sell_price'=>'float'];} }
