<?php

namespace App\Services\Alpaca;

use Illuminate\Support\Facades\Cache;

class ClientStatusService{

    static function connect($clientID){
        if(!Cache::has('alpacaClients')){
            Cache::put('alpacaClients', []);
        }
        $arr = Cache::get('alpacaClients');

        if(!empty($arr)){
            //cerca se già presente
            foreach($arr as $key => $client){
                if($arr[$key]['id'] == $clientID){
                    $arr[$key]['connected'] = true;
                    $arr[$key]['time'] = now();
                    Cache::put('alpacaClients',$arr);
                    return;
                }
            }
        }
        //array vuota o id non trovato
        $newClient = [
            'id' => $clientID,
            'connected' => true,
            'time' => now()
        ];
        array_push($arr,$newClient);
        Cache::put('alpacaClients',$arr);

    }

    static function disconnect($clientID){
        if(!Cache::has('alpacaClients')){
            Cache::put('alpacaClients', []);
        }
        $arr = Cache::get('alpacaClients');

        if(!empty($arr)){
            //cerca se già presente
            foreach($arr as $key => $client){
                if($arr[$key]['id'] == $clientID){
                    $arr[$key]['connected'] = false;
                    $arr[$key]['time'] = now();
                    Cache::put('alpacaClients',$arr);
                    return;
                }
            }
        }
    }

    static function list(){
        return Cache::get('alpacaClients');
    }

    static function destroy(){
        Cache::put('alpacaClients',[]);
    }


}