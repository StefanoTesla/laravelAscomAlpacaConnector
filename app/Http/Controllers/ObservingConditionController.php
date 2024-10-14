<?php

namespace App\Http\Controllers;

use App\Services\Alpaca\ClientStatusService;
use App\Services\WeatherData\AscomSender;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ObservingConditionController extends Controller
{

    protected $weatherStation;
    protected $device;
    protected $data;

    public function __construct()
    {
        $this->device = 'observing';
        $this->data = AscomSender::getData();
    }
    public function getAvaragePeriod(){
        return Response::json(['value' => '0.0','ErrorNumber' => 0, 'ErrorMessages' => '']);
    }
    public function getMeasure(Request $request){
        $measure = last(request()->segments());
        if(isset($this->data[$measure])){
            return Response::json(['Value' =>$this->data[$measure]['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
        return $this->propertyNotImplemented();
    }
    public function sensorDescription(){
        $sensorName = Session::get('sensorname');
        if( isset($this->data[$sensorName]['desc']) ){
            return Response::json(['Value' => $this->data[$sensorName]['desc'], 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
        } else {
            return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
        }
    }
    public function lastUpdate(){
        $sensorName = Session::get('sensorname');
        if( isset($this->data[$sensorName]['sync']) ){
            return Response::json(['Value' => $this->data[$sensorName]['sync']->diffInSeconds(now()), 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
        } else {
            return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
        }        
    }
    public function propertyNotImplemented(){
        return Response::json([
            'ErrorNumber' => 1024,
            'ErrorMessage' => "Property Not implemented"
        ]);
    }
    public function methodNotImplemented(){
        return Response::json([
            'ErrorNumber' => 1024,
            'ErrorMessage' => "Method Not Implemented"
        ]);
    }
    public function actionNotImplemented(){
        return Response::json([
            'ErrorNumber' => 1036,
            'ErrorMessage' => "Action Not Implemented"
        ]);
    }
    public function getConnectionState(){
        if(Session::get('clientid')){
            if(ClientStatusService::state(Session::get('clientid'),$this->device)){
                return Response::json(['value' => true, 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
            } else {
                return Response::json(['value' => false, 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
            }
        }
        return Response::json(['ErrorNumber' => 1025, 'ErrorMessage' => "ClientID was not provided"],400);
    }
    public function putConnectionState(){
        $con = false;
        if(Session::get('clientid')){
            if(Session::get('connected') == true){
                ClientStatusService::connect(Session::get('clientid'),'observing');
                $con = true;
            } else {
                ClientStatusService::disconnect(Session::get('clientid'),'observing');
            }
            return Response::json(['value' => $con, 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
        } else {
            return Response::json(['ErrorNumber' => 1025, 'ErrorMessage' => "Client ID was not provided"]);
        }
    }
    public function getDescription(){
        return Response::json(['value' => 'Ascom bridge for meteo and safety', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    }
    public function driverInfo(){
        return Response::json(['value' => 'powered by Laravel', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    }
    public function driverVersion(){
        return Response::json(['value' => '1.0.0', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    }
    public function interfaceVersion(){
        return Response::json(['value' => '1', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    }
    public function name(){
        return Response::json(['value' => 'TeslaAscomConnector', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    }

}
