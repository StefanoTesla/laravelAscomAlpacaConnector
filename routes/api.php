<?php
use App\Http\Middleware\Alpaca\ReadAlpacaParameters;
use App\Http\Middleware\Alpaca\WriteAlpacaParameters;
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
        WriteAlpacaParameters::class])
    ->group(function () {
    Route::controller(ObservingConditionController::class)->group(function () {

        //properties
        Route::get('avarageperiod', 'getAvaragePeriod');
        Route::put('avarageperiod', 'propertyNotImplemented');

        Route::get('cloudcover', 'getMeasure');
        Route::get('description', 'getDescription');
        Route::get('dewpoint', 'getMeasure');
        Route::get('driverinfo', 'driverInfo');
        Route::get('driverversion', 'driverVersion');
        Route::get('humidity', 'getMeasure');
        Route::get('interfaceversion', 'interfaceVersion');
        Route::get('name', 'name');
        Route::get('pressure', 'getMeasure');
        Route::get('rainrate', 'getMeasure');
        Route::get('skybrightness', 'getMeasure');
        Route::get('skyquality', 'getMeasure');
        Route::get('skytemperature', 'getMeasure');
        Route::get('starfwhm','getMeasure');
        Route::get('supportedactions','propertyNotImplemented');
        Route::get('temperature', 'getMeasure');
        Route::get('winddirection', 'getMeasure');
        Route::get('windgust',  'getMeasure');
        Route::get('windspeed','getMeasure');

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
        WriteAlpacaParameters::class])
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
        WriteAlpacaParameters::class])
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
        WriteAlpacaParameters::class])
    ->group(function () {
    Route::controller(SafetyMonitorController::class)->group(function () {
        Route::put('connected', 'putConnectionState');
        Route::get('connected', 'getConnectionState');
    });

});
