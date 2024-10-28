<?php

namespace App\Console\Commands\Report;

use App\Services\WeatherData\Report\ReportSenderService;
use Illuminate\Console\Command;

class SendShortTimeReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:sendreport';

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
        $sender = New ReportSenderService();
        $sender->main();
    }
}
