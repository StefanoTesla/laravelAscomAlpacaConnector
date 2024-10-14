<?php

namespace App\Models;
use App\Models\WeatherData\RainRate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyMonitor extends Model
{
    use HasFactory;

    public function isSafe(){

        $conditions =[
            'dataFromGW200Available' => false,
            'gotRainInTheLatestPeriod' => false
        ];

        $RainData = RainRate::where('ack_time', '>', now()->subMinutes(30))->get();
        
        if($RainData->count()){
            $conditions['dataFromGW200Available'] = true;
        }

        $rain = $RainData->sum('value');

        if($rain == 0){
            $conditions['gotRainInTheLatestPeriod'] = true;
        }

        if(
            $conditions['dataFromGW200Available'] &&
            $conditions['gotRainInTheLatestPeriod']
        ){
            return true;
        }

        return false;

    }
}
