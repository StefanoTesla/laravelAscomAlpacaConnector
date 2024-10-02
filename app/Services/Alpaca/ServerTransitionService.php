<?php

namespace App\Services\Alpaca;

use Illuminate\Support\Facades\Cache;

class ServerTransitionService{

    static function get(){
        if (Cache::has('serverTransitionId')){
            $value = Cache::get('serverTransitionId');
            if($value >= 42949672934){
                Cache::put('serverTransitionId', 0);
            } else {
                Cache::increment('serverTransitionId');
            }
        } else {
            Cache::put('serverTransitionId', 0);
        }
        return Cache::get('serverTransitionId');
    }

    static function read(){
        return Cache::get('serverTransitionId');
    }

    static function destroy(){
        return Cache::forget('serverTransitionId');
    }

}