@php
    use App\Filament\Pages\MarketingStudio;
    use App\Filament\Pages\StoreReports;
    use App\Filament\Resources\OrderResource;
    use App\Filament\Resources\ProductResource;
    use App\Filament\Resources\ReviewResource;
    use App\Services\AdminReporting as Report;

    $metrics = [
        ['key' => 'sales', 'label' => 'المبيعات المحققة', 'icon' => 'heroicon-o-banknotes', 'money' => true],
        ['key' => 'orders', 'label' => 'الطلبات الواردة', 'icon' => 'heroicon-o-shopping-bag'],
        ['key' => 'sales_count', 'label' => 'الطلبات المحققة', 'icon' => 'heroicon-o-check-badge'],
        ['key' => 'average', 'label' => 'متوسط الطلب المحقق', 'icon' => 'heroicon-o-calculator', 'money' => true],
    ];
    $maxSales = max(array_column($trend, 'value'));
    $orderIndex = OrderResource::getUrl();
@endphp

<x-filament-panels::page class="orq-workspace">
    <x-admin.report-hero title="نظرة عامة" description="تابع مبيعات ORQ، واعرف الطلبات والمنتجات اللي محتاجة متابعة.">
        <a class="orq-button orq-button-gold" href="{{ StoreReports::getUrl(parameters: $period->query()) }}">
            @svg('heroicon-o-chart-bar-square') تقارير الأداء
        </a>
        <a class="orq-button orq-button-ghost" href="{{ MarketingStudio::getUrl() }}">أدوات التسويق</a>
    </x-admin.report-hero>

    <x-admin.period :period="$period" :action="url('/admin')" />

    <section aria-label="مؤشرات الفترة" class="orq-metrics">
        @foreach ($metrics as $metric)
            @php
                $key = $metric['key'];
                $value = $current[$key];
                $change = $previous[$key] > 0 && $value !== null ? round(($value - $previous[$key]) / $previous[$key] * 100) : null;
                $precision = $key === 'average' || fmod((float) $value, 1) !== 0.0 ? 2 : 0;
            @endphp
            <article class="orq-metric {{ $loop->first ? 'orq-metric-featured' : '' }}">
                <div class="orq-metric-top">
                    <h2>{{ $metric['label'] }}</h2>
                    <span class="orq-icon">@svg($metric['icon'])</span>
                </div>
                <strong><bdi>{{ $value === null ? '—' : (($metric['money'] ?? false) ? Report::money($value, $precision) : number_format($value)) }}</bdi></strong>
                <div class="orq-metric-footer">
                    @if ($change !== null)
                        <span class="orq-delta" data-direction="{{ $change <=> 0 }}"><bdi>{{ $change > 0 ? '+' : '' }}{{ $change }}%</bdi></span>
                        <span>مقارنة بالفترة السابقة</span>
                    @else
                        <span>{{ $value === null ? 'لا توجد طلبات محققة' : 'لا توجد قيمة سابقة للمقارنة' }}</span>
                    @endif
                </div>
            </article>
        @endforeach
    </section>

    <div class="orq-dashboard-grid">
        <section class="orq-card orq-trend">
            <div class="orq-card-head">
                <div><h2>اتجاه المبيعات</h2><p>طلب مدفوع أو دفع عند الاستلام تم تسليمه، باستثناء الملغى والمرتجع والمسترد. الأرقام حسب تاريخ إنشاء الطلب.</p></div>
                <span class="orq-chart-key">ج.م</span>
            </div>
            @if ($maxSales > 0)
                <div class="orq-chart" tabindex="0" role="region" aria-label="مخطط المبيعات المحققة">
                    <ol class="orq-chart-plot" style="--bars: {{ count($trend) }}">
                        @foreach ($trend as $point)
                            @php
                                $dateLabel = $point['from'] === $point['to'] ? $point['from'] : $point['from'].' – '.$point['to'];
                                $showTick = count($trend) < 10 || $loop->first || $loop->last || $loop->index % ceil(count($trend) / 6) === 0;
                            @endphp
                            <li>
                                <button type="button" class="orq-chart-column" aria-label="{{ $dateLabel.' · '.Report::money($point['value'], 2) }}"
                                    style="--height: {{ round($point['value'] / $maxSales * 100, 2) }}%">
                                    <span class="orq-chart-bar"></span>
                                    <span aria-hidden="true" class="orq-chart-tip"><bdi>{{ $dateLabel }}</bdi><bdi>{{ Report::money($point['value'], 2) }}</bdi></span>
                                </button>
                                <bdi class="orq-chart-tick" aria-hidden="true">{{ $showTick ? $point['from'] : '' }}</bdi>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @else
                <div class="orq-chart-empty">
                    @svg('heroicon-o-chart-bar')
                    <p>مفيش مبيعات محققة في الفترة المختارة.</p>
                </div>
            @endif
        </section>

        <section class="orq-card orq-operations">
            <div class="orq-card-head"><div><h2>متابعة التشغيل</h2><p>الحالة الحالية لكل الفترات</p></div></div>
            <div class="orq-action-list">
                @foreach ([
                    ['label' => 'دفع عند الاستلام بانتظار التأكيد', 'value' => $operations['pending'], 'icon' => 'heroicon-o-clock', 'url' => OrderResource::getUrl(parameters: ['filters' => ['status' => ['values' => ['pending']], 'payment_method' => ['value' => 'cod']]])],
                    ['label' => 'طلبات للتجهيز والشحن', 'value' => $operations['shipping'], 'icon' => 'heroicon-o-truck', 'url' => OrderResource::getUrl(parameters: ['filters' => ['status' => ['values' => ['confirmed', 'processing']]]])],
                    ['label' => 'أحجام مخزونها 5 قطع أو أقل', 'value' => $operations['stock'], 'icon' => 'heroicon-o-cube', 'url' => ProductResource::getUrl(parameters: ['filters' => ['low_stock' => ['isActive' => true]]])],
                    ['label' => 'تقييمات بانتظار المراجعة', 'value' => $operations['reviews'], 'icon' => 'heroicon-o-star', 'url' => ReviewResource::getUrl(parameters: ['filters' => ['status' => ['value' => 'pending']]])],
                ] as $item)
                    <a href="{{ $item['url'] }}" class="orq-action-row">
                        <span class="orq-icon">@svg($item['icon'])</span>
                        <span>{{ $item['label'] }}</span>
                        <strong class="orq-count">{{ number_format($item['value']) }}</strong>
                        @svg('heroicon-o-chevron-left', 'orq-arrow')
                    </a>
                @endforeach
            </div>
        </section>
    </div>

    <div class="orq-dashboard-grid">
        <section class="orq-card">
            <div class="orq-card-head"><div><h2>أحدث الطلبات</h2><p>خلال الفترة المختارة</p></div><a class="orq-text-link" href="{{ $orderIndex }}">كل الطلبات @svg('heroicon-o-arrow-left')</a></div>
            @if ($latest->isEmpty())
                <p class="orq-empty">مفيش طلبات في الفترة المختارة.</p>
            @else
                <div class="orq-table-wrap" tabindex="0" role="region" aria-label="أحدث الطلبات">
                    <table class="orq-table orq-latest-table" role="table">
                        <thead role="rowgroup"><tr role="row"><th scope="col">الطلب</th><th scope="col">العميل</th><th scope="col">الحالة</th><th scope="col">الإجمالي</th><th scope="col">التاريخ</th></tr></thead>
                        <tbody role="rowgroup">
                            @foreach ($latest as $order)
                                <tr role="row">
                                    <td role="cell"><a class="orq-text-link" href="{{ OrderResource::getUrl('view', ['record' => $order]) }}"><bdi>{{ $order->order_number }}</bdi></a></td>
                                    <td role="cell">{{ $order->customer_display_name === 'N/A' ? 'غير مسجل' : $order->customer_display_name }}</td>
                                    <td role="cell"><span class="orq-status" data-status="{{ $order->status }}">{{ __('admin.order_status.'.$order->status) }}</span></td>
                                    <td role="cell"><bdi>{{ Report::money($order->total, 2) }}</bdi></td>
                                    <td role="cell"><bdi>{{ $order->created_at->setTimezone('Africa/Cairo')->format('d/m H:i') }}</bdi></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="orq-card">
            <div class="orq-card-head"><div><h2>مخزون يحتاج متابعة</h2><p>الأقل توفراً من الأحجام المعروضة للبيع</p></div><a class="orq-text-link" href="{{ ProductResource::getUrl() }}">المنتجات @svg('heroicon-o-arrow-left')</a></div>
            @forelse ($operations['lowStock'] as $variant)
                <a class="orq-stock-row" href="{{ ProductResource::getUrl('edit', ['record' => $variant->product_id]) }}">
                    <span>{{ $variant->product->name_ar ?: $variant->product->name }} <small>{{ (int) $variant->size }} مل</small></span>
                    <span class="orq-status" data-status="{{ $variant->stock_quantity > 0 ? 'pending' : 'cancelled' }}">{{ $variant->stock_quantity > 0 ? $variant->stock_quantity.' متبقي' : 'نفد' }}</span>
                </a>
            @empty
                <p class="orq-empty">كل الأحجام المتاحة للبيع مخزونها أعلى من حد التنبيه.</p>
            @endforelse
        </section>
    </div>
</x-filament-panels::page>
