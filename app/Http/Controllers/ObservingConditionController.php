<?php

namespace App\Http\Controllers;

use App\Models\WeatherStation;
use App\Services\Alpaca\ClientStatusService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class ObservingConditionController extends Controller
{

    protected $weatherStation;
    protected $device;

    public function __construct()
    {
        $this->weatherStation = WeatherStation::find(1);
        $this->device = 'observing';
        Session::put('device_type', $this->device);
        Log::info(Session::all());
    }
    function getAvaragePeriod(){
        return Response::json(['value' => '0.0','ErrorNumber' => 0, 'ErrorMessages' => '']);
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
    function sensorDescription(){
        switch (Session::get('sensorname')) {
            case 'windspeed':
            case 'windgust': 
            case 'winddirection':
            case 'rainrate':
            case 'pressure':
            case 'humidity':
            case 'dewpoint':
            //case 'cloudcover':
            case 'temperature':
                return Response::json(['Value' => 'GW2000', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
                break;
            
            default:
                return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
                break;
        }
    }
    function lastUpdate(){
        switch (Session::get('sensorname')) {
            case 'windspeed':
            case 'windgust': 
            case 'winddirection':
            case 'rainrate':
            case 'pressure':
            case 'humidity':
            case 'dewpoint':
            case 'temperature':
                return Response::json(['Value' => $this->weatherStation->updated_at->diffInSeconds(now()), 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
                break;
            
            default:
                return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
                break;

        }
    }
    function propertyNotImplemented(){
        return Response::json([
            'ErrorNumber' => 1024,
            'ErrorMessage' => "Property Not implemented"
        ]);
    }
    function methodNotImplemented(){
        return Response::json([
            'ErrorNumber' => 1024,
            'ErrorMessage' => "Method Not Implemented"
        ]);
    }
    function actionNotImplemented(){
        return Response::json([
            'ErrorNumber' => 1036,
            'ErrorMessage' => "Action Not Implemented"
        ]);
    }
    function getConnectionState(){
        if(Session::get('clientid')){
            if(ClientStatusService::state(Session::get('clientid'),$this->device)){
                return Response::json(['value' => true, 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
            } else {
                return Response::json(['value' => false, 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
            }
        }
        return Response::json(['ErrorNumber' => 1025, 'ErrorMessage' => "ClientID was not provided"],400);
    }
    function putConnectionState(){
        $con = false;
        if(Session::get('clientid')){
            if(Session::get('connected') == true){
                ClientStatusService::connect(Session::get('clientid'),'observing');
                $con = true;
            } else {
                ClientStatusService::disconnect(Session::get('clientid'),'observing');
            }
        }
        return Response::json(['value' => $con, 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    }

    function getDescription(){
        return Response::json(['value' => 'Ascom bridge for meteo and safety', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    }

    function driverInfo(){
        return Response::json(['value' => 'powered by Laravel', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    }
    function driverVersion(){
        return Response::json(['value' => '1.0.0', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    }
    function interfaceVersion(){
        return Response::json(['value' => '1', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    }
    function name(){
        return Response::json(['value' => 'TeslaAscomConnector', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    }

}
