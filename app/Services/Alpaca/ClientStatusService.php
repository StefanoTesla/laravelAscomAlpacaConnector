<?php

namespace App\Services\Alpaca;

use App\Models\AlpacaClient;
use Illuminate\Support\Carbon;

class ClientStatusService{

    static function connect(int $clientID,string $device){

        $client = AlpacaClient::where('client_id',$clientID)
                                ->where('device_type',$device)
                                ->first();

        if(is_null($client)){
            $client = new AlpacaClient();
            $client->client_id = $clientID;
            $client->connected = true;
            $client->device_type = $device;
            $client->save();
        } else {
            $client->connected = true;
            $client->updated_at = now();
            $client->save();
        }
    }

    static function disconnect(int $clientID,string $device){
        $client = AlpacaClient::where('client_id',$clientID)
                                ->where('device_type',$device)
                                ->first();
        if(!is_null($client)){
            $client->connected = false;
            $client->save();
        }
    }

    static function state(int $clientID,string $device){
        $client = AlpacaClient::where('client_id',$clientID)
                                ->where('device_type',$device)
                                ->first();
        if(!is_null($client)){
            return $client->connected;
        }

        return false;
    }


    static function list(){
        return AlpacaClient::all()->groupBy('device_type');
    }

    static function clean(){
        AlpacaClient::where('updated_at', '<', Carbon::now()->subDay())->delete();
    }


}