<?php

namespace App\Services\WeatherData;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AscomSender{

    static function refreshCache(array $newWheaterData){

    $cache = Cache::get('ascom.weatherdata');

    $struct = [
        'cloudcover' => null,
        'dewpoint' => null,
        'humidity' => null,
        'pressure' => null,
        'rainrate' => null,
        'skybrightness' => null,
        'skyquality' => null,
        'skytemperature' => null,
        'starfwhm' => null,
        'temperature' => null,
        'winddirection' => null,
        'windgust' => null,
        'windspeed' => null,
    ];

    if(isset($cache)){
        foreach($cache as $measure => $data){
            if(isset($data['sync'])){
                if(($data['sync'] < now()->subMinutes(10))){
                    unset($cache[$measure]);
                }
            } else {
                unset($cache[$measure]);
            }
    
        }
    }


    

    //check if incoming data are fresh and store them
    foreach($newWheaterData as $measure => $value){
        if($value['sync'] > now()->subMinutes(1)){
            if(isset($cache[$measure]['sync'])){
                if($value['sync'] > $cache[$measure]['sync']){
                    $cache[$measure] = $value;
                }
            } else {
                $cache[$measure] = $value;
            }
        } else {
            unset($cache[$measure]);
        }
    }


    //create a new cache object
    foreach($struct as $measure => $values){
        if(isset($cache[$measure])){
            $struct[$measure] = $cache[$measure];
        }
    }

    Cache::forever('ascom.weatherdata', $struct);

    }

    static function getData(){

        if (Cache::has('ascom.weatherdata')){
            return Cache::get('ascom.weatherdata');
        } else {
            return null;
        }
        
    }

}