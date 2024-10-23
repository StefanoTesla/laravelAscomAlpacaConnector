<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortTimeReportResurces extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'interval'     => $this->interval,
            'temperature'  => $this->temperature,
            'dew_point'    => $this->dew_point,
            'pressure'     => $this->pressure,
            'humidity'     => $this->humidity,
            'rain_rate'    => $this->rain_rate,
            'gust_speed'   => $this->gust_speed,
            'wind_speed'   => $this->wind_speed,
            'wind_dir'     => $this->wind_dir,
            // Nota: il campo 'sync' non è incluso nell'output
        ];
    }
}
