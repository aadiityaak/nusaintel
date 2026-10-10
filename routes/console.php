<?php

use App\Jobs\SearchKlatenSources;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SearchKlatenSources)
    ->dailyAt('02:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
