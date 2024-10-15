<?php

namespace App\Console\Commands;

use App\Models\WeatherData\Report\ShortTimeReport;
use App\Models\WeatherData\SingleMeasure\DewPoint;
use App\Models\WeatherData\SingleMeasure\Humidity;
use App\Models\WeatherData\SingleMeasure\Pressure;
use App\Models\WeatherData\SingleMeasure\RainRate;
use App\Models\WeatherData\SingleMeasure\Temperature;
use App\Models\WeatherData\SingleMeasure\Wind;
use App\Models\WeatherData\SingleMeasure\WindGust;
use Illuminate\Console\Command;

class ReportReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command will delete all the reports! and give to the software the possibilities to recreate all the report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('Sicuro di voler continuare? Questa azione eliminerà tutti i report già presenti e ne creerà di nuovi partendo dalle singole misure esistenti!')) {
            ShortTimeReport::truncate();

            Temperature::where('sync', '=', true)
               ->update(['sync' => false]);
            DewPoint::where('sync', '=', true)
               ->update(['sync' => false]);
            Humidity::where('sync', '=', true)
               ->update(['sync' => false]);
            Pressure::where('sync', '=', true)
               ->update(['sync' => false]);
            Wind::where('sync', '=', true)
               ->update(['sync' => false]);
            WindGust::where('sync', '=', true)
               ->update(['sync' => false]);
            RainRate::where('sync', '=', true)
               ->update(['sync' => false]);
        }

    }
}
