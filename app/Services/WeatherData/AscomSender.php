<?php

namespace App\Services\WeatherData;

use Illuminate\Support\Facades\Cache;

class AscomSender{

    static function refreshCache(array $data){

    Cache::forever('ascom.weatherdata', $data);
    }

    static function getData(){

        if (Cache::has('ascom.weatherdata')){
            return Cache::get('ascom.weatherdata');
        } else {
            return null;
        }
        
    }

}