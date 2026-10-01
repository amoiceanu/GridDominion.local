<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Game extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['game_time' => 'datetime', 'is_paused' => 'boolean', 'cash' => 'float', 'energy_price' => 'float', 'reliability' => 'float', 'satisfaction' => 'float', 'reputation' => 'float']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function city(): HasOne { return $this->hasOne(City::class); }
    public function plants(): HasMany { return $this->hasMany(PowerPlant::class); }
    public function batteries(): HasMany { return $this->hasMany(Battery::class); }
    public function locations(): HasMany { return $this->hasMany(BuildLocation::class); }
    public function projects(): HasMany { return $this->hasMany(ConstructionProject::class); }
    public function energySnapshots(): HasMany { return $this->hasMany(EnergySnapshot::class); }
    public function financialSnapshots(): HasMany { return $this->hasMany(FinancialSnapshot::class); }
    public function events(): HasMany { return $this->hasMany(GameEvent::class); }
    public function weatherSnapshots(): HasMany { return $this->hasMany(WeatherSnapshot::class); }
    public function marketSnapshots(): HasMany { return $this->hasMany(MarketSnapshot::class); }
}
