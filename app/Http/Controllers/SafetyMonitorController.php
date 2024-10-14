<?php

namespace App\Http\Controllers;

use App\Models\SafetyMonitor;
use App\Services\Alpaca\ClientStatusService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class SafetyMonitorController extends Controller
{
    protected $safety;
    protected $device;

    public function __construct()
    {
        $this->safety = new SafetyMonitor();
        $this->device = 'safety';
    }

    function isSafe(){

        return Response::json([
            'Value' => $this->safety->isSafe(),
            'ErrorNumber' => 0,
            'ErrorMessage' => ""
        ]);
        
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
                ClientStatusService::connect(Session::get('clientid'),$this->device);
                $con = true;
            } else {
                ClientStatusService::disconnect(Session::get('clientid'),$this->device);
            }
            return Response::json(['value' => $con, 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
        } else {
            return Response::json(['ErrorNumber' => 1025, 'ErrorMessage' => "Client ID was not provided"]);
        }
        
    }
    function getDescription(){
        return Response::json(['value' => 'Ascom bridge for safety', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
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
        return Response::json(['value' => 'TeslaSafetyMonitor', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    }
}
