<?php

namespace App\Filament\Pages;

use App\Services\AdminReporting;
use App\Support\CampaignAttribution;
use App\Support\ReportPeriod;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class StoreReports extends Page
{
    protected static ?string $slug = 'reports';

    protected static ?string $title = 'تقارير الأداء';

    protected static ?int $navigationSort = -1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected string $view = 'filament.pages.store-reports';

    public function getHeading(): ?string
    {
        return null;
    }

    protected function getViewData(): array
    {
        $period = ReportPeriod::fromFilters(request()->query());
        $source = CampaignAttribution::identifier(request()->query('source')) ?? '';
        $reports = app(AdminReporting::class);
        $campaigns = $reports->campaigns($period);

        return [
            'period' => $period,
            'source' => $source,
            'sources' => $campaigns->pluck('utm_source')->unique()->sort()->values(),
            'campaigns' => $source === '' ? $campaigns : $campaigns->where('utm_source', $source),
            'products' => $reports->products($period),
            'regions' => $reports->regions($period),
            'totals' => $reports->totals($period),
            'statuses' => $reports->statuses($period),
            'payments' => $reports->payments($period),
            'funnel' => $reports->funnel($period),
        ];
    }
}
