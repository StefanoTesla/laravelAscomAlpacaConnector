<?php

use Illuminate\Support\Facades\Schedule;



Schedule::command('getdata:fromweatherstation')->everyTenSeconds();