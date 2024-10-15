<?php

namespace App\Console\Commands;

use App\Models\WeatherData\SingleMeasure\CloudCover;
use App\Models\WeatherData\SingleMeasure\SkyBrightness;
use App\Models\WeatherData\SingleMeasure\SkyQuality;
use App\Models\WeatherData\SingleMeasure\SkyTemperature;
use App\Services\Ascom\AscomObservingCache;
use App\Services\WeatherStations\MySQMService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GetDataFromMySQM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getdata:frommysqm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel('weather_station')->info("--- START DATA ACQUISITION ---");
        $service = new MySQMService();
        $start = microtime(true);
        $mysqm = $service->getLiveData();
        $finish = round(microtime(true) - $start,3);
        Log::channel('weather_station')->info("MySQM Data ack in ".$finish." s");

        $validator = Validator::make($mysqm,
            [
                'sqm' => 'required|numeric',
                'sky_brightness' => 'required|numeric',
                'sky_temperature' => 'required|numeric',
                'cloud_cover' => 'required|numeric',
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

        /**
         * store data
         */

        Log::channel('weather_station')->info("--- Weather Station Data Acquisition finish ---");
        
        $ascomData = [
            'skyquality' => [
                'value' => $validated['sqm'],
                'sync' => $now,
                'desc' => SkyQuality::getDescription()
                ],
            'skybrightness' => [
                'value' => $validated['sky_brightness'],
                'sync' => $now,
                'desc' => SkyBrightness::getDescription()
                ],
            'skytemperature' => [
                'value' => $validated['sky_temperature'],
                'sync' => $now,
                'desc' => SkyTemperature::getDescription()
                ],
            'cloudcover' => [
                'value' => $validated['rain_rate'],
                'sync' => $now,
                'desc' => CloudCover::getDescription()
                ]
        ];
        Log::channel('weather_station')->info("--- Refreshing Ascom Cache ---");
        try {
            AscomObservingCache::refreshCache($ascomData);
        } catch (\Throwable $th) {
            Log::channel('weather_station')->error($th);
        }
        Log::channel('weather_station')->info("--- Refreshing Ascom Cache finish ---");
    }
}
