<?php

declare(strict_types=1);

namespace App\Enums;

enum EventType: string
{
    case CityGrowth = 'CITY_GROWTH';
    case Weather = 'WEATHER';
    case EnergyMarket = 'ENERGY_MARKET';
    case Infrastructure = 'INFRASTRUCTURE';
    case Regulation = 'REGULATION';
    case Random = 'RANDOM_EVENT';
}
