<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WeatherStation;

class SafetyMonitor extends Model
{
    use HasFactory;

    public function isSafe(){

        $weather = WeatherStation::find(1);

        return $weather->rainrate ? false : true;

    }
}
