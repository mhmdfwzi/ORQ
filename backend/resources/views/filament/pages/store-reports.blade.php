@php
    use App\Filament\Pages\MarketingStudio;
    use App\Filament\Pages\StoreReports;
    use App\Filament\Resources\CampaignSpendResource;
    use App\Services\AdminReporting as Report;

    $stages = ['visit' => 'زيارة المتجر', 'cart' => 'إضافة للسلة', 'checkout' => 'بدء إتمام الطلب', 'sale' => 'طلب محقق'];
    $conversion = Report::ratio($funnel['sale'], $funnel['visit']);
    $coverage = Report::ratio($totals['attributed'], $totals['sales_count']);
    $lostRate = Report::ratio($totals['lost'], $totals['orders']);
    $percentage = fn (?float $value): string => $value === null ? '—' : number_format($value * 100, 1).'%';
    $reportUrl = StoreReports::getUrl();
@endphp

<x-filament-panels::page class="orq-workspace">
    <x-admin.report-hero title="تقارير الأداء" description="اعرف إيه اللي بيتباع، ومنين بتيجي الطلبات، وأداء كل حملة بعد احتساب الطلبات المحققة.">
        <a class="orq-button orq-button-gold" href="{{ route('admin.reports.campaigns', $period->query() + ($source !== '' ? ['source' => $source] : [])) }}">
            @svg('heroicon-o-arrow-down-tray') تصدير الحملات CSV
        </a>
        <a class="orq-button orq-button-ghost" href="{{ MarketingStudio::getUrl() }}">أدوات التسويق</a>
    </x-admin.report-hero>

    <x-admin.period :period="$period" :action="$reportUrl" :source="$source" />

    <section class="orq-metrics orq-report-metrics" aria-label="مؤشرات التقارير">
        @foreach ([
            ['تحويل الزيارة إلى طلب محقق', $percentage($conversion), 'من جلسات الزيارة المسجّلة', 'heroicon-o-funnel'],
            ['الطلبات المرتبطة بحملة', $percentage($coverage), $totals['attributed'].' من '.$totals['sales_count'].' طلباً محققاً', 'heroicon-o-link'],
            ['الإلغاء والمرتجعات', $percentage($lostRate), $totals['lost'].' من '.$totals['orders'].' طلباً وارداً', 'heroicon-o-arrow-uturn-left'],
        ] as [$label, $value, $note, $icon])
            <article class="orq-metric">
                <div class="orq-metric-top"><h2>{{ $label }}</h2><span class="orq-icon">@svg($icon)</span></div>
                <strong><bdi>{{ $value }}</bdi></strong>
                <div class="orq-metric-footer">{{ $note }}</div>
            </article>
        @endforeach
    </section>

    <section class="orq-card" id="campaigns">
        <div class="orq-card-head orq-card-head-wrap">
            <div><h2>أداء الحملات الإعلانية</h2><p>آخر رابط حملة موسوم في نفس المتصفح خلال 30 يوماً. يطبق فلتر المصدر على جدول الحملات وتصديره.</p></div>
            <div class="orq-inline-actions">
                <form method="get" action="{{ $reportUrl }}#campaigns" class="orq-inline-actions">
                    @foreach ($period->query() as $key => $date)
                        <input type="hidden" name="{{ $key }}" value="{{ $date }}">
                    @endforeach
                    <select class="orq-input" name="source" aria-label="مصدر الحملة">
                        <option value="">كل المصادر</option>
                        @foreach ($sources->push($source)->filter(fn ($value) => $value !== '')->unique() as $item)
                            <option value="{{ $item }}" @selected($source === $item)>{{ $item }}</option>
                        @endforeach
                    </select>
                    <button class="orq-button orq-button-light" type="submit">تصفية</button>
                </form>
                <a class="orq-button orq-button-light" href="{{ CampaignSpendResource::getUrl() }}">إدارة الإنفاق</a>
            </div>
        </div>
        @if ($campaigns->isEmpty())
            <div class="orq-empty orq-empty-action">
                <span>مفيش طلبات موسومة أو إنفاق مسجّل للفترة والمصدر المختارين.</span>
                <a href="{{ MarketingStudio::getUrl() }}" class="orq-text-link">اعمل رابط لأول حملة @svg('heroicon-o-arrow-left')</a>
            </div>
        @else
            <div class="orq-table-wrap" tabindex="0" role="region" aria-label="أداء الحملات الإعلانية">
                <table class="orq-table orq-campaign-table">
                    <thead><tr>
                        <th scope="col">الحملة / المصدر / الوسيط</th><th scope="col">الواردة</th><th scope="col">المحققة</th><th scope="col">ملغاة أو مرتجعة</th>
                        <th scope="col">المبيعات</th><th scope="col">الإنفاق المسجّل</th><th scope="col">العائد (ROAS)</th><th scope="col">تكلفة الطلب</th>
                    </tr></thead>
                    <tbody>
                        @foreach ($campaigns as $campaign)
                            @php
                                $roas = Report::ratio((float) $campaign->sales, (float) $campaign->spend);
                                $cpa = $campaign->spend !== null ? Report::ratio((float) $campaign->spend, (int) $campaign->sales_count) : null;
                            @endphp
                            <tr>
                                <th scope="row"><bdi>{{ $campaign->utm_campaign }}</bdi><small><bdi>{{ $campaign->utm_source }}</bdi> · <bdi>{{ $campaign->utm_medium }}</bdi></small></th>
                                <td>{{ number_format($campaign->orders_count) }}</td>
                                <td>{{ number_format($campaign->sales_count) }}</td>
                                <td>{{ number_format($campaign->lost) }}</td>
                                <td><bdi>{{ Report::money($campaign->sales, 2) }}</bdi></td>
                                <td><bdi>{{ $campaign->spend === null ? '—' : Report::money($campaign->spend, 2) }}</bdi>
                                    @if ($campaign->spend_days > 0)<small>{{ $campaign->spend_days }} يوم مسجّل</small>@endif
                                </td>
                                <td><bdi>{{ $roas === null ? '—' : number_format($roas, 2).'×' }}</bdi></td>
                                <td><bdi>{{ $cpa === null ? '—' : Report::money($cpa, 2) }}</bdi></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <p class="orq-note">العائد = مبيعات الحملة ÷ الإنفاق المسجّل. تكلفة الطلب = الإنفاق المسجّل ÷ الطلبات المحققة. راجع اكتمال تسجيل الإنفاق قبل المقارنة.</p>
    </section>

    <section class="orq-card" id="product-views">
        <div class="orq-card-head"><div><h2>مشاهدات المنتجات</h2><p>كل فتح لصفحة عطر يُحسب مرة لكل جلسة في اليوم بتوقيت القاهرة. الأرقام حسب الفترة المختارة.</p></div></div>
        <div class="orq-table-wrap" tabindex="0" role="region" aria-label="مشاهدات المنتجات">
            <table class="orq-table">
                <thead><tr><th scope="col">المنتج</th><th scope="col">المشاهدات</th></tr></thead>
                <tbody>
                    @forelse ($productViews as $product)
                        <tr>
                            <th scope="row"><bdi>{{ $product->name_ar ?: $product->name }}</bdi></th>
                            <td><bdi>{{ number_format($product->views) }}</bdi></td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="orq-empty">مفيش منتجات في الكتالوج.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="orq-report-grid">
        <section class="orq-card">
            <div class="orq-card-head"><div><h2>مسار الشراء</h2><p>جلسات المتجر اللي تم قياسها خلال الفترة</p></div></div>
            @if ($funnel['visit'] > 0)
                <ol class="orq-funnel">
                    @foreach ($stages as $key => $label)
                        <li>
                            <div><span>{{ $label }}</span><strong>{{ number_format($funnel[$key]) }} <small><bdi>{{ $percentage(Report::ratio($funnel[$key], $funnel['visit'])) }}</bdi></small></strong></div>
                            <div class="orq-progress" aria-hidden="true"><span style="--progress: {{ 100 * $funnel[$key] / $funnel['visit'] }}%"></span></div>
                        </li>
                    @endforeach
                </ol>
                <p class="orq-note">كل جلسة تتحسب مرة في كل مرحلة. الجلسة ممكن تدخل مرحلة بعد انتهاء جلسة سابقة. البيانات تتجمع من وقت تشغيل القياس.</p>
            @else
                <p class="orq-empty">مفيش جلسات مقاسة في الفترة المختارة.</p>
            @endif
        </section>

        <section class="orq-card">
            <div class="orq-card-head"><div><h2>حالات الطلبات وطرق الدفع</h2><p>كل الطلبات الواردة خلال الفترة</p></div></div>
            @if ($statuses->isEmpty())
                <p class="orq-empty">مفيش طلبات في الفترة المختارة.</p>
            @else
                <dl class="orq-breakdown">
                    @foreach (__('admin.order_status') as $key => $label)
                        <div><dt>{{ $label }}</dt><dd>{{ number_format($statuses[$key] ?? 0) }}</dd></div>
                    @endforeach
                </dl>
                <dl class="orq-payment-list">
                    @foreach ($payments as $payment)
                        <div><dt>{{ __('admin.payment_method.'.$payment->payment_method) }} <small>{{ $payment->orders_count }} طلب وارد</small></dt><dd><bdi>{{ Report::money($payment->sales, 2) }}</bdi></dd></div>
                    @endforeach
                </dl>
            @endif
        </section>

        <section class="orq-card">
            <div class="orq-card-head"><div><h2>أعلى المنتجات مبيعاً</h2><p>حسب عدد القطع من الطلبات المحققة. قيمة البنود قبل خصم الطلب والشحن.</p></div></div>
            <div class="orq-table-wrap" tabindex="0" role="region" aria-label="أعلى المنتجات مبيعاً">
                <table class="orq-table">
                    <thead><tr><th scope="col">المنتج</th><th scope="col">القطع</th><th scope="col">قيمة البنود</th></tr></thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr><th scope="row"><bdi>{{ $product->label }}</bdi></th><td>{{ number_format($product->units) }}</td><td><bdi>{{ Report::money($product->sales, 2) }}</bdi></td></tr>
                        @empty
                            <tr><td colspan="3" class="orq-empty">مفيش منتجات مباعة في الفترة المختارة.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="orq-card">
            <div class="orq-card-head"><div><h2>أعلى المحافظات مبيعاً</h2><p>حسب قيمة الطلبات المحققة وعنوان التوصيل</p></div></div>
            <div class="orq-table-wrap" tabindex="0" role="region" aria-label="أعلى المحافظات مبيعاً">
                <table class="orq-table">
                    <thead><tr><th scope="col">المحافظة</th><th scope="col">الطلبات المحققة</th><th scope="col">المبيعات</th></tr></thead>
                    <tbody>
                        @forelse ($regions as $region)
                            <tr><th scope="row">{{ $region->label }}</th><td>{{ number_format($region->orders_count) }}</td><td><bdi>{{ Report::money($region->sales, 2) }}</bdi></td></tr>
                        @empty
                            <tr><td colspan="3" class="orq-empty">مفيش مبيعات حسب المحافظة في الفترة المختارة.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-filament-panels::page>
