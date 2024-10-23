<?php

namespace App\Services\WeatherData\Report;

use App\Exceptions\LoginFailedException;
use App\Http\Resources\ShortTimeReportResurces;
use App\Models\WeatherData\Report\ShortTimeReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class ReportSenderService{

    public function main(){

    if(!$this->login()){
        Log::info("Login failed");
        return;
    };
  
    $reportsToSync = ShortTimeReport::where('sync',0)
                        ->orderBy('interval','asc')
                        ->get();
    $chunkedRepors = $reportsToSync->chunk(100);
    foreach($chunkedRepors as $records) {
            $data = ShortTimeReportResurces::collection($records);
            $data = $data->toArray(request());

            $response = Http::accept('application/json')
            ->withToken(Crypt::decryptString(Cache::get('aut_token')))
            ->post('http://127.0.0.1:8000/api/weatherdata/short/store/',$data);

            $data = $response->json();

            Log::info($response->status());

            if($response->successful()){
                $this->updateSyncValue($data['valid_rows'],1);
                Log::info("Dati inviati correttamente");
            } else {

                if($response->status() == 422){
                    Log::info("Alcuni dati contengono errori");
                    if(!empty($data['valid_rows'])){
                        $this->updateSyncValue($data['valid_rows'],1);
                        Log::info("Aggiorno i dati validi");
                    }
                    if(!empty($data['invalid_rows'])){
                        Log::info("Aggiorno i dati con errori");
                        $intervals =[];
                        foreach($data['invalid_rows'] as $unvalid){
                            if(isset($unvalid['interval'])){
                                $intervals[] = $unvalid['interval'];
                                Log::error("Nella data :".$unvalid['interval']." ci sono errori.");
                            } else {
                                Log::error("Campo interval mancante.");
                            }
                            Log::error($unvalid['errors']);
                        }
                        $this->updateSyncValue($intervals,2);
                    }
                } else {
                    $status = $response->status();
                    Log::error("Remote server response {$status}, abort");
                    return;
                };
            }
            $intervals = [];

    };


    }


private function updateSyncValue(array $intervals,int $status){
    $formattedIntervals = array_map(function($interval) {
        // Converte l'intervallo usando Carbon e restituisce nel formato desiderato
        return Carbon::parse($interval)->format('Y-m-d H:i:s');
    }, $intervals);
    Log::info(ShortTimeReport::whereIn('interval', $formattedIntervals)->update(['sync' => $status]));
}


    private function login():bool{
        $login = 0;
        $retry = 0;
        while (true) {
            switch ($login) {
                case 0:
                    if (Cache::has('aut_token')){
                        $login = 20;
                    } else {
                        $login = 10;
                    }
                    break;
    
                case 10:
                    $retry +=1;
                    try{
                        $this->doLogin();
                        $login = 20;
                    } catch (\Throwable $th) {
                        Log::error($th);
                        return false;
                    }

                    if($retry > 2){
                        return false;
                    }
                    break;
    
                case 20:
                    $logged = $this->checkIfLogged();
                    if($logged){
                        return true;
                    } else {
                        $login = 10;
                    }
                    break;
    
                default:
                    Log::error("why I'm here?");
                    break;
            }
        }

        
    }

    private function checkIfLogged():bool{
        Log::info("Login check");
        try{
            $response =  Http::accept('application/json')
            ->withToken(Crypt::decryptString(Cache::get('aut_token')))
            ->get('http://127.0.0.1:8000/api/logged')
            ->json();
        } catch (\Throwable $th) {
            Log::info($th);
            return false;
        }

        if($response['message'] == "logged"){
            return true;
        }

        return false;
    }

    private function doLogin():bool{
        $response =  Http::accept('application/json')
            ->post('http://127.0.0.1:8000/api/login',[
                'email' => env("REMOTE_USER"),
                'password' => env("REMOTE_PSW"),
            ]);

        if($response->successful()){
            $token = $response->json();

            if(isset($token['access_token'])){
                Cache::put('aut_token', Crypt::encryptString($token['access_token']), now()->addDays(5));
                return true;
            } else {
                throw new LoginFailedException();
            }
        }
    return false;
    }

}