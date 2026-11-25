<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Support\CampaignAttribution;
use App\Support\ReportPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminReporting
{
    private const RECOGNIZED = "(orders.status NOT IN ('cancelled', 'returned')
        AND (orders.payment_status = 'paid'
             OR (orders.payment_method = 'cod' AND orders.status = 'delivered' AND orders.payment_status = 'pending')))";

    public static function money(float|int|string|null $amount, int $decimals = 0): string
    {
        return number_format((float) $amount, $decimals).' ج.م';
    }

    public static function ratio(float|int $numerator, float|int $denominator): ?float
    {
        return $denominator > 0 ? $numerator / $denominator : null;
    }

    private function orders(ReportPeriod $period): Builder
    {
        return Order::query()
            ->where('orders.created_at', '>=', $period->utcStart())
            ->where('orders.created_at', '<', $period->utcEnd());
    }

    public function performance(ReportPeriod $period): array
    {
        return [
            'campaigns' => $this->campaigns($period),
            'products' => $this->products($period),
            'regions' => $this->regions($period),
            'totals' => $this->totals($period),
            'statuses' => $this->statuses($period),
            'payments' => $this->payments($period),
            'funnel' => $this->funnel($period),
        ];
    }

    private function comparableText(string $column): string
    {
        // A legacy MySQL table and a new reporting table can have different collations.
        return DB::connection() instanceof MySqlConnection
            ? "CONVERT({$column} USING utf8mb4) COLLATE utf8mb4_unicode_ci"
            : $column;
    }

    public function totals(ReportPeriod $period): array
    {
        $recognized = self::RECOGNIZED;
        $row = $this->orders($period)->toBase()->selectRaw("
            COUNT(*) AS orders_count,
            SUM(CASE WHEN {$recognized} THEN 1 ELSE 0 END) AS sales_count,
            SUM(CASE WHEN {$recognized} THEN orders.total ELSE 0 END) AS sales,
            SUM(CASE WHEN orders.status IN ('cancelled', 'returned') THEN 1 ELSE 0 END) AS lost,
            SUM(CASE WHEN {$recognized} AND orders.utm_source IS NOT NULL THEN 1 ELSE 0 END) AS attributed
        ")->first();

        return [
            'orders' => (int) $row->orders_count,
            'sales_count' => (int) $row->sales_count,
            'sales' => (float) $row->sales,
            'average' => self::ratio((float) $row->sales, (int) $row->sales_count),
            'lost' => (int) $row->lost,
            'attributed' => (int) $row->attributed,
        ];
    }

    public function trend(ReportPeriod $period): array
    {
        $buckets = $period->chartBuckets();
        $recognized = self::RECOGNIZED;
        $query = $this->orders($period)->toBase()->whereRaw($recognized);

        foreach ($buckets as $index => $bucket) {
            $query->selectRaw("SUM(CASE WHEN orders.created_at >= ? AND orders.created_at < ?
                THEN orders.total ELSE 0 END) AS bucket_{$index}", [$bucket->utcStart(), $bucket->utcEnd()]);
        }

        $result = $query->first();
        $series = [];
        foreach ($buckets as $index => $bucket) {
            $series[] = [
                'from' => $bucket->from->format('d/m'),
                'to' => $bucket->through->format('d/m'),
                'value' => (float) $result->{"bucket_{$index}"},
            ];
        }

        return $series;
    }

    public function operations(): array
    {
        $stock = ProductVariant::forSale()->whereHas('product', fn (Builder $query) => $query->published())
            ->where('stock_quantity', '<=', 5);
        $pending = Order::query()->where('status', 'pending')->where('payment_method', 'cod');
        $shipping = Order::query()->whereIn('status', ['confirmed', 'processing']);

        return [
            'pending' => $pending->count(),
            'shipping' => $shipping->count(),
            'reviews' => Review::where('status', 'pending')->count(),
            'stock' => (clone $stock)->count(),
            'lowStock' => $stock->with('product:id,name,name_ar')->oldest('stock_quantity')->orderBy('id')->limit(5)->get(),
        ];
    }

    public function latest(ReportPeriod $period): Collection
    {
        return $this->orders($period)->with('user:id,name')->latest('created_at')->latest('id')->limit(5)->get();
    }

    public function products(ReportPeriod $period): Collection
    {
        $arabicName = $this->comparableText("MAX(NULLIF(products.name_ar, ''))");
        $productName = $this->comparableText('MAX(products.name)');
        $storedName = $this->comparableText('MAX(order_items.product_name)');

        return $this->orders($period)->toBase()->whereRaw(self::RECOGNIZED)
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->selectRaw("order_items.product_id, COALESCE({$arabicName}, {$productName}, {$storedName}) AS label")
            ->selectRaw('SUM(order_items.quantity) AS units, SUM(order_items.total_price) AS sales')
            ->groupBy('order_items.product_id')->orderByDesc('units')->orderByDesc('sales')->orderBy('order_items.product_id')->limit(10)->get();
    }

    public function regions(ReportPeriod $period): Collection
    {
        $shipping = $this->comparableText("NULLIF(orders.shipping_governorate, '')");
        $destination = $this->comparableText('destinations.name');
        $address = $this->comparableText("NULLIF(addresses.state, '')");
        $region = "COALESCE({$shipping}, {$destination}, {$address}, 'غير محددة')";

        return $this->orders($period)->toBase()->whereRaw(self::RECOGNIZED)
            ->leftJoin('addresses', 'addresses.id', '=', 'orders.address_id')
            ->leftJoin('destinations', 'destinations.id', '=', 'addresses.governorate_id')
            ->selectRaw("{$region} AS label, COUNT(*) AS orders_count, SUM(orders.total) AS sales")
            ->groupByRaw($region)->orderByDesc('sales')->orderBy('label')->limit(10)->get();
    }

    public function statuses(ReportPeriod $period): Collection
    {
        return $this->orders($period)->toBase()->selectRaw('status, COUNT(*) AS orders_count')
            ->groupBy('status')->pluck('orders_count', 'status');
    }

    public function payments(ReportPeriod $period): Collection
    {
        $recognized = self::RECOGNIZED;

        return $this->orders($period)->toBase()
            ->selectRaw("payment_method, COUNT(*) AS orders_count,
                SUM(CASE WHEN {$recognized} THEN orders.total ELSE 0 END) AS sales")
            ->groupBy('payment_method')->orderByDesc('orders_count')->get();
    }

    public function funnel(ReportPeriod $period): array
    {
        $dates = array_values($period->query());
        $events = DB::table('storefront_funnel_events')->whereBetween('day', $dates);
        $visits = (clone $events)->where('stage', 'visit')->select('session_id');
        $stages = (clone $events)->whereIn('session_id', $visits)
            ->selectRaw('stage, COUNT(DISTINCT session_id) AS sessions')->groupBy('stage')->pluck('sessions', 'stage');
        $sales = $this->orders($period)->whereRaw(self::RECOGNIZED)
            ->whereIn(DB::raw($this->comparableText('orders.analytics_session_id')), $visits)
            ->distinct()->count('analytics_session_id');

        return [
            'visit' => (int) ($stages['visit'] ?? 0),
            'cart' => (int) ($stages['cart'] ?? 0),
            'checkout' => (int) ($stages['checkout'] ?? 0),
            'sale' => $sales,
        ];
    }

    public function campaigns(ReportPeriod $period, string $source = ''): Collection
    {
        $fields = CampaignAttribution::FIELDS;
        $orders = $this->orders($period)->toBase()->whereNotNull('utm_source');
        $spend = DB::table('campaign_spends')->whereBetween('day', array_values($period->query()));
        if ($source !== '') {
            $sourceColumn = $this->comparableText('utm_source');
            $orders->whereRaw("{$sourceColumn} = ?", [$source]);
            $spend->whereRaw("{$sourceColumn} = ?", [$source]);
        }

        $comparable = array_map($this->comparableText(...), $fields);
        $selection = array_map(fn (string $expression, string $field) => DB::raw("{$expression} AS {$field}"), $comparable, $fields);
        $groupBy = implode(', ', $comparable);
        $recognized = self::RECOGNIZED;
        $orders = $orders->select($selection)->selectRaw("COUNT(*) AS orders_count,
            SUM(CASE WHEN {$recognized} THEN 1 ELSE 0 END) AS sales_count,
            SUM(CASE WHEN {$recognized} THEN orders.total ELSE 0 END) AS sales,
            SUM(CASE WHEN orders.status IN ('cancelled', 'returned') THEN 1 ELSE 0 END) AS lost,
            NULL AS spend, 0 AS spend_days")->groupByRaw($groupBy);
        $spend = $spend->select($selection)->selectRaw('0, 0, 0, 0, SUM(amount), COUNT(*)')->groupByRaw($groupBy);

        return DB::query()->fromSub($orders->unionAll($spend), 'campaigns')->select($fields)
            ->selectRaw('SUM(orders_count) AS orders_count, SUM(sales_count) AS sales_count, SUM(sales) AS sales,
                SUM(lost) AS lost, SUM(spend) AS spend, SUM(spend_days) AS spend_days')
            ->groupBy($fields)->orderByDesc('sales')->orderByDesc('orders_count')->orderByDesc('spend')
            ->orderBy('utm_source')->orderBy('utm_medium')->orderBy('utm_campaign')->get();
    }
}
