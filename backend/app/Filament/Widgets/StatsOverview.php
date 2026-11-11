<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $month = Carbon::now()->startOfMonth();

        return [
            Stat::make('Today Orders', Order::whereDate('created_at', $today)->count())
                ->description('Orders received today')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),
            
            Stat::make('Monthly Revenue', '$' . number_format(Order::where('payment_status', 'paid')
                ->where('created_at', '>=', $month)
                ->sum('total'), 2))
                ->description('Total paid revenue this month')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
            
            Stat::make('Active Products', Product::where('is_active', true)->count())
                ->description('Products currently visible')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),

            Stat::make('Pending Reviews', Review::where('status', 'pending')->count())
                ->description('Reviews awaiting approval')
                ->descriptionIcon('heroicon-m-star')
                ->color(Review::where('status', 'pending')->count() > 0 ? 'warning' : 'gray'),
        ];
    }
}
