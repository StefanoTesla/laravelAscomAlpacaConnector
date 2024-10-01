<?php

use App\Models\WeatherStation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return view('welcome');
});

/*
Route::get('/crea', function () {
    $ws = New WeatherStation();
    $ws->save();
    return view('welcome');
});
*/

Route::get('management/apiversion', function () {
    return Response::json(['Value' => [1]]);
});

Route::get('management/v1/description', function () {
    return Response::json([
        'Value' => [
            "ServerName"=> "TeslaAlpacaConnector",
            "Manufacturer"=> "TeslaCompany",
            "ManufacturerVersion"=> "v1.0.0",
            "Location"=>"Cerreto Guidi,IT"
        ]
    ]);
});


Route::get('management/v1/configureddevices', function () {
    return Response::json([
        'Value' => [
            [
                "DeviceName"=> "MeteoGard",
                "DeviceType"=> "ObservingConditions",
                "DeviceNumber" => 0,
                "UniqueID"=> "277C652F-2BB9-4E86-A6A6-9230C42876FA"
            ],
            [
                "DeviceName"=> "SafetyGard",
                "DeviceType"=> "SafetyMonitor",
                "DeviceNumber" => 0,
                "UniqueID"=> "277C652F-2BC9-4E86-A6A6-9230C42876FA"
            ]
        ]
    ]);
});