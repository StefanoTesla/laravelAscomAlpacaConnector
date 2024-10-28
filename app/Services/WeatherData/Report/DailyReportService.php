<?php

namespace App\Services\WeatherData\Report;

use App\Models\WeatherData\Report\ShortTimeReport;
use App\Models\WeatherData\SingleMeasure\DewPoint;
use App\Models\WeatherData\SingleMeasure\Humidity;
use App\Models\WeatherData\SingleMeasure\Pressure;
use App\Models\WeatherData\SingleMeasure\RainRate;
use App\Models\WeatherData\SingleMeasure\Temperature;
use App\Models\WeatherData\SingleMeasure\Wind;
use App\Models\WeatherData\SingleMeasure\WindGust;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ShortTimeReportService{
    private $startMainInterval;
    private $endMainInterval;
    private $currentInterval;
    private $endInterval;


}