<?php

namespace App\Http\Controllers;

use App\Services\Alpaca\ClientStatusService;
use App\Services\WeatherData\AscomSender;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

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

    public function cloudcover(){
        if(isset($this->data['cloudcover'])){
            return Response::json(['Value' =>$this->data['cloudcover']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
        return $this->propertyNotImplemented();
    }
    public function dewPoint(){
        if(isset($this->data['dewpoint'])){
            return Response::json(['Value' =>$this->data['dewpoint']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        } 
        return $this->propertyNotImplemented();
    }
    public function humidity(){
        if(isset($this->data['humidity'])){
            return Response::json(['Value' =>$this->data['humidity']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        } 
        return $this->propertyNotImplemented();
    }
    public function pressure(){
        if(isset($this->data['pressure'])){
            return Response::json(['Value' =>$this->data['pressure']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
        return $this->propertyNotImplemented();
    }
    public function rainrate(){
        if(isset($this->data['rainrate'])){
            return Response::json(['Value' =>$this->data['rainrate']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
        return $this->propertyNotImplemented();
    }
    public function skybrightness(){
        if(isset($this->data['skybrightness'])){
            return Response::json(['Value' =>$this->data['skybrightness']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
        return $this->propertyNotImplemented();       
    }
    public function skyquality(){
        if(isset($this->data['skyquality'])){
            return Response::json(['Value' =>$this->data['skyquality']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
        return $this->propertyNotImplemented();    
    }
    public function skytemperature(){
        if(isset($this->data['skytemperature'])){
            return Response::json(['Value' =>$this->data['skytemperature']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
        return $this->propertyNotImplemented();    
    }
    public function starfwhm(){
        if(isset($this->data['starfwhm'])){
            return Response::json(['Value' =>$this->data['starfwhm']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        } 
        return $this->propertyNotImplemented();      
    }
    public function temperature(){
        if(isset($this->data['temperature'])){
            return Response::json(['Value' =>$this->data['temperature']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        } 
        return $this->propertyNotImplemented();
    }
    public function windDirection(){
        if(isset($this->data['winddirection'])){
            return Response::json(['Value' =>$this->data['winddirection']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        } 
        return $this->propertyNotImplemented(); 
    }
    public function windGust(){
        if(isset($this->data['windgust'])){
            return Response::json(['Value' =>$this->data['windgust']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
        }
        return $this->propertyNotImplemented(); 
    }
    public function windSpeed(){
        if(isset($this->data['windspeed'])){
            return Response::json(['Value' =>$this->data['windspeed']['value'] ,'ErrorNumber' =>0, 'ErrorMessages' => '']);
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
