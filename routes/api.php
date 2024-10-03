<?php
use App\Http\Middleware\ReadAlpacaParameters;
use App\Http\Middleware\AscomAlpacaParameters;
use App\Http\Controllers\ObservingConditionController;
use App\Http\Controllers\SafetyMonitorController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

Route::get('test', function(){
    return Response::json(['hello' => 'word']);
});
Route::prefix('/v1/observingconditions/0')
    ->middleware(
        [ReadAlpacaParameters::class,
        'client.connected:observing',
        AscomAlpacaParameters::class])
    ->group(function () {
    Route::controller(ObservingConditionController::class)->group(function () {

        //properties
        Route::get('avarageperiod', 'getAvaragePeriod');
        Route::put('avarageperiod', 'propertyNotImplemented');
        Route::get('cloudcover', 'propertyNotImplemented');
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
        Route::put('action','actionNotImplemented');
        Route::put('commandblind','methodNotImplemented');
        Route::put('commandbool','methodNotImplemented');
        Route::put('commandstring','methodNotImplemented');
        Route::put('refresh', 'methodNotImplemented');
        Route::get('sensordescription','sensorDescription');
        Route::get('timesincelastupdate','lastUpdate');
        
    });

});
/* connection should be outside connection check middleware, the ->withNoMiddleware helper doesen't work */
Route::prefix('/v1/observingconditions/0')
    ->middleware(
        [ReadAlpacaParameters::class,
        AscomAlpacaParameters::class])
    ->group(function () {
    Route::controller(ObservingConditionController::class)->group(function () {
        Route::put('connected', 'putConnectionState');
        Route::get('connected', 'getConnectionState');
        
    });

});


Route::prefix('/v1/safetymonitor/0')
    ->middleware(
        [ReadAlpacaParameters::class,
        'client.connected:safety',
        AscomAlpacaParameters::class])
    ->group(function () {
    Route::controller(SafetyMonitorController::class)->group(function () {

        //properties
        Route::get('issafe', 'isSafe');
        Route::get('description', 'getDescription');
        Route::get('driverinfo', 'driverInfo');
        Route::get('driverversion', 'driverVersion');
        Route::get('interfaceversion', 'interfaceVersion');
        Route::get('name', 'name');
        Route::get('supportedactions','propertyNotImplemented');

        //methods
        Route::put('action','actionNotImplemented');
        Route::put('commandblind','methodNotImplemented');
        Route::put('commandbool','methodNotImplemented');
        Route::put('commandstring','methodNotImplemented');        
    });

});
/* connection should be outside connection check middleware, the ->withNoMiddleware helper doesen't work */
Route::prefix('/v1/safetymonitor/0')
    ->middleware(
        [ReadAlpacaParameters::class,
        AscomAlpacaParameters::class])
    ->group(function () {
    Route::controller(SafetyMonitorController::class)->group(function () {
        Route::put('connected', 'putConnectionState');
        Route::get('connected', 'getConnectionState');
    });

});
