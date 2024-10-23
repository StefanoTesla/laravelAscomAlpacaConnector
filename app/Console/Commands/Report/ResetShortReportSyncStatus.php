<?php

namespace App\Console\Commands\Report;

use App\Models\WeatherData\Report\ShortTimeReport;
use Illuminate\Console\Command;

class ResetShortReportSyncStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:resetsyncstatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ShortTimeReport::query()->update(['sync' => 0]);
    }
}
