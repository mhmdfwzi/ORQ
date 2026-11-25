<?php

use App\Services\AdminReporting;
use App\Services\OnlineOrderService;
use App\Support\ReportPeriod;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('orq:check-admin-reports', function (AdminReporting $reports) {
    $reports->performance(ReportPeriod::fromFilters([]));
    $this->info('Admin reporting queries passed.');
})->purpose('Run the admin report queries against the configured database');

Schedule::call(fn () => app(OnlineOrderService::class)->releaseExpired())
    ->name('orq-release-expired-checkouts')
    ->everyFiveMinutes()
    ->withoutOverlapping(10);
