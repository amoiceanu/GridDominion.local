<?php
declare(strict_types=1);
namespace App\Services\Game;
use App\Enums\PlantType;
use App\Models\Battery;
use App\Models\ConstructionProject;
use App\Models\PowerPlant;
final class ConstructionService
{
    public function progressPercent(\DateTimeInterface $start,\DateTimeInterface $end,\DateTimeInterface $now):int{$total=max(1,$end->getTimestamp()-$start->getTimestamp());return (int)max(0,min(100,round(100*($now->getTimestamp()-$start->getTimestamp())/$total)));}
    public function complete(ConstructionProject $project):PowerPlant|Battery
    {
        if($project->project_type==='battery'){$battery=Battery::create(['game_id'=>$project->game_id,'build_location_id'=>$project->build_location_id,'name'=>'Grid Battery '.chr(65+($project->game->batteries()->count()%26)),'capacity_mwh'=>40,'charge_mwh'=>0,'charge_rate_mw'=>10,'discharge_rate_mw'=>10,'auto_mode'=>true]);$project->delete();return $battery;}
        $type=PlantType::from($project->project_type); $plant=PowerPlant::create(['game_id'=>$project->game_id,'build_location_id'=>$project->build_location_id,'name'=>$type->label().' '.chr(65+($project->game->plants()->count()%26)),'type'=>$type,'capacity_mw'=>$type->capacity(),'operating_cost_per_mwh'=>$type->operatingCost(),'co2_g_kwh'=>$type->co2(),'commissioned_at'=>$project->completes_at]); $project->delete(); return $plant;
    }
}
