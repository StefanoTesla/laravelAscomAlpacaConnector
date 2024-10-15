<?php

namespace App\Services\WeatherStations;

use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class MySQMService{

    private $ip;
    const LIVE_DATA_URL = 'rd';
    const RAIN_DATA_URL = 'get_piezo_rain?';
    const VERSION_URL = 'get_version?';
    const NETWORK_URL = 'get_network_info?';

    private $commands = [
        "sqm" =>['comment' =>'sqm'],
        "nelm" =>['comment' =>'nelm'],			   
        "lux" =>['comment' =>'sky_brightness'],
        "ambient" => ['comment' =>'outdoor_temperature'],
        "humidity" => ['comment' => 'outdoor_humidity'],
        "dewpoint" => ['comment'=>'dew_point'],
        "pressure" =>['comment' =>'absolute_pressure'],
        "skyobject" =>['comment' =>'sky_temperature'],   
        "cloudcover" =>['comment' =>'cloud_cover'],
    ];

    public function __construct() {
        $this->ip = 'https://mysqm.marcoformenton.duckdns.org';
    }

    public function getLiveData(){
        try{
            $response =  Http::get($this->ip.'/'.self::LIVE_DATA_URL); 
        }
        catch (Exception $e) {
            Log::info($e);
            return $e;
        }

        try{
            $data = $this->handleLiveData($response);
        }
        catch (Exception $e) {
            return $e;
        }

        return $data;
        
        
    }

    public function getRainData(){
        try{
            $response =  Http::get('http://'.$this->ip.'/'.self::RAIN_DATA_URL);
        }
        catch (Exception $e) {
            return $e;
        }

        dd($response->json());
    }

    public function getVersion(){
        try{
            $response =  Http::get('http://'.$this->ip.'/'.self::VERSION_URL);
        }
        catch (Exception $e) {
            return $e;
        }

        dd($response->json());
    }


    private function handleLiveData(Response $response){
        $data = $response->json();
        Log::info($data);
        $struct = [];
        foreach ($data as $key => $measure) {
            if(isset($this->commands[$key])){
                $struct[$this->commands[$key]['comment']] = $measure;
            }
        }

        /*NO conversion for the moment 
        foreach($struct as $comment => $value){
            switch ($comment){
                case "dew_point":
                case "indoor_temperature":
                case "outdoor_temperature":
                    if(isset($value['unit'])){
                        if($value['unit'] == "F"){
                            $struct[$comment] = $this->convertFtoC($value);
                        } else {
                            $struct[$comment] = floatval($value['val']);
                        }
                    } else {
                        $struct[$comment] = floatval($value);
                    }
                    break;
                case "indoor_humidity":
                case "outdoor_humidity":
                    $struct[$comment] = intval(str_replace("%","",$value));
                    break;
                case 'wind_speed':
                case 'gust_speed':
                case 'wind_max_day':
                    $struct[$comment] = $this->convertWindUnit($value);
                    break;
                case 'wind_direction':
                    $struct[$comment] = intval($value);
                    break;
                case 'absolute_pressure':
                case 'relative_pressure':
                    $struct[$comment] = $this->convertPressureUnit($value);
                    break;
                case 'rain_rate':
                case 'rain_event':
                case 'rain_day':  
                case 'rain_week': 
                case 'rain_month':
                case 'rain_year':
                case 'rain_totals':
                    $struct[$comment] = $this->convertRainUnit($value);
                    break;
                case 'UVI':
                    $struct[$comment] = intval($value);
                    break;
                case 'light':
                    $struct[$comment] = floatval(str_replace(" Klux", "",$value));
                    break;
            }
        } NO conversion for the moment */
        return $struct;
    }
    

    private function convertFtoC($value){
        return round(floatval(($value['val'] - 32) / 1.8),1);
    }

    private function convertWindUnit($value){
        if(str_contains($value,"m/s")){
            $tmp = floatval(str_replace(" m/s", "",$value));
            return round($tmp,2);
        }
        if(str_contains($value,"km/h")){
            $tmp = floatval(str_replace(" km/h", "",$value));
            $tmp = $tmp * 0.277777;
            return round($tmp,2);
        }
        if(str_contains($value," mph")){
            $tmp = floatval(str_replace(" mph", "",$value));
            $tmp = $tmp * 0.447;
            return round($tmp,2);
        }
        if(str_contains($value," knots")){
            $tmp = floatval(str_replace(" knots", "",$value));
            $tmp = $tmp * 0.514444;
            return round($tmp,2);
        }

    }

    private function convertPressureUnit($value){
        if(str_contains($value,"hPa")){
            $tmp = floatval(str_replace(" hPa", "",$value));
            return round($tmp,2);
        }
        if(str_contains($value,"inHg")){
            $tmp = floatval(str_replace(" inHg", "",$value));
            $tmp = $tmp * 33.8639;
            return round($tmp,2);
        }
        if(str_contains($value,"mmHg")){
            $tmp = floatval(str_replace(" mmHg", "",$value));
            $tmp = $tmp * 1.33322;
            return round($tmp,2);
        }
    }

    private function convertRainUnit($value){
        if(str_contains($value,"mm")){
            $tmp = floatval(str_replace(" mm", "",$value));
            return round($tmp,2);
        }
        if(str_contains($value,"in")){
            $tmp = floatval(str_replace(" in", "",$value));
            $tmp = $tmp * 25.4;
            return round($tmp,1);
        }
        if(str_contains($value,"mm/Hr")){
            $tmp = floatval(str_replace(" mm/Hr", "",$value));
            return round($tmp,2);
        }
        if(str_contains($value,"in/Hr")){
            $tmp = floatval(str_replace(" in/Hr", "",$value));
            $tmp = $tmp * 25.4;
            return round($tmp,1);
        }
    }
}