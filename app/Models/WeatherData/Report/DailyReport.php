<?php

namespace App\Models\WeatherData\Report;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    public $timestamps = false;

    protected $fillable = ['day','temperature','dew_point','pressure','humidity','rain_rate','gust_speed','wind_speed','wind_dir','sync'];
    protected $casts = [
        'day' => 'date',
    ];


   
}
