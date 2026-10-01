<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FinancialSnapshot extends Model { protected $guarded=[]; protected function casts():array{return ['recorded_at'=>'datetime','revenue'=>'float','expenses'=>'float','profit'=>'float','cash_balance'=>'float'];} }
