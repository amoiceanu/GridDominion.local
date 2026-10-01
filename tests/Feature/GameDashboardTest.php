<?php
declare(strict_types=1);
namespace Tests\Feature;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
final class GameDashboardTest extends TestCase
{
    use RefreshDatabase;
    public function test_dashboard_renders_a_playable_seeded_game():void{$this->seed(DatabaseSeeder::class);$this->get('/')->assertOk()->assertSee('GRID')->assertSee('AURORA VALLEY')->assertSee('CITY GRID');}
    public function test_tick_returns_live_grid_state():void{$this->seed(DatabaseSeeder::class);$game=\App\Models\Game::firstOrFail();$this->postJson("/games/{$game->id}/tick")->assertOk()->assertJsonStructure(['game','city','grid','weather','plants','locations','history']);}
    public function test_valid_build_starts_a_construction_project():void{$this->seed(DatabaseSeeder::class);$game=\App\Models\Game::firstOrFail();$site=$game->locations()->where('is_occupied',false)->where('zone','hills')->firstOrFail();$this->postJson("/games/{$game->id}/build",['location_id'=>$site->id,'type'=>'wind'])->assertOk();$this->assertDatabaseHas('construction_projects',['game_id'=>$game->id,'project_type'=>'wind']);}
}
