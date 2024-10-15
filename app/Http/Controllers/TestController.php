<?php

namespace App\Http\Controllers;

use App\Models\WeatherData\Report\ShortTimeReport;
use App\Models\WeatherData\SingleMeasure\DewPoint;
use App\Models\WeatherData\SingleMeasure\Humidity;
use App\Models\WeatherData\SingleMeasure\Pressure;
use App\Models\WeatherData\SingleMeasure\RainRate;
use App\Models\WeatherData\SingleMeasure\Temperature;
use App\Models\WeatherData\SingleMeasure\Wind;
use App\Models\WeatherData\SingleMeasure\WindGust;
use App\Services\Ascom\AscomObservingCache;
use App\Services\WeatherData\Report\ShortTimeReportService;
use App\Services\WeatherStations\Gw2000Service;
//use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Constraint\IsFalse;

class TestController extends Controller
{
    public function test(Request $request){
 
        $rep = new ShortTimeReportService();

        $rep->createReport();
    }
}
