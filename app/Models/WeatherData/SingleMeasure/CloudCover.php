<?php

namespace App\Models\WeatherData\SingleMeasure;

use App\Enums\SyncStatusEnum;
use Illuminate\Database\Eloquent\Model;

class CloudCover extends Model
{
    public $timestamps = false;
    protected $fillable = ['value','ack_time','sync'];
    protected $casts = [
        'ack_time' => 'datetime',
        'sync' => SyncStatusEnum::class,
    ];
    
    static function getDescription():string{
        return "MySQM+";
    }
}
