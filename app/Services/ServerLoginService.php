<?php

namespace App\Services;

use App\Exceptions\LoginFailedException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServerLoginService{


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

    public static function getToken(){
        if(self::login()){
            return Crypt::decryptString(Cache::get('aut_token'));
        }
    }

}