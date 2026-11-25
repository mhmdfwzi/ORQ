<?php

namespace App\Filament\Pages;

use App\Services\AdminReporting;
use App\Support\ReportPeriod;
use Filament\Pages\Dashboard;

class Overview extends Dashboard
{
    protected static ?string $title = 'نظرة عامة';

    protected string $view = 'filament.pages.overview';

    public function getHeading(): ?string
    {
        return null;
    }

    protected function getViewData(): array
    {
        $period = ReportPeriod::fromFilters(request()->query());
        $reports = app(AdminReporting::class);

        return [
            'period' => $period,
            'current' => $reports->totals($period),
            'previous' => $reports->totals($period->previous()),
            'trend' => $reports->trend($period),
            'operations' => $reports->operations(),
            'latest' => $reports->latest($period),
        ];
    }
}
