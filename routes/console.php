<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule notification commands
Schedule::command('notifications:payment-reminders')
    ->daily()
    ->at('09:00')
    ->description('Send daily payment reminders');

Schedule::command('notifications:low-stock')
    ->daily()
    ->at('08:00')
    ->description('Check and send low stock alerts');

Schedule::command('notifications:upcoming-events')
    ->daily()
    ->at('08:00')
    ->description('Send upcoming event reminders (24 hours before)');
