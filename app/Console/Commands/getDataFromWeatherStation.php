<?php

namespace App\Console\Commands;

use App\Models\WeatherStation;
use Exception;
use Illuminate\Console\Command;
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
        $data = null;
        Log::channel('weather_station')->info("--- Wheather Station Data Reading Started ---");
        try{
            $response =  Http::get(config('tesla.wheatherStationURL'));
        }
        catch (Exception $e) {
            Log::channel('weather_station')->alert("unable to catch data from weather station");
            Log::channel('weather_station')->alert($e);
            $this->fail('Something went wrong during data catching.');
        }


        if ($response->successful()) {
            $data = $response->json();
        } else {
            Log::channel('weather_station')->alert("unable to get data, sesponse was not successful");
            Log::channel('weather_station')->alert($response->status());
            $this->fail('Wrong HTTP response.');
        }

        $validation = [];
            
        //normal data
        foreach($data['common_list'] as $id){
            switch ($id['id']){
                case "0x02":
                    $validation['temperature'] = floatval($id["val"]);
                    break;
                case "0x03":
                    $validation['dewpoint'] = round(floatval($id["val"]), 2);
                    break;
                case "0x07":
                    $validation['humidity'] = intval(str_replace("%","",$id["val"]));
                    break;
                case "0x0B":
                    $validation['windspeed'] = round( floatval(str_replace(" km/h","",$id["val"]))  * 0.27777777777778,2);
                    break;
                case "0x0C":
                    $validation['windgust'] = round(floatval(str_replace(" km/h","",$id["val"])) *  0.27777777777778,2);
                    break;
                case "0x0A":
                    $validation['winddir'] = intval($id["val"]);
                    break; 
            }
        }
        //rain
        foreach($data['piezoRain'] as $id){
            switch ($id['id']){
                case "0x0E":
                    $validation['rainrate'] = round(floatval(str_replace(" mm/Hr","",$id["val"])),2);
                    break;
            }
        }

        //pressure
        $p = str_replace(" hPa","",$data["wh25"][0]["abs"]);
        $validation['pressure'] = round(floatval($p),2);


        $validator = Validator::make($validation,
            [
                'temperature' => 'required|numeric',
                'dewpoint' => 'required|numeric',
                'humidity' => 'required|numeric',
                'windspeed' => 'required|numeric',
                'windgust' => 'required|numeric',
                'winddir' => 'required|numeric',
                'rainrate' => 'required|numeric',
                'pressure' => 'required|numeric',
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

        //find the weather station
        $weatherStation = WeatherStation::findOrFail(1);

        //stor data
        $weatherStation->temperature = $validated['temperature'];
        $weatherStation->dewpoint = $validated['dewpoint'];
        $weatherStation->humidity = $validated['humidity'];
        $weatherStation->windspeed = $validated['windspeed'];
        $weatherStation->windgust = $validated['windgust'];
        $weatherStation->winddir = $validated['winddir'];
        $weatherStation->rainrate = $validated['rainrate'];
        $weatherStation->pressure = $validated['pressure'];
        $weatherStation->updated_at = now();
        $weatherStation->save();

        Log::channel('weather_station')->info("--- Wheather Station Data Reading Finished ---");
    }     
        

}

