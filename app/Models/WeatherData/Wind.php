<?php

namespace App\Models\WeatherData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wind extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['value','direction','ack_time','sync'];
    protected $casts = [
        'ack_time' => 'datetime',
    ];

    static function getDescription():string{
        return "GW2000";
    }
}
