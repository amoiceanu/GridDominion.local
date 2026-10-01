<?php
declare(strict_types=1);
namespace App\Http\Controllers\Game;
use App\Enums\PlantType;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuildFacilityRequest;
use App\Models\Game;
use App\Services\Game\GameEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GameController extends Controller
{
    public function index():View{$game=Game::query()->firstOrFail();return view('game.index',['game'=>$game,'state'=>$this->serialize($game)]);}
    public function state(Game $game):JsonResponse{return response()->json($this->serialize($game));}
    public function tick(Game $game,GameEngine $engine):JsonResponse{if(!$game->is_paused){for($i=0;$i<$game->speed;$i++)$game=$engine->tick($game);}return response()->json($this->serialize($game));}
    public function speed(Game $game):JsonResponse{$data=request()->validate(['speed'=>['required','integer','in:0,1,2,4']]);$game->update(['is_paused'=>$data['speed']===0,'speed'=>$data['speed']===0?$game->speed:$data['speed']]);return response()->json(['ok'=>true]);}
    public function build(BuildFacilityRequest $request,Game $game):JsonResponse
    {
        $data=$request->validated(); $location=$game->locations()->findOrFail($data['location_id']); abort_if($location->is_occupied,422,'This site is already occupied.'); abort_unless(in_array($data['type'],$location->allowed_types,true),422,'That technology is not permitted at this location.');
        if($data['type']==='battery'){$cost=1500000;$months=4;}else{$type=PlantType::from($data['type']);$cost=$type->cost();$months=$type->buildMonths();} abort_if($game->cash<$cost,422,'Insufficient funds.');
        DB::transaction(function()use($game,$location,$data,$cost,$months):void{$game->decrement('cash',$cost);$location->update(['is_occupied'=>true]);$game->projects()->create(['build_location_id'=>$location->id,'project_type'=>$data['type'],'started_at'=>$game->game_time,'completes_at'=>$game->game_time->copy()->addMonths($months),'cost'=>$cost]);$game->events()->create(['type'=>'INFRASTRUCTURE','title'=>'CONSTRUCTION STARTED','description'=>strtoupper($data['type']).' project mobilized at '.$location->name.'.','starts_at'=>$game->game_time]);}); return response()->json($this->serialize($game->fresh()));
    }
    private function serialize(Game $game):array
    {
        $game->load(['city','plants.location','batteries.location','locations.project','events'=>fn($q)=>$q->latest('starts_at')->limit(8)]);$latest=$game->energySnapshots()->latest('recorded_at')->first();$weather=$game->weatherSnapshots()->latest('recorded_at')->first();$market=$game->marketSnapshots()->latest('recorded_at')->first();$financial=$game->financialSnapshots()->latest('recorded_at')->first();$history=$game->energySnapshots()->latest('recorded_at')->limit(24)->get()->reverse()->values();$finHistory=$game->financialSnapshots()->latest('recorded_at')->limit(30)->get()->reverse()->values();$mix=$latest?->energy_mix??['solar'=>0,'wind'=>0,'gas'=>0,'hydro'=>0];$total=max(.001,array_sum($mix));$renewables=$mix['solar']+$mix['wind']+$mix['hydro'];$co2=$total?round(($mix['gas']*490)/$total):0;
        return ['game'=>['id'=>$game->id,'cash'=>$game->cash,'time'=>$game->game_time->format('M Y'),'date_full'=>$game->game_time->format('D, d M Y · H:i'),'speed'=>$game->is_paused?0:$game->speed,'price'=>$game->energy_price,'reliability'=>$game->reliability,'satisfaction'=>$game->satisfaction,'reputation'=>$game->reputation,'blackouts'=>$game->blackout_hours],'city'=>['name'=>$game->city->name,'population'=>$game->city->population],'grid'=>['demand'=>$latest?->demand_mw??18,'production'=>$latest?->production_mw??20,'reserve'=>($latest?->production_mw??20)-($latest?->demand_mw??18),'imported'=>$latest?->imported_mw??0,'exported'=>$latest?->exported_mw??0],'battery'=>['charge'=>$game->batteries->sum('charge_mwh'),'capacity'=>$game->batteries->sum('capacity_mwh')],'weather'=>$weather?['condition'=>$weather->condition,'temperature'=>$weather->temperature,'wind'=>$weather->wind_speed,'solar'=>round($weather->solar_factor*100)]:['condition'=>'Sunny','temperature'=>27,'wind'=>18,'solar'=>92],'market'=>['buy'=>$market?->buy_price??168,'sell'=>$market?->sell_price??82],'finance'=>['revenue'=>$financial?->revenue??0,'expenses'=>$financial?->expenses??0,'profit'=>$financial?->profit??0],'mix'=>array_map(fn($v)=>round($v/$total*100),$mix)+['renewable'=>round($renewables/$total*100),'co2'=>$co2],'plants'=>$game->plants->map(fn($p)=>['id'=>$p->id,'name'=>$p->name,'type'=>$p->type->value,'capacity'=>$p->capacity_mw,'output'=>$p->current_output_mw,'x'=>$p->location?->x,'y'=>$p->location?->y]),'batteries'=>$game->batteries->map(fn($b)=>['id'=>$b->id,'name'=>$b->name,'charge'=>$b->charge_mwh,'capacity'=>$b->capacity_mwh,'x'=>$b->location?->x,'y'=>$b->location?->y]),'locations'=>$game->locations->map(fn($l)=>['id'=>$l->id,'name'=>$l->name,'zone'=>$l->zone,'x'=>$l->x,'y'=>$l->y,'allowed'=>$l->allowed_types,'occupied'=>$l->is_occupied,'project'=>$l->project?['type'=>$l->project->project_type,'progress'=>$l->project->progress]:null]),'events'=>$game->events->map(fn($e)=>['type'=>$e->type->value,'title'=>$e->title,'description'=>$e->description,'time'=>$e->starts_at->diffForHumans($game->game_time)]),'history'=>['labels'=>$history->map(fn($s)=>$s->recorded_at->format('H:i')),'demand'=>$history->pluck('demand_mw'),'production'=>$history->pluck('production_mw'),'battery'=>$history->pluck('battery_mwh')],'fin_history'=>['labels'=>$finHistory->map(fn($s)=>$s->recorded_at->format('d M')),'revenue'=>$finHistory->pluck('revenue'),'expenses'=>$finHistory->pluck('expenses'),'profit'=>$finHistory->pluck('profit')]];
    }
}
