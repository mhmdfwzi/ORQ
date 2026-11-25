@php
    use App\Filament\Pages\StoreReports;
    use App\Filament\Resources\CampaignSpendResource;
    use App\Services\AdminReporting as Report;
@endphp

<x-filament-panels::page class="orq-workspace">
    <x-admin.report-hero title="أدوات التسويق" description="جهّز روابط حملاتك، احسب تكلفة الطلب المستهدفة، وسجّل الإنفاق عشان تتابع العائد.">
        <a class="orq-button orq-button-gold" href="{{ CampaignSpendResource::getUrl() }}">@svg('heroicon-o-banknotes') تسجيل الإنفاق</a>
        <a class="orq-button orq-button-ghost" href="{{ StoreReports::getUrl() }}#campaigns">أداء الحملات</a>
    </x-admin.report-hero>

    <div class="orq-report-grid orq-tools-grid">
        <section class="orq-card">
            <div class="orq-card-head"><div><h2>إنشاء رابط حملة</h2><p>استخدم نفس المصدر والوسيط واسم الحملة عند تسجيل الإنفاق.</p></div><span class="orq-icon">@svg('heroicon-o-link')</span></div>
            <form wire:submit="buildLink" class="orq-tool-form">
                <div class="orq-form-grid">
                    @foreach ([
                        ['destination', 'مسار صفحة الوصول', '/products/alpha'],
                        ['utm_source', 'المصدر', 'instagram'],
                        ['utm_medium', 'الوسيط', 'paid_social'],
                        ['utm_campaign', 'اسم الحملة', 'orq-winter'],
                    ] as [$field, $label, $placeholder])
                        <label class="orq-field {{ $loop->first || $loop->last ? 'orq-field-full' : '' }}">
                            <span>{{ $label }}</span>
                            <input class="orq-input" wire:model="link.{{ $field }}" placeholder="{{ $placeholder }}" dir="auto" maxlength="{{ $loop->first ? 1000 : 100 }}"
                                @if ($field === 'utm_source' || $field === 'utm_medium') list="{{ $field }}-ideas" @endif
                                aria-invalid="{{ $errors->has('link.'.$field) ? 'true' : 'false' }}" required>
                            @error('link.'.$field)<span class="orq-error">{{ $message }}</span>@enderror
                        </label>
                    @endforeach
                </div>
                <datalist id="utm_source-ideas"><option value="facebook"></option><option value="instagram"></option><option value="tiktok"></option><option value="google"></option><option value="whatsapp"></option></datalist>
                <datalist id="utm_medium-ideas"><option value="paid_social"></option><option value="cpc"></option><option value="organic_social"></option><option value="email"></option><option value="referral"></option></datalist>
                <button type="submit" class="orq-button orq-button-solid" wire:loading.attr="disabled" wire:target="buildLink">إنشاء الرابط</button>
            </form>
            @if ($campaignUrl)
                <div class="orq-tool-result" x-data="{ copied: false }" wire:key="link-{{ md5($campaignUrl) }}">
                    <label class="orq-field"><span>رابط الحملة</span><input x-ref="url" class="orq-input" readonly dir="ltr" value="{{ $campaignUrl }}" @focus="$event.target.select()"></label>
                    <div class="orq-inline-actions">
                        <button type="button" class="orq-button orq-button-light" x-on:click="$refs.url.select(); navigator.clipboard?.writeText($refs.url.value).then(() => copied = true).catch(() => copied = false)">
                            @svg('heroicon-o-clipboard-document') نسخ الرابط
                        </button>
                        <span class="orq-note" role="status" x-show="copied" x-cloak>تم النسخ.</span>
                    </div>
                </div>
            @endif
        </section>

        <section class="orq-card">
            <div class="orq-card-head"><div><h2>حاسبة ميزانية الإعلان</h2><p>احسب نقطة التعادل بناءً على قيمة الطلب وتكاليفه المتغيرة.</p></div><span class="orq-icon">@svg('heroicon-o-calculator')</span></div>
            <form wire:submit="calculateBudget" class="orq-tool-form">
                <div class="orq-form-grid">
                    @foreach ([
                        ['value', 'متوسط قيمة الطلب (ج.م)', '700', '0.01', '0.01'],
                        ['cost', 'تكلفة الطلب قبل الإعلان (ج.م)', '350', '0', '0.01'],
                        ['spend', 'ميزانية الإعلان (ج.م)', '3000', '0', '0.01'],
                        ['orders', 'عدد الطلبات المحققة المستهدف', '15', '1', '1'],
                    ] as [$field, $label, $placeholder, $min, $step])
                        <label class="orq-field">
                            <span>{{ $label }}</span>
                            <input class="orq-input" type="number" wire:model="budget.{{ $field }}" placeholder="{{ $placeholder }}" min="{{ $min }}" step="{{ $step }}" inputmode="{{ $field === 'orders' ? 'numeric' : 'decimal' }}"
                                aria-invalid="{{ $errors->has('budget.'.$field) ? 'true' : 'false' }}" required>
                            @error('budget.'.$field)<span class="orq-error">{{ $message }}</span>@enderror
                        </label>
                    @endforeach
                </div>
                <p class="orq-note">احسب تكلفة المنتج والتغليف والشحن والرسوم وأثر المرتجعات ضمن تكلفة الطلب.</p>
                <button type="submit" class="orq-button orq-button-solid" wire:loading.attr="disabled" wire:target="calculateBudget">احسب الميزانية</button>
            </form>
            @if ($budgetResult)
                <div class="orq-tool-result" role="status">
                    @if ($budgetResult['contribution'] <= 0)
                        <p class="orq-error">تكلفة الطلب تساوي أو تتجاوز قيمته. مفيش هامش متاح للإعلان.</p>
                    @endif
                    <dl class="orq-budget-results">
                        <div><dt>هامش الطلب قبل الإعلان</dt><dd><bdi>{{ Report::money($budgetResult['contribution'], 2) }}</bdi></dd></div>
                        <div><dt>تكلفة الطلب حسب الخطة</dt><dd><bdi>{{ Report::money($budgetResult['planned_cpa'], 2) }}</bdi></dd></div>
                        <div><dt>العائد المطلوب للتعادل</dt><dd><bdi>{{ $budgetResult['break_even_roas'] === null ? '—' : number_format($budgetResult['break_even_roas'], 2).'×' }}</bdi></dd></div>
                        <div><dt>عدد الطلبات للتعادل</dt><dd>{{ $budgetResult['break_even_orders'] === null ? '—' : number_format($budgetResult['break_even_orders']) }}</dd></div>
                        <div><dt>المتبقي بعد التكاليف والإعلان</dt><dd><bdi>{{ Report::money($budgetResult['profit'], 2) }}</bdi></dd></div>
                    </dl>
                </div>
            @endif
        </section>
    </div>
</x-filament-panels::page>
