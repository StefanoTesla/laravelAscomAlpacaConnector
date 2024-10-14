<?php

namespace App\Console\Commands;

use App\Models\WeatherData\DewPoint;
use App\Models\WeatherData\Humidity;
use App\Models\WeatherData\Pressure;
use App\Models\WeatherData\RainRate;
use App\Models\WeatherData\Temperature;
use App\Models\WeatherData\Wind;
use App\Models\WeatherData\WindGust;
use App\Models\WeatherStation;
use App\Services\WeatherData\AscomSender;
use App\Services\WeatherStations\Gw2000Service;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class getDataFromWeatherStation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getdata:fromweatherstation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read the data from the weather Station';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel('weather_station')->info("--- START DATA ACQUISITION ---");
        $service = new Gw2000Service();

        $gw2000 = $service->getLiveData();


        $validator = Validator::make($gw2000,
            [
                'outdoor_temperature' => 'required|numeric',
                'dew_point' => 'required|numeric',
                'outdoor_humidity' => 'required|numeric',
                'wind_speed' => 'required|numeric',
                'wind_direction' => 'required|numeric',
                'gust_speed' => 'required|numeric',
                'rain_rate' => 'required|numeric',
                'absolute_pressure' => 'required|numeric',
            ]
        );

        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->keys() as $key) {
                foreach ($errors->get($key) as $message) {
                    Log::channel('weather_station')->alert("Errore nel campo '$key': $message");
                }
            }
            $this->fail('Error during data validation.');
        }

        $validated = $validator->validated();

        $now = now();
        //store data

        Temperature::create([
            'value' => $validated['outdoor_temperature'],
            'ack_time' => $now,
            'sync' => false
        ]);

        DewPoint::create([
            'value' => $validated['dew_point'],
            'ack_time' => $now,
            'sync' => false
        ]);

        Humidity::create([
            'value' => $validated['outdoor_humidity'],
            'ack_time' => $now,
            'sync' => false
        ]);

        Wind::create([
            'value' => $validated['wind_speed'],
            'direction' => $validated['wind_direction'],
            'ack_time' => $now,
            'sync' => false
        ]);
        WindGust::create([
            'value' => $validated['gust_speed'],
            'ack_time' => $now,
            'sync' => false
        ]);

        RainRate::create([
            'value' => $validated['rain_rate'],
            'ack_time' => $now,
            'sync' => false
        ]);

        Pressure::create([
            'value' => $validated['absolute_pressure'],
            'ack_time' => $now,
            'sync' => false
        ]);

        $ascomData = [
            'dewpoint' => [
                'value' => $validated['dew_point'],
                'sync' => $now,
                'desc' => DewPoint::getDescription()
                ],
             'humidity' => [
                'value' => $validated['outdoor_humidity'],
                'sync' => $now,
                'desc' => Humidity::getDescription()
                ],
             'pressure' => [
                'value' => $validated['absolute_pressure'],
                'sync' => $now,
                'desc' => Pressure::getDescription()
                ],
             'rainrate' => [
                'value' => $validated['rain_rate'],
                'sync' => $now,
                'desc' => RainRate::getDescription()
                ],
             'temperature' => [
                'value' => $validated['outdoor_temperature'],
                'sync' => $now,
                'desc' => Temperature::getDescription()
                ],
             'winddirection' => [
                'value' => $validated['wind_direction'],
                'sync' => $now,
                'desc' => Wind::getDescription()
                ],
             'windgust' => [
                'value' => $validated['gust_speed'],
                'sync' => $now,
                'desc' => WindGust::getDescription()
                ],
             'windspeed' => [
                    'value' => $validated['wind_speed'],
                     'sync' => $now,
                     'desc' => Wind::getDescription()
                 ],
        ];
        Log::channel('weather_station')->info("--- Weather Station Data Acquisition finish ---");
        $ascomData = [
            'dewpoint' => [
                'value' => $validated['dew_point'],
                'sync' => $now,
                'desc' => DewPoint::getDescription()
                ],
             'humidity' => [
                'value' => $validated['outdoor_humidity'],
                'sync' => $now,
                'desc' => Humidity::getDescription()
                ],
             'pressure' => [
                'value' => $validated['absolute_pressure'],
                'sync' => $now,
                'desc' => Pressure::getDescription()
                ],
             'rainrate' => [
                'value' => $validated['rain_rate'],
                'sync' => $now,
                'desc' => RainRate::getDescription()
                ],
             'temperature' => [
                'value' => $validated['outdoor_temperature'],
                'sync' => $now,
                'desc' => Temperature::getDescription()
                ],
             'winddirection' => [
                'value' => $validated['wind_direction'],
                'sync' => $now,
                'desc' => Wind::getDescription()
                ],
             'windgust' => [
                'value' => $validated['gust_speed'],
                'sync' => $now,
                'desc' => WindGust::getDescription()
                ],
             'windspeed' => [
                    'value' => $validated['wind_speed'],
                     'sync' => $now,
                     'desc' => Wind::getDescription()
                 ],
        ];
        Log::channel('weather_station')->info("--- Refreshing Ascom Cache ---");
        try {
            AscomSender::refreshCache($ascomData);
        } catch (Exception $th) {
            Log::channel('weather_station')->error($th);
        }
        

        Log::channel('weather_station')->info("--- Refreshing Ascom Cache finish ---");
    }     
        
}

