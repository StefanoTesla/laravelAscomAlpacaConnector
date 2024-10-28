<?php

namespace App\Services\WeatherData\Report;

use App\Enums\SyncStatusEnum;
use App\Models\WeatherData\Report\ShortTimeReport;
use App\Services\WeatherData\Report\ReportSenderService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

use App\Models\WeatherData\SingleMeasure\Temperature;
use App\Models\WeatherData\SingleMeasure\DewPoint;
use App\Models\WeatherData\SingleMeasure\Humidity;
use App\Models\WeatherData\SingleMeasure\Pressure;
use App\Models\WeatherData\SingleMeasure\RainRate;
use App\Models\WeatherData\SingleMeasure\Wind;
use App\Models\WeatherData\SingleMeasure\WindGust;
use App\Models\WeatherData\SingleMeasure\SkyBrightness;
use App\Models\WeatherData\SingleMeasure\SkyQuality;
use App\Models\WeatherData\SingleMeasure\SkyTemperature;
use App\Models\WeatherData\SingleMeasure\CloudCover;
use Illuminate\Support\Facades\Artisan;

class ShortTimeReportService{
    private $startMainInterval;
    private $endMainInterval;
    private $endLoopTime;
    private $currentInterval;
    private $endInterval;

    private const MODELS = [
        'temperature' =>[
            'class'=> Temperature::class,
            'calc' => 'avg'],
        'dew_point' =>[
            'class'=> DewPoint::class,
            'calc' => 'avg'],
        'humidity' =>[
            'class'=> Humidity::class,
            'calc' => 'avg'],
        'pressure' =>[
            'class'=> Pressure::class,
            'calc' => 'avg'],
        'rain_rate' =>[
            'class'=> RainRate::class,
            'calc' => 'max'],
        'wind' =>[
            'class'=> Wind::class,
            'calc' => 'wind'],
        'gust_speed' =>[
            'class'=> WindGust::class,
            'calc' => 'max'],
        'sky_brightness' =>[
            'class'=> SkyBrightness::class,
            'calc' => 'avg'],
        'sky_temperature' =>[
            'class'=> SkyTemperature::class,
            'calc' => 'avg'],
        'sqm' =>[
            'class'=> SkyQuality::class,
            'calc' => 'avg'],
        'cloud_cover' =>[
            'class'=> CloudCover::class,
            'calc' => 'max'],
        ];


    public function createReport(){
        /* check the end interval time  now roundend to the fifth minutes */
        $this->endMainInterval = $this->getEndOfMainInterval();
        $this->startMainInterval = $this->getOldestMeasureTime();
        if($this->startMainInterval >= $this->endMainInterval ){
            Log::channel('weather_short_report')->info("nothing to report");
            return;
        }

        $this->currentInterval = $this->startMainInterval->copy();

        $this->endLoopTime = $this->endMainInterval->copy()->subMinutes(5);

        while ($this->currentInterval <= $this->endLoopTime) {
            $this->endInterval = $this->currentInterval->copy()->addMinutes(5);

            if($this->endInterval > now()){
                Log::channel('weather_short_report')->error("long sad story");
                return;
            }

            $localeStart = $this->currentInterval->copy()->setTimezone('Europe/Rome');
            $localeEnd = $this->endInterval->copy()->setTimezone('Europe/Rome');

            Log::channel('weather_short_report')->info("Handling data from: ". $localeStart->format('Y-m-d H:i') ." to: ". $localeEnd->format('Y-m-d H:i') );

            $interval = $this->computeData();


            try {
                $interval->save();
                $this->setSyncedMeasure();
            } catch (\Throwable $th) {
                Log::channel('weather_short_report')->emergency("errore di storicizzazione");
                Log::channel('weather_short_report')->emergency("provo ad eliminarlo");
                $find = ShortTimeReport::where('interval',$this->endInterval)->delete();
                if($find){

                    try {
                        Log::channel('weather_short_report')->emergency("Lo risalvo");
                        $interval->save();
                        Log::channel('weather_short_report')->emergency("salvato");
                        $this->setSyncedMeasure();
                    } catch (\Throwable $th) {
                        Log::channel('weather_short_report')->emergency("non c'è verso!");
                        Log::channel('weather_short_report')->emergency($th);
                    }

                }


            }
            // Aggiungi 5 minuti all'intervallo corrente
            $this->currentInterval->addMinutes(5);
        }

        try {
            Artisan::call('report:sendreport');
            Log::channel('weather_short_report')->emergency("dati inviati");
        } catch (\Throwable $th) {
            Log::channel('weather_short_report')->emergency("impossibile inviare dati al server {$th}");
        }

    }
    private function getEndOfMainInterval(){
        $now=now();
        $minutes = intval($now->format('i'));
        $roundedMinutes = floor($minutes / 5) * 5;
        return $now->copy()->minute($roundedMinutes)->second(0);

    }

    private function getOldestMeasureTime():?Carbon{
        $array =[];
        $selected = null;

        //scroll al the reportable measure
        foreach(self::MODELS as $key => $model){
            $array[$key] = $model['class']::where('sync',false)
                                ->where('ack_time','<',$this->endMainInterval)
                                ->orderBy('ack_time','asc')
                                ->pluck('ack_time')
                                ->first();
        }

        foreach($array as $datetime){
            if($selected == null){
                $selected = $datetime;
            } elseif(!is_null($datetime) && ($datetime < $selected)){
                    $selected = $datetime;
                }
            }

        if($selected == null){
            return now();
        }

        $minutes = intval($selected->format('i'));
        $roundedMinutes = floor($minutes / 5) * 5;
        $oldest = $selected->copy()->minute($roundedMinutes)->second(0);

        return $oldest;
    }

    private function computeData():ShortTimeReport{

        $interval = new ShortTimeReport([
            'interval' => $this->endInterval,
            'sync' => SyncStatusEnum::INCOMPLETE
        ]);
        foreach(self::MODELS as $key => $model){
            $avg = null;
            $max= null;

            switch ($model['calc']){
                case 'avg':
                    $avg = $model['class']::where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->avg('value');
                    if(isset($avg)){
                        $interval->$key = round($avg,2);
                    }
                    break;

                case 'max':
                    $max = $model['class']::where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->max('value');
                    if(isset($max)){
                        $interval->$key = round($max,2);
                    }
                    break;

                case 'wind':
                    $winds = Wind::where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->get();

                    if(count($winds) == 0){
                        break;
                    }

                    $avg = $this->calcAvgWind($winds);

                    $interval->wind_speed = round($avg['speed'],2);
                    $interval->wind_dir = round($avg['direction'],2);
                    break;
            }
        }

        return $interval;
    }

    private function calcAvgWind($windsData){
        $wind = [
            'speed' => null,
            'direction' => null
        ];

        $sumX = 0;
        $sumY = 0;
        $count = count($windsData);

        if($count == 0){
            return null;
        }

        foreach($windsData as $reading){
                $value = $reading['value'];
                $direction = $reading['direction'];

                // Converti la direzione da gradi a radianti
                $radians = deg2rad($direction);

                // Calcola le componenti x e y
                $vx = $value * cos($radians);
                $vy = $value * sin($radians);

                // Somma le componenti
                $sumX += $vx;
                $sumY += $vy;
        }

        // Calcola la media delle componenti x e y
        $mean_vx = $sumX / $count;
        $mean_vy = $sumY / $count;

        // Calcola la velocità media
        $mean_speed = sqrt(($mean_vx ** 2) + ($mean_vy ** 2));

        // Calcola la direzione media
        $mean_direction = rad2deg(atan2($mean_vy, $mean_vx));

        // Assicurati che la direzione sia positiva
        if ($mean_direction < 0) {
            $mean_direction += 360;
        }

        return $wind = [
                'speed' => round($mean_speed,2),
                'direction' => intval($mean_direction)
            ];

    }


    private function setSyncedMeasure(){

        foreach(self::MODELS as $key => $model){
            $array[$key] = $model['class']::where('sync',0)
                                        ->where('ack_time', '>=', $this->currentInterval)
                                        ->where('ack_time', '<=', $this->endInterval)
                                        ->update(['sync' => true]);
        }

    }

}
