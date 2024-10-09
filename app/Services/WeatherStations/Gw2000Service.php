<?php

namespace App\Services\WeatherStations;

use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class Gw2000Service{

    private $ip;
    const LIVE_DATA_URL = 'get_livedata_info?';
    const RAIN_DATA_URL = 'get_piezo_rain?';
    const VERSION_URL = 'get_version?';
    const NETWORK_URL = 'get_network_info?';

    private $commands = [
        "0x01" =>['comment' =>'indoor_temperature'],			   
        "0x02" =>['comment' =>'outdoor_temperature'],		   
        "0x03" =>['comment' =>'dew_point'],   
        "0x04" =>['comment' =>'Wind chill'],		   
        "0x05" =>['comment' =>'Heat index'],		   
        "0x06" =>['comment' =>'indoor_humidity'],		   
        "0x07" =>['comment' =>'outdoor_humidity'],	   
        "0x08" =>['comment' =>'absolute_pressure'],		   
        "0x09" =>['comment' =>'relative_pressure'],		   
        "0x0A" =>['comment' =>'wind_direction'],	   
        "0x0B" =>['comment' =>'wind_speed'],  
        "0x0C" =>['comment' =>'gust_speed'], 
        "0x0D" =>['comment' =>'rain_event'],   
        "0x0E" =>['comment' =>'rain_rate'],
        "0x0F" =>['comment' =>'rain_gain'],  
        "0x10" =>['comment' =>'rain_day'],  
        "0x11" =>['comment' =>'rain_week'], 
        "0x12" =>['comment' =>'rain_month'],
        "0x13" =>['comment' =>'rain_year'],
        "0x14" =>['comment' =>'rain_totals'],
        "0x15" =>['comment' =>'light'],
        "0x16" =>['comment' =>'UV'],   
        "0x17" =>['comment' =>'UVI'], 
        "0x19" =>['comment' =>'wind_max_day'],
        //wh25
        "intemp"=>['comment' =>'indoor_temperature'],
        "inhumi"=>['comment' =>'indoor_humidity'],
        "abs"=> ['comment' =>'absolute_pressure'],
        "rel"=> ['comment' =>'relative_pressure'],
    ];

    public function __construct() {
        $this->ip = config('tesla.wheatherStationIP');
    }

    public function getLiveData(){
        try{
            $response =  Http::get('http://'.$this->ip.'/'.self::LIVE_DATA_URL);
        }
        catch (Exception $e) {
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

        $struct = [];
        foreach ($data as $list) {
            foreach($list as $row){

                if(isset($row['id']) && isset($this->commands[$row['id']])){
                    if(isset($row['unit'])){
                        $struct[$this->commands[$row['id']]['comment']] = ['val' => $row['val'], 'unit' => $row['unit']];
                    }else {
                        $struct[$this->commands[$row['id']]['comment']] = $row['val'];
                    }
                    
                } else {
                    if(is_array($row)){
                        foreach($row as $key => $value){
                            if(isset($this->commands[$key])){
                                $struct[$this->commands[$key]['comment']] = $value;
                            }
                        }
                    } else {
                        Log::info("non array".$row);
                    }
                }
            }
        }

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
        }
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