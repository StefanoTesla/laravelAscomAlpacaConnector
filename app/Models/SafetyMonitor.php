<?php

namespace App\Models;
use App\Models\WeatherData\RainRate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WeatherStation;

class SafetyMonitor extends Model
{
    use HasFactory;

    public function isSafe(){

        $weather = RainRate::where('ack_time', '>', now()->subMinutes(30))
        ->where('value', '<>', 0)
        ->get();

        if(count($weather) == 0) {
            return true;
        }

        return false;

    }
}
