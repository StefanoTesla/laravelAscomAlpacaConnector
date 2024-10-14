<?php

namespace App\Console\Commands;


use App\Services\WeatherData\Report\ShortTimeReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ShortTimeReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:short';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Report of the last 5 minutes of data acknowledged';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel('weather_short_report')->info("Short report started at: ".now());
        $report = new ShortTimeReportService;
        $report->createReport();
        Log::channel('weather_short_report')->info("Short report finished");
    }
}
