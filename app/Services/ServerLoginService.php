<?php

namespace App\Services;

use App\Exceptions\LoginFailedException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServerLoginService{


    private static function login():bool{
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
                        self::doLogin();
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
                    $logged = self::checkIfLogged();
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

    private static function checkIfLogged():bool{
        Log::info("Login check");
        try{
            $response =  Http::accept('application/json')
            ->withToken(Crypt::decryptString(Cache::get('aut_token')))
            ->get(env("REMOTE_DOMAIN").'/api/logged')
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

    private static function doLogin():bool{
        $response =  Http::accept('application/json')
            ->post(env("REMOTE_DOMAIN").'/api/login',[
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

        return null;
    }

}
