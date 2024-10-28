<?php

use Illuminate\Support\Facades\Schedule;



Schedule::command('getdata:fromweatherstation')->everyTenSeconds();

Schedule::command('report:short')
                ->cron('1,6,11,16,21,26,31,36,41,46,51,56 * * * *')
                ->withoutOverlapping(10)
                ->runInBackground();

Schedule::command('alpaca:clean-client')
                ->everyThreeHours();
