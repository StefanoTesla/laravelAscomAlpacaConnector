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

    public function createReport(){
        /* check the end interval time  now roundend to the fifth minutes */
        $this->endMainInterval = $this->getEndOfMainInterval();
        $this->startMainInterval = $this->getOldestMeasureTime();
        Log::channel('weather_short_report')->info("-- NEW SHORT REPORT STARTED --");
        if($this->startMainInterval >= $this->endMainInterval ){
            Log::channel('weather_short_report')->info("nothing to report");
            return;
        }

        $this->currentInterval = $this->startMainInterval->copy();


        while ($this->currentInterval < $this->endMainInterval) {
            $this->endInterval = $this->currentInterval->copy()->addMinutes(5);

            if($this->endInterval > now()){
                Log::info("can't handle the future");
                return;
            }

            $localeStart = $this->currentInterval->copy()->setTimezone('Europe/Rome');
            $localeEnd = $this->endInterval->copy()->setTimezone('Europe/Rome');

            Log::channel('weather_short_report')->info("Handling data from: ". $localeStart->format('Y-m-d H:i') ." to: ". $localeEnd->format('Y-m-d H:i') );
            $interval = new ShortTimeReport([
                'interval' => $this->endInterval,
                'sync' => false
            ]);

            $t = $this->getAvgTemperature();
            if(isset($t)){
                $interval->temperature = round($t,2);
            }
            $d = $this->getAvgDewPoint();
            if(isset($d)){
                $interval->dew_point = round($d,2);
            }
            $h = $this->getAvgHumidity();
            if(isset($h)){
                $interval->humidity = intval($h);
            }
            $p = $this->getAvgPressure();
            if(isset($p)){
                $interval->pressure = round($p,2);
            }
            $r = $this->getMaxRainRate();
            if(isset($r)){
                $interval->rain_rate = round($r,2);
            }
            $g = $this->getMaxGustSpeed();
            if(isset($g)){
                $interval->gust_speed = round($g,2);
            }
            $w = $this->getAvgWind();

            if(isset($w['speed'])){
                $interval->wind_speed = round($w['speed'],2);
            }
            if(isset($w['direction'])){
                $interval->wind_dir = round($w['direction'],2);
            }

            try {
                $interval->save();
                $this->setSyncedMeasure();
            } catch (\Throwable $th) {
                Log::channel('weather_short_report')->emergency("errore di storicizzazione");
                Log::channel('weather_short_report')->emergency("provo ad eliminarlo");
                $find = ShortTimeReport::where('interval',$this->endInterval)->delete();
                if($find){

                    Log::channel('weather_short_report')->emergency("Lo risalvo");
                    try {
                        $interval->save();
                        $this->setSyncedMeasure();
                    } catch (\Throwable $th) {
                        Log::channel('weather_short_report')->emergency("non c'è verso!");
                    }

                }

                Log::channel('weather_short_report')->emergency($th);
            }

            // Aggiungi 5 minuti all'intervallo corrente
            $this->currentInterval->addMinutes(5);
            Log::channel('weather_short_report')->info("Short Report created");
        }
        Log::channel('weather_short_report')->info("-- SHORT REPORT FINISHED --");
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

        $array[] = Temperature::where('sync','=',false)
            ->where('ack_time','<',$this->endMainInterval)
            ->orderBy('ack_time','asc')
            ->pluck('ack_time')
            ->first();
        $array[] = DewPoint::where('sync','=',false)
            ->where('ack_time','<',$this->endMainInterval)
            ->orderBy('ack_time','asc')
            ->pluck('ack_time')
            ->first();
        $array[] = Humidity::where('sync','=',false)
            ->where('ack_time','<',$this->endMainInterval)
            ->orderBy('ack_time','asc')
            ->pluck('ack_time')
            ->first();
        $array[] = Pressure::where('sync','=',false)
            ->where('ack_time','<',$this->endMainInterval)
            ->orderBy('ack_time','asc')
            ->pluck('ack_time')
            ->first();
        $array[] = RainRate::where('sync','=',false)
            ->where('ack_time','<',$this->endMainInterval)
            ->orderBy('ack_time','asc')
            ->pluck('ack_time')
            ->first();
        $array[] = WindGust::where('sync','=',false)
            ->where('ack_time','<',$this->endMainInterval)
            ->orderBy('ack_time','asc')
            ->pluck('ack_time')
            ->first();

        foreach($array as $datatime){
            if($selected == null){
                $selected = $datatime;
            } elseif(!is_null($datatime) && ($datatime < $selected)){
                    $selected = $datatime;
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
    private function getAvgTemperature(){
        return Temperature::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->avg('value');
    }
    private function getAvgDewPoint(){
        return DewPoint::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->avg('value');

    }
    private function getAvgHumidity(){
        return Humidity::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->avg('value');
    }
    private function getAvgPressure(){
        return Pressure::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->avg('value');
    }
    private function getMaxGustSpeed(){
        return WindGust::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->max('value');
    }
    private function getMaxRainRate(){
        return RainRate::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->max('value');
    }
    private function getAvgWind(){
        $temp = Wind::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->get();
        $wind = [
            'speed' => null,
            'direction' => null
        ];
        if(is_null($temp)){
            return $wind;
        }

        $sumX = 0;
        $sumY = 0;
        $count = count($temp);

        if($count == 0){
            return null;
        }

        foreach($temp as $reading){
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
        Temperature::where('sync', '=', false)
           ->where('ack_time', '>=', $this->currentInterval)
           ->where('ack_time', '<=', $this->endInterval)
           ->update(['sync' => true]);
        DewPoint::where('sync', '=', false)
           ->where('ack_time', '>=', $this->currentInterval)
           ->where('ack_time', '<=', $this->endInterval)
           ->update(['sync' => true]);
        Humidity::where('sync', '=', false)
           ->where('ack_time', '>=', $this->currentInterval)
           ->where('ack_time', '<=', $this->endInterval)
           ->update(['sync' => true]);
        Pressure::where('sync', '=', false)
           ->where('ack_time', '>=', $this->currentInterval)
           ->where('ack_time', '<=', $this->endInterval)
           ->update(['sync' => true]);
        WindGust::where('sync', '=', false)
           ->where('ack_time', '>=', $this->currentInterval)
           ->where('ack_time', '<=', $this->endInterval)
           ->update(['sync' => true]);
        Wind::where('sync', '=', false)
           ->where('ack_time', '>=', $this->currentInterval)
           ->where('ack_time', '<=', $this->endInterval)
           ->update(['sync' => true]);
        RainRate::where('sync', '=', false)
           ->where('ack_time', '>=', $this->currentInterval)
           ->where('ack_time', '<=', $this->endInterval)
           ->update(['sync' => true]);

    }

}
