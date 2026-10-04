<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
| Auto-publish artikel edukasi yang sudah waktunya tayang.
| Jalanin di dev: php artisan schedule:work
| Di production: setup cron * * * * * cd /path && php artisan schedule:run
*/

Schedule::command('edukasi:publish-scheduled')->everyMinute();