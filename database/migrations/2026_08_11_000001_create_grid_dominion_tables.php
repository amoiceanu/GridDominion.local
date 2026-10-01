<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table): void {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->string('name');
            $table->dateTime('game_time')->index(); $table->unsignedTinyInteger('speed')->default(1); $table->boolean('is_paused')->default(false);
            $table->decimal('cash', 16, 2)->default(10000000); $table->decimal('energy_price', 8, 2)->default(91); $table->decimal('gas_multiplier', 6, 3)->default(1);
            $table->decimal('reliability', 6, 3)->default(100); $table->decimal('satisfaction', 6, 2)->default(86); $table->decimal('reputation', 6, 2)->default(75); $table->unsignedInteger('blackout_hours')->default(0); $table->timestamps();
        });
        Schema::create('cities', function (Blueprint $table): void {
            $table->id(); $table->foreignId('game_id')->unique()->constrained()->cascadeOnDelete(); $table->string('name'); $table->unsignedBigInteger('population')->default(25000); $table->decimal('base_demand_mw', 10, 3)->default(18); $table->decimal('industrial_demand_mw', 10, 3)->default(0); $table->unsignedInteger('growth_points')->default(0); $table->timestamps();
        });
        Schema::create('build_locations', function (Blueprint $table): void {
            $table->id(); $table->foreignId('game_id')->constrained()->cascadeOnDelete(); $table->string('name'); $table->string('zone'); $table->decimal('x', 5, 2); $table->decimal('y', 5, 2); $table->json('allowed_types'); $table->boolean('is_occupied')->default(false); $table->timestamps(); $table->index(['game_id', 'zone']);
        });
        Schema::create('power_plants', function (Blueprint $table): void {
            $table->id(); $table->foreignId('game_id')->constrained()->cascadeOnDelete(); $table->foreignId('build_location_id')->nullable()->constrained()->nullOnDelete(); $table->string('name'); $table->string('type')->index(); $table->decimal('capacity_mw', 10, 2); $table->decimal('current_output_mw', 10, 3)->default(0); $table->decimal('operating_cost_per_mwh', 10, 2); $table->unsignedInteger('co2_g_kwh')->default(0); $table->boolean('is_active')->default(true); $table->dateTime('commissioned_at')->nullable(); $table->timestamps();
        });
        Schema::create('batteries', function (Blueprint $table): void {
            $table->id(); $table->foreignId('game_id')->constrained()->cascadeOnDelete(); $table->foreignId('build_location_id')->nullable()->constrained()->nullOnDelete(); $table->string('name'); $table->decimal('capacity_mwh', 10, 2); $table->decimal('charge_mwh', 10, 3); $table->decimal('charge_rate_mw', 10, 2); $table->decimal('discharge_rate_mw', 10, 2); $table->boolean('auto_mode')->default(true); $table->timestamps();
        });
        Schema::create('construction_projects', function (Blueprint $table): void {
            $table->id(); $table->foreignId('game_id')->constrained()->cascadeOnDelete(); $table->foreignId('build_location_id')->constrained()->cascadeOnDelete(); $table->string('project_type'); $table->dateTime('started_at'); $table->dateTime('completes_at')->index(); $table->decimal('cost', 14, 2); $table->unsignedTinyInteger('progress')->default(0); $table->timestamps();
        });
        Schema::create('energy_snapshots', function (Blueprint $table): void {
            $table->id(); $table->foreignId('game_id')->constrained()->cascadeOnDelete(); $table->dateTime('recorded_at')->index(); $table->decimal('demand_mw', 10, 3); $table->decimal('production_mw', 10, 3); $table->decimal('imported_mw', 10, 3)->default(0); $table->decimal('exported_mw', 10, 3)->default(0); $table->decimal('battery_mwh', 10, 3)->default(0); $table->json('energy_mix')->nullable(); $table->timestamps(); $table->index(['game_id', 'recorded_at']);
        });
        Schema::create('financial_snapshots', function (Blueprint $table): void {
            $table->id(); $table->foreignId('game_id')->constrained()->cascadeOnDelete(); $table->dateTime('recorded_at')->index(); $table->decimal('revenue', 14, 2); $table->decimal('expenses', 14, 2); $table->decimal('profit', 14, 2); $table->decimal('cash_balance', 16, 2); $table->timestamps();
        });
        Schema::create('game_events', function (Blueprint $table): void {
            $table->id(); $table->foreignId('game_id')->constrained()->cascadeOnDelete(); $table->string('type')->index(); $table->string('title'); $table->text('description'); $table->json('effects')->nullable(); $table->dateTime('starts_at'); $table->dateTime('ends_at')->nullable(); $table->boolean('is_read')->default(false); $table->timestamps(); $table->index(['game_id', 'starts_at']);
        });
        Schema::create('market_snapshots', function (Blueprint $table): void {
            $table->id(); $table->foreignId('game_id')->constrained()->cascadeOnDelete(); $table->dateTime('recorded_at'); $table->decimal('buy_price', 8, 2); $table->decimal('sell_price', 8, 2); $table->timestamps();
        });
        Schema::create('weather_snapshots', function (Blueprint $table): void {
            $table->id(); $table->foreignId('game_id')->constrained()->cascadeOnDelete(); $table->dateTime('recorded_at'); $table->string('condition'); $table->integer('temperature'); $table->unsignedTinyInteger('wind_speed'); $table->decimal('solar_factor', 5, 3); $table->decimal('wind_factor', 5, 3); $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['weather_snapshots','market_snapshots','game_events','financial_snapshots','energy_snapshots','construction_projects','batteries','power_plants','build_locations','cities','games'] as $table) Schema::dropIfExists($table);
    }
};
