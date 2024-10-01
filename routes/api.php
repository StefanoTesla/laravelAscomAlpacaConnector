<?php

use App\Http\Controllers\ObservingConditionController;
use App\Models\SafetyMonitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;



Route::prefix('/v1/observingconditions/0')->group(function () {
    Route::controller(ObservingConditionController::class)->group(function () {
        Route::get('avarageperiod', 'getAvaragePeriod');
        Route::put('avarageperiod', 'putAvaragePeriod');
        Route::get('temperature', 'temperature');
        Route::get('humidity', 'humidity');
        Route::get('dewpoint', 'dewpoint');
        Route::get('pressure', 'pressure');
        Route::get('rainrate', 'rainrate');
        Route::get('winddirection', 'windDirection');
        Route::get('windgust',  'windGust');
        Route::get('windspeed','windSpeed');
        Route::get('sensordescription','sensorDescription');
        Route::get('timesincelastupdate','lastUpdate');
    });

    Route::get('/cloudcover', function (Request $request) {
        return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
    });

    Route::put('/refresh', function (Request $request) {
        return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
    });

    Route::get('/skybrightness', function (Request $request) {
        return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
    });
    Route::get('/skyquality', function (Request $request) {
        return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
    });
    Route::get('/skytemperature', function (Request $request) {
        return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
    });
    Route::get('/starfwhm', function (Request $request) {
        return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
    });

    /* common */
    Route::put('/action', function (Request $request) {
        return Response::json(['ErrorNumber' => 1036, 'ErrorMessage' => "Not implemented"]);
    });
    Route::put('/commandblind', function (Request $request) {
        return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
    });
    Route::put('/commandbool', function (Request $request) {
        return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
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
        return Response::json(['value' => 'Ascom bridge for meteo and safety', 'ErrorNumber' => 0, 'ErrorMessage' => ""]);
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

Route::prefix('/v1/safetymonitor/0')->group(function () {
    Route::get('issafe', function () {
        $safety = new SafetyMonitor();

        dd($safety->isSafe());

    });

    /* common */
    Route::put('/action', function (Request $request) {
        return Response::json(['ErrorNumber' => 1036, 'ErrorMessage' => "Not implemented"]);
    });
    Route::put('/commandblind', function (Request $request) {
        return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
    });
    Route::put('/commandbool', function (Request $request) {
        return Response::json(['ErrorNumber' => 1024, 'ErrorMessage' => "Not implemented"]);
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