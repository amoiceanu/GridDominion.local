<?php
declare(strict_types=1);
namespace App\Services\Game;
final class WeatherService
{
    public function generate(?int $seed=null):array
    {
        if($seed!==null)mt_srand($seed); $options=[['Sunny',27,14,1,.42],['Cloudy',19,20,.48,.62],['Rain',15,24,.28,.74],['Windy',18,38,.72,1.08],['Calm',22,7,.82,.18],['Storm',13,52,.2,1.15]];
        [$condition,$temp,$wind,$solar,$windFactor]=$options[array_rand($options)]; return compact('condition','temp','wind','solar','windFactor');
    }
}
