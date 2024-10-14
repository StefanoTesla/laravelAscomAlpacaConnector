<?php

namespace App\Models\Ascom;
use App\Models\WeatherData\SingleMeasure\RainRate;
use Illuminate\Database\Eloquent\Model;

class SafetyMonitor extends Model
{

    public function isSafe(){

        $conditions =[
            'dataFromGW200Available' => false,
            'gotRainInTheLatestPeriod' => false
        ];

        //check if data is populated in the latest 30minutes
        $RainData = RainRate::where('ack_time', '>', now()->subMinutes(30))->get();
        
        if($RainData->count()){
            $conditions['dataFromGW200Available'] = true;
        }

        //check if there was rain in the latest 30minutes
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
