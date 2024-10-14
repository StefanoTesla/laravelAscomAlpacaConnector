<?php

namespace App\Models\WeatherData;
use Illuminate\Database\Eloquent\Model;

class Humidity extends Model
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
