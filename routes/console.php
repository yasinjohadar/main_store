<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled Commands for Backup System
Schedule::command('backup:run-scheduled')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('backup:cleanup-expired')
    ->daily()
    ->at('02:00')
    ->withoutOverlapping();

Schedule::job(new \App\Jobs\LowStockAlertJob())
    ->daily()
    ->at('08:00')
    ->withoutOverlapping();

Schedule::job(new \App\Jobs\SeoAuditJob())
    ->daily()
    ->at('03:00')
    ->withoutOverlapping();

// Zoom sync command removed
