@props(['title', 'description'])

<header {{ $attributes->class(['orq-hero']) }}>
    <div class="orq-hero-copy">
        <h1>{{ $title }}</h1>
        <p>{{ $description }}</p>
    </div>
    @if (trim($slot->toHtml()) !== '')
        <div class="orq-hero-actions">{{ $slot }}</div>
    @endif
</header>
