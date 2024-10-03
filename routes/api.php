<?php
use App\Http\Middleware\ReadAlpacaParameters;
use App\Http\Middleware\AscomAlpacaParameters;
use App\Http\Controllers\ObservingConditionController;



use App\Models\SafetyMonitor;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;



Route::prefix('/v1/observingconditions/0')->middleware([ReadAlpacaParameters::class,AscomAlpacaParameters::class])->group(function () {
    Route::controller(ObservingConditionController::class)->group(function () {

        //properties
        Route::get('avarageperiod', 'getAvaragePeriod');
        Route::put('avarageperiod', 'putAvaragePeriod');
        Route::get('cloudcover', 'propertyNotImplemented');
        Route::put('connected', 'putConnectionState');
        Route::get('connected', 'getConnectionState');
        Route::get('description', 'getDescription');
        Route::get('dewpoint', 'dewpoint');
        Route::get('driverinfo', 'driverInfo');
        Route::get('driverversion', 'driverVersion');
        Route::get('humidity', 'humidity');
        Route::get('interfaceversion', 'interfaceVersion');
        Route::get('name', 'name');
        Route::get('pressure', 'pressure');
        Route::get('rainrate', 'rainrate');
        Route::get('skybrightness', 'propertyNotImplemented');
        Route::get('skyquality', 'propertyNotImplemented');
        Route::get('skytemperature', 'propertyNotImplemented');
        Route::get('starfwhm','propertyNotImplemented');
        Route::get('supportedactions','propertyNotImplemented');
        Route::get('temperature', 'temperature');
        Route::get('winddirection', 'windDirection');
        Route::get('windgust',  'windGust');
        Route::get('windspeed','windSpeed');

        //methods
        Route::put('action','methodNotImplemented');
        Route::put('commandblind','methodNotImplemented');
        Route::put('commandbool','methodNotImplemented');
        Route::put('commandstring','methodNotImplemented');
        Route::put('refresh', 'methodNotImplemented');
        Route::get('sensordescription','sensorDescription');
        Route::get('timesincelastupdate','lastUpdate');
        
    });

    /* common */



});

Route::prefix('/v1/safetymonitor/0')->group(function () {
    Route::get('issafe', function () {
        $safety = new SafetyMonitor();
        return Response::json(['value' => $safety->isSafe(), 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    });

    /* common */
    Route::put('/action', function (Request $request) {
        return Response::json([
            'ErrorNumber' => 1036,
            'ErrorMessage' => "Not implemented"
        ]);
    });
    Route::put('/commandblind', function (Request $request) {
        return Response::json([
            'ErrorNumber' => 1024,
            'ErrorMessage' => "Not implemented"
        ]);
    });
    Route::put('/commandbool', function (Request $request) {
        return Response::json([
            'ErrorNumber' => 1024,
            'ErrorMessage' => "Not implemented"
        ]);
    });
    Route::put('/commandstring', function (Request $request) {
        return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
    });
    Route::put('/connected', function (Request $request) {
        return Response::json(['value' => 'true', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    });
    Route::get('/connected', function (Request $request) {
        return Response::json(['value' => 'true', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    });
    Route::get('/description', function (Request $request) {
        return Response::json(['value' => 'Ascom bridge for safety', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    });
    Route::get('/driverinfo', function (Request $request) {
        return Response::json(['value' => 'powered by Laravel', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    });
    Route::get('/driverversion', function (Request $request) {
        return Response::json(['value' => "1", 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    });
    Route::get('/interfaceversion', function (Request $request) {
        return Response::json(['value' => "1", 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    });
    Route::get('/name', function (Request $request) {
        return Response::json(['value' => 'TeslaAscomConnector', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
    });
    Route::get('/supportedactions', function (Request $request) {
        return Response::json(['value' => '[]', 'ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
    });
});

