<?php

namespace App\Models\WeatherData\SingleMeasure;
use Illuminate\Database\Eloquent\Model;

class Wind extends Model
{
    public $timestamps = false;
    protected $fillable = ['value','direction','ack_time','sync'];
    protected $casts = [
        'ack_time' => 'datetime',
    ];

    static function getDescription():string{
        return "GW2000";
    }
}
