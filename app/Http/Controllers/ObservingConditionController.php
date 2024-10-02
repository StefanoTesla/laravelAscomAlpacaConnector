<?php

namespace App\Http\Controllers;

use App\Models\WeatherStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class ObservingConditionController extends Controller
{

    protected $weatherStation;

    public function __construct()
    {
        $this->weatherStation = WeatherStation::find(1);
    }
    function getAvaragePeriod(){
        return Response::json(['value' => '0.0','ErrorNumber' => 0, 'ErrorMessages' => '']);
    }

    function putAvaragePeriod(){
            return Response::json(['ErrorNumber' =>1024 , 'ErrorMessages' => 'Action not implemented']);
        }
    function temperature(){
            return Response::json(['Value' =>$this->weatherStation->temperature ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
    function dewPoint(){
            return Response::json(['Value' =>$this->weatherStation->dewpoint ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
    function humidity(){
            return Response::json(['Value' =>$this->weatherStation->humidity, 'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
    function pressure(){
            return Response::json(['Value' =>$this->weatherStation->pressure, 'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
    function rainrate(){
            return Response::json(['Value' =>$this->weatherStation->rainrate, 'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
    function windSpeed(){
            return Response::json(['Value' =>$this->weatherStation->windspeed, 'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
    function windDirection(){
            return Response::json(['Value' =>$this->weatherStation->winddir, 'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
    function windGust(){
            return Response::json(['Value' =>$this->weatherStation->windgust, 'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
    function sensorDescription(Request $request){
        switch ($request->SensorName) {
            case 'WindSpeed':
            case 'WindGust': 
            case 'WindDirection':
            case 'RainRate':
            case 'Pressure':
            case 'Humidity':
            case 'DewPoint':
            case 'CloudCover':
            case 'Temperature':
                return Response::json(['Value' => 'GW2000', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
                break;
            
            default:
                return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
                break;
        }
    }

    function lastUpdate(Request $request){
        switch ($request->SensorName) {
            case 'WindSpeed':
            case 'WindGust': 
            case 'WindDirection':
            case 'RainRate':
            case 'Pressure':
            case 'Humidity':
            case 'DewPoint':
            case 'CloudCover':
            case 'Temperature':
                return Response::json(['Value' => $this->weatherStation->updated_at->diffInSeconds(now()), 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
                break;
            
            default:
                return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
                break;

        }
    }

}
