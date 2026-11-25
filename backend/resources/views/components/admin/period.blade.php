@props(['period', 'action', 'source' => ''])

@php
    $calendarDays = (int) $period->from->diffInDays($period->through) + 1;
    $endsToday = $period->through->isToday();
    $activePreset = $endsToday && in_array($calendarDays, [7, 30, 90], true) ? $calendarDays : null;
@endphp

<div class="orq-period">
    <nav class="orq-presets" aria-label="فترات التقرير">
        @foreach ([7, 30, 90] as $days)
            <a href="{{ $action.'?'.http_build_query(['days' => $days] + ($source !== '' ? ['source' => $source] : [])) }}"
                @if ($activePreset === $days) aria-current="true" @endif>آخر {{ $days }} يوم</a>
        @endforeach
    </nav>
    <form method="get" action="{{ $action }}" class="orq-date-form">
        @if ($source !== '')
            <input type="hidden" name="source" value="{{ $source }}">
        @endif
        <label><span>من</span><input class="orq-input" type="date" name="from" value="{{ $period->from->toDateString() }}" max="{{ today('Africa/Cairo')->toDateString() }}" required></label>
        <label><span>إلى</span><input class="orq-input" type="date" name="to" value="{{ $period->through->toDateString() }}" max="{{ today('Africa/Cairo')->toDateString() }}" required></label>
        <button class="orq-button orq-button-light" type="submit">عرض</button>
    </form>
    @if (request()->hasAny(['from', 'to']) && (request()->query('from') !== $period->from->toDateString() || request()->query('to') !== $period->through->toDateString()))
        <p class="orq-error" role="status">تم عرض الفترة الافتراضية. اختر فترة صحيحة لا تتجاوز 366 يوماً وتنتهي اليوم أو قبله.</p>
    @endif
</div>
