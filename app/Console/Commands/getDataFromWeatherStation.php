<?php

namespace App\Console\Commands;

use App\Enums\SyncStatusEnum;
use App\Models\WeatherData\SingleMeasure\DewPoint;
use App\Models\WeatherData\SingleMeasure\Humidity;
use App\Models\WeatherData\SingleMeasure\Pressure;
use App\Models\WeatherData\SingleMeasure\RainRate;
use App\Models\WeatherData\SingleMeasure\Temperature;
use App\Models\WeatherData\SingleMeasure\Wind;
use App\Models\WeatherData\SingleMeasure\WindGust;
use App\Services\Ascom\AscomObservingCache;
use App\Services\WeatherStations\Gw2000Service;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GetDataFromWeatherStation extends Command
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
        $service = new Gw2000Service();
        $start = microtime(true);
        $gw2000 = $service->getLiveData();
        $finish = round(microtime(true) - $start,3);
        Log::channel('weather_station')->info("GW2000 Data ack in ".$finish." s");

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
            'sync' => SyncStatusEnum::INCOMPLETE
        ]);

        DewPoint::create([
            'value' => $validated['dew_point'],
            'ack_time' => $now,
            'sync' => SyncStatusEnum::INCOMPLETE
        ]);

        Humidity::create([
            'value' => $validated['outdoor_humidity'],
            'ack_time' => $now,
            'sync' => SyncStatusEnum::INCOMPLETE
        ]);

        Wind::create([
            'value' => $validated['wind_speed'],
            'direction' => $validated['wind_direction'],
            'ack_time' => $now,
            'sync' => SyncStatusEnum::INCOMPLETE
        ]);
        WindGust::create([
            'value' => $validated['gust_speed'],
            'ack_time' => $now,
            'sync' => SyncStatusEnum::INCOMPLETE
        ]);

        RainRate::create([
            'value' => $validated['rain_rate'],
            'ack_time' => $now,
            'sync' => SyncStatusEnum::INCOMPLETE
        ]);

        Pressure::create([
            'value' => $validated['absolute_pressure'],
            'ack_time' => $now,
            'sync' => SyncStatusEnum::INCOMPLETE
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

        try {
            AscomObservingCache::refreshCache($ascomData);
            Log::channel('weather_station')->info("--- Refreshing Ascom Cache finish ---");
        } catch (Exception $th) {
            Log::channel('weather_station')->error($th);
        }
        
        
    }     
        
}

