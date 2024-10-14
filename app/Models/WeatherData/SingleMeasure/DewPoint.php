<?php

namespace App\Models\WeatherData\SingleMeasure;

use Illuminate\Database\Eloquent\Model;

class DewPoint extends Model
{
    public $timestamps = false;
    protected $fillable = ['value','ack_time','sync'];
    protected $casts = [
        'ack_time' => 'datetime',
    ];
    static function getDescription():string{
        return "GW2000";
    }
}
