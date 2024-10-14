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

        if($this->startMainInterval >= $this->endMainInterval ){
            Log::info("nothing to report");
            return;
        }
        
        $this->currentInterval = $this->startMainInterval->copy(); 
    
    
        while ($this->currentInterval < $this->endMainInterval) {
    
            $this->endInterval = $this->currentInterval->copy()->addMinutes(5);
            
            $wind = $this->getAvgWind();

            if($wind){
                $windSpeed = 
                $windDir = $wind['direction'];
            }



            $interval = ShortTimeReport::create([
                'interval' => $this->endInterval,
                'temperature' => $this->getAvgTemperature(),
                'dew_point' => $this->getAvgDewPoint(),
                'humidity' => $this->getAvgHumidity(),
                'pressure' => $this->getAvgPressure(),
                'rain_rate' => $this->getMaxRainRate(),
                'gust_speed' => $this->getMaxGustSpeed(),
                'wind_speed'=> $wind['speed'],
                'wind_dir' =>$wind['direction'],
                'sync' => false
            ])->save();



            $this->setSyncedMeasure();
            
            // Aggiungi 5 minuti all'orario corrente
            $this->currentInterval->addMinutes(5);

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
        
        $array[] = Temperature::where('sync','=',false)
            ->orderBy('ack_time','asc')
            ->pluck('ack_time')
            ->first();
        $array[] = DewPoint::where('sync','=',false)
            ->orderBy('ack_time','asc')
            ->pluck('ack_time')
            ->first();
        $array[] = Humidity::where('sync','=',false)
            ->orderBy('ack_time','asc')
            ->pluck('ack_time')
            ->first();
        $array[] = Pressure::where('sync','=',false)
            ->orderBy('ack_time','asc')
            ->pluck('ack_time')
            ->first();
        $array[] = RainRate::where('sync','=',false)
            ->orderBy('ack_time','asc')
            ->pluck('ack_time')
            ->first();
        $array[] = WindGust::where('sync','=',false)
            ->orderBy('ack_time','asc')
            ->pluck('ack_time')
            ->first();

        foreach($array as $datatime){
            if(!is_null($datatime) && ($datatime < $selected)){
                    $selected = $datatime;
                }
            }
        if(is_null($selected)){
            return now();
        }

        $minutes = intval($selected->format('i')); 
        $roundedMinutes = floor($minutes / 5) * 5;
        $oldest = $selected->copy()->minute($roundedMinutes)->second(0);

        return $oldest;
    }

    private function getAvgTemperature(){
        $temp = Temperature::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->avg('value');
        if($temp){
            return round($temp, 2);
        }
        return null;
    }
    private function getAvgDewPoint(){
        $temp = DewPoint::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->avg('value');
        if($temp){
            return round($temp, 2);
        }
        return null;
    }
    private function getAvgHumidity(){
        $temp = Humidity::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->avg('value');
        if($temp){
            return intval($temp);
        }
        return null;
    }
    private function getAvgPressure(){
        $temp = Pressure::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->avg('value');
        if($temp){
            return round($temp, 2);
        }
        return null;
    }
    private function getMaxGustSpeed(){
        $temp = WindGust::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->max('value');
        if($temp){
            return round($temp, 2);
        }
        return null;
    }
    private function getMaxRainRate(){
        $temp = RainRate::where('sync','=',0)->where('ack_time','>=',$this->currentInterval)->where('ack_time','<',$this->endInterval)->max('value');
        if($temp){
            return round($temp, 2);
        }
        return null;
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
        
        $mean_speed = sqrt(($sumX ** 2) + ($sumY ** 2));

        // Calcola la direzione media
        $mean_direction = rad2deg(atan2($sumY, $sumX));
        
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