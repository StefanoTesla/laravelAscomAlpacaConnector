<?php

namespace App\Models\WeatherData\Report;

use Illuminate\Database\Eloquent\Model;

class ShortTimeReport extends Model
{
    public $timestamps = false;

    protected $fillable = ['interval','temperature','dew_point','pressure','humidity','rain_rate','gust_speed','wind_speed','wind_dir','sync'];
    protected $casts = [
        'interval' => 'datetime',
    ];
}
