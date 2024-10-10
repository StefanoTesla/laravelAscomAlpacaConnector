<?php

namespace App\Http\Controllers;

use App\Models\WeatherData\DewPoint;
use App\Models\WeatherData\Humidity;
use App\Models\WeatherData\Pressure;
use App\Models\WeatherData\RainRate;
use App\Models\WeatherData\Temperature;
use App\Models\WeatherData\Wind;
use App\Models\WeatherData\WindGust;
use App\Services\WeatherData\AscomSender;
use App\Services\WeatherStations\Gw2000Service;
//use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    public function test(){
        $weather = RainRate::where('ack_time', '>', now()->subMinutes(30))
        ->where('value', '<>', 0)
        ->get();
        
        dd($weather);
}
}
