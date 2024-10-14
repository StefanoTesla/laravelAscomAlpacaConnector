<?php

namespace App\Console\Commands;

use App\Services\Alpaca\ClientStatusService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanAlpacaClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alpaca:clean-client';

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
        $ret = ClientStatusService::clean();

        if($ret){
            Log::info("Alpaca Clients is clean");
        } 
    }
}
