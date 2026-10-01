<?php
declare(strict_types=1);
namespace App\Services\Game;
use App\Enums\EventType;
use App\Models\Game;
final class EventService
{
    public function maybeGenerate(Game $game):void
    {
        if($game->game_time->hour===9&&random_int(1,120)===1)$game->events()->create(['type'=>EventType::EnergyMarket,'title'=>'MARKET VOLATILITY','description'=>'Regional demand is pushing import prices higher.','effects'=>['market'=>1.18],'starts_at'=>$game->game_time,'ends_at'=>$game->game_time->copy()->addDays(3)]);
    }
}
