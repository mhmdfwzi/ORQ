@extends('layouts.app')

@php
$pageTitle = __('Home');
$pageDescription = app()->getLocale() == 'ar' ? 'ميديكامو شركة رائدة في إنتاج الأدوية المتخصصة في المنتجات العلاجية والتجميلية' : 'Medicamo is a leading pharmaceutical production company specializing in therapeutic and cosmetic products. Feel Better Naturally.';
@endphp

@section('content')
<!-- ==================== Hero Section with Text Slider ==================== -->
<section class="hero-section-new">
    <!-- Background Image -->
    <div class="hero-bg-image" style="background-image: url('{{ asset('images/hero-bg.png') }}');"></div>
    
    <!-- Overlay -->
    <div class="hero-overlay"></div>
    
    <!-- Floating Elements -->
    <div class="hero-floating-elements">
        <div class="floating-circle circle-1"></div>
        <div class="floating-circle circle-2"></div>
        <div class="floating-circle circle-3"></div>
    </div>
    
    <div class="container">
        <div class="hero-wrapper">
            <!-- Hero Text Slider -->
            <div class="hero-text-slider">
                <!-- Slide 1 -->
                <div class="hero-slide active" data-slide="1">
                    <span class="hero-badge">
                        <i class="fas fa-shield-alt"></i> {{ __('Trusted Pharmaceutical Company') }}
                    </span>
                    <h1 class="hero-title-new">
                        {{ __('Your Health Is Our') }} 
                        <span class="text-gradient">{{ __('Priority') }}</span>
                    </h1>
                    <p class="hero-subtitle-new">
                        {{ __('Premium pharmaceutical & cosmetic products with fast delivery. Quality you can trust, prices you\'ll love.') }}
                    </p>
                </div>
                
                <!-- Slide 2 -->
                <div class="hero-slide" data-slide="2">
                    <span class="hero-badge">
                        <i class="fas fa-spa"></i> {{ __('Natural Beauty Products') }}
                    </span>
                    <h1 class="hero-title-new">
                        {{ __('Premium') }} <span class="text-gradient">{{ __('Skincare') }}</span> {{ __('Products') }}
                    </h1>
                    <p class="hero-subtitle-new">
                        {{ __('Discover our collection of dermatologist-approved skincare solutions. Natural ingredients, visible results.') }}
                    </p>
                </div>
                
                <!-- Slide 3 -->
                <div class="hero-slide" data-slide="3">
                    <span class="hero-badge">
                        <i class="fas fa-tags"></i> {{ __('Limited Time Offer') }}
                    </span>
                    <h1 class="hero-title-new">
                        {{ __('Special Offers Up To') }} 
                        <span class="text-gradient">{{ __('30% Off') }}</span>
                    </h1>
                    <p class="hero-subtitle-new">
                        {{ __('Shop now and save big on your favorite health & beauty products. Don\'t miss out on our exclusive deals!') }}
                    </p>
                </div>
                
                <!-- CTA Buttons (shared) -->
                <div class="hero-buttons-new">
                    <a href="{{ route('products.index') }}" class="btn-hero-primary">
                        <i class="fas fa-shopping-bag"></i>
                        {{ __('Shop Now') }}
                    </a>
                    <a href="{{ route('contact') }}" class="btn-hero-secondary">
                        <i class="fas fa-headset"></i>
                        {{ __('Contact Us') }}
                    </a>
                </div>
                
                <!-- Slider Dots -->
                <div class="slider-dots">
                    <button class="slider-dot active" data-slide="1"></button>
                    <button class="slider-dot" data-slide="2"></button>
                    <button class="slider-dot" data-slide="3"></button>
                </div>
                
                <!-- Trust Badges -->
                <div class="hero-trust-badges">
                    <div class="trust-badge">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ __('100% Authentic') }}</span>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-truck"></i>
                        <span>{{ __('Fast Delivery') }}</span>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-lock"></i>
                        <span>{{ __('Secure Payment') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== Featured Products ==================== -->
<section class="section">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>{{ __('Our Featured Products') }}</h2>
            <p>{{ __('Choose from our best pharmaceutical and therapeutic products') }}</p>
        </div>

        <div class="products-grid">
            @foreach($featuredProducts as $index => $product)
            <x-product-card :product="$product" :delay="(($index % 6) + 1) * 50" />
            @endforeach
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-large">
                <i class="fas fa-th"></i>
                {{ __('View All Products') }}
            </a>
        </div>
    </div>
</section>

<!-- ==================== Special Offers Section ==================== -->
@if(isset($offerProducts) && $offerProducts->count() > 0)
<section class="section offers-section">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2><i class="fas fa-fire" style="color: #ff6b6b;"></i> {{ __('Hot Offers') }}</h2>
            <p>{{ __('Don\'t miss our exclusive deals and limited-time offers') }}</p>
        </div>

        <div class="offers-grid">
            <!-- Main Offer Banner (First Featured Product or Static) -->
            @php
                $mainOffer = $offerProducts->where('is_offer_featured', true)->first() ?? $offerProducts->first();
                $sideOffers = $offerProducts->where('id', '!=', $mainOffer->id)->take(3);
            @endphp
            
            <div class="offer-main-banner" data-aos="fade-right">
                <div class="offer-main-content">
                    <div class="offer-badge-big">
                        <span class="discount-value">{{ $mainOffer->discount_percentage }}%</span>
                        <span class="discount-label">OFF</span>
                    </div>
                    <div class="offer-main-text">
                        <span class="offer-tag">
                            <i class="fas fa-bolt"></i> 
                            {{ $mainOffer->offer_label ?? __('Limited Time') }}
                        </span>
                        <h3>{{ app()->getLocale() == 'ar' ? ($mainOffer->name_ar ?? $mainOffer->name) : $mainOffer->name }}</h3>
                        @php
                            $offerDesc = app()->getLocale() == 'ar' ? ($mainOffer->short_description_ar ?? $mainOffer->short_description) : $mainOffer->short_description;
                        @endphp
                        <p>{{ Str::limit($offerDesc, 80) }}</p>
                        
                        @if($mainOffer->offer_end_date)
                        <div class="offer-countdown" data-end-date="{{ $mainOffer->offer_end_date->toIso8601String() }}">
                            <div class="countdown-item">
                                <span class="countdown-value days">00</span>
                                <span class="countdown-label">{{ __('Days') }}</span>
                            </div>
                            <div class="countdown-item">
                                <span class="countdown-value hours">00</span>
                                <span class="countdown-label">{{ __('Hours') }}</span>
                            </div>
                            <div class="countdown-item">
                                <span class="countdown-value minutes">00</span>
                                <span class="countdown-label">{{ __('Mins') }}</span>
                            </div>
                            <div class="countdown-item">
                                <span class="countdown-value seconds">00</span>
                                <span class="countdown-label">{{ __('Secs') }}</span>
                            </div>
                        </div>
                        @endif
                        
                        <div class="offer-prices">
                            <span class="offer-old-price">{{ number_format($mainOffer->price, 2) }} EGP</span> <span class="offer-new-price">{{ number_format($mainOffer->sale_price, 2) }} EGP</span>
                        </div>
                        
                        <a href="{{ route('products.show', $mainOffer) }}" class="btn-offer-shop">
                            <i class="fas fa-shopping-bag"></i> {{ __('Shop Now') }}
                        </a>
                    </div>
                </div>
                @if($mainOffer->images && count($mainOffer->images) > 0)
                <div class="offer-main-image" style="position: absolute; right: 20px; bottom: 20px; opacity: 0.15; pointer-events: none;">
                    <img src="{{ asset('storage/' . $mainOffer->images[0]) }}" alt="{{ $mainOffer->name }}" style="max-height: 200px; object-fit: contain;">
                </div>
                @endif
                <div class="offer-main-bg"></div>
            </div>

            <!-- Small Offer Cards -->
            @if($sideOffers->count() > 0)
            <div class="offers-side" data-aos="fade-left">
                @php
                    $cardColors = ['green', 'purple', 'orange', 'blue', 'pink'];
                    $cardIcons = ['fa-pills', 'fa-spa', 'fa-baby', 'fa-heartbeat', 'fa-leaf'];
                @endphp
                @foreach($sideOffers as $index => $offer)
                @php
                    $hasImage = $offer->images && is_array($offer->images) && count($offer->images) > 0;
                @endphp
                <a href="{{ route('products.show', $offer) }}" class="offer-card offer-card-{{ $cardColors[$index % count($cardColors)] }} {{ $hasImage ? 'has-image' : '' }}">
                    <div class="offer-card-icon">
                        @if($hasImage)
                        <img src="{{ asset('storage/' . $offer->images[0]) }}" alt="{{ $offer->name }}">
                        @else
                        <i class="fas {{ $cardIcons[$index % count($cardIcons)] }}"></i>
                        @endif
                    </div>
                    <div class="offer-card-content">
                        <span class="offer-card-badge">{{ $offer->discount_percentage }}% OFF</span>
                        <h4>{{ Str::limit($offer->name, 25) }}</h4>
                        <p>
                            <span style="text-decoration: line-through; color: #999; font-size: 0.85em;">{{ number_format($offer->price, 2) }} EGP</span>
                            <span style="color: var(--secondary-green); font-weight: 700;">{{ number_format($offer->sale_price, 2) }} EGP</span>
                        </p>
                        <span class="offer-card-link">
                            Shop <i class="fas fa-arrow-right"></i>
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="{{ route('products.index', ['on_sale' => 1]) }}" class="btn btn-outline-primary">
                <i class="fas fa-tags"></i> View All Offers
            </a>
        </div>
    </div>
</section>
@endif

<!-- ==================== About Preview ==================== -->
@php
    $isAr = app()->getLocale() == 'ar';
    
    $defaultWhoWeAre = '<strong>Medicamo</strong> is a smart medical solutions company operating in the fields of:
<ul>
<li>Dietary Supplements</li>
<li>Medical Cosmeceuticals</li>
<li>Third-Party Manufacturing (Contract Manufacturing)</li>
</ul>';
    
    $aboutWhoWeAreEn = \App\Models\Setting::getValue('about_who_we_are', $defaultWhoWeAre);
    $aboutWhoWeAreAr = \App\Models\Setting::getValue('about_who_we_are_ar', '');
    $aboutWhoWeAre = ($isAr && $aboutWhoWeAreAr) ? $aboutWhoWeAreAr : $aboutWhoWeAreEn;
    
    $aboutVisionEn = \App\Models\Setting::getValue('about_vision', 'To become one of the leading Egyptian companies in dietary supplements, medical cosmeceuticals, and pharmaceuticals, recognized for product quality, innovation, and a science-driven approach.');
    $aboutVisionAr = \App\Models\Setting::getValue('about_vision_ar', '');
    $aboutVision = ($isAr && $aboutVisionAr) ? $aboutVisionAr : $aboutVisionEn;
    
    $aboutMissionEn = \App\Models\Setting::getValue('about_mission', 'Delivering safe, effective, and scientifically grounded health and beauty products through high-quality manufacturing and professional management.');
    $aboutMissionAr = \App\Models\Setting::getValue('about_mission_ar', '');
    $aboutMission = ($isAr && $aboutMissionAr) ? $aboutMissionAr : $aboutMissionEn;
    
    $aboutHomeImage = \App\Models\Setting::getValue('about_home_image', '');
@endphp
<section class="section about-preview">
    <div class="container">
        <div class="about-content">
            <div class="about-text" data-aos="fade-left">
                <h2>Who We Are</h2>
                <div>{!! $aboutWhoWeAre !!}</div>
                
                <h4 class="mt-4 text-primary"><i class="fas fa-eye me-2"></i> Our Vision</h4>
                <p class="text-muted">
                    {{ $aboutVision }}
                </p>
                
                <h4 class="mt-3 text-success"><i class="fas fa-bullseye me-2"></i> Our Mission</h4>
                <p class="text-muted">
                    {{ $aboutMission }}
                </p>

                <a href="{{ route('about') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-right"></i>
                    Learn More About Us
                </a>
            </div>

            <div class="about-image" data-aos="fade-right">
                <img src="{{ $aboutHomeImage ? asset('storage/' . $aboutHomeImage) : asset('images/about-team.png') }}" alt="Medicamo Professional Team">
            </div>
        </div>
    </div>
</section>

<!-- ==================== Why Choose Us ==================== -->
@php
    $whyChooseTitleEn = \App\Models\Setting::getValue('why_choose_title_en', 'Why Choose Medicamo?');
    $whyChooseTitleAr = \App\Models\Setting::getValue('why_choose_title_ar', 'لماذا تختار ميديكامو؟');
    $whyChooseTitle = ($isAr && $whyChooseTitleAr) ? $whyChooseTitleAr : $whyChooseTitleEn;

    $whyChooseSubtitleEn = \App\Models\Setting::getValue('why_choose_subtitle_en', 'We offer you the best pharmaceutical shopping experience');
    $whyChooseSubtitleAr = \App\Models\Setting::getValue('why_choose_subtitle_ar', 'نقدم لك أفضل تجربة تسوق للمنتجات الطبية');
    $whyChooseSubtitle = ($isAr && $whyChooseSubtitleAr) ? $whyChooseSubtitleAr : $whyChooseSubtitleEn;

    $whyChooseFeatures = json_decode(\App\Models\Setting::getValue('why_choose_features', '[]'), true) ?: [];
@endphp
<section class="section why-us">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>{{ $whyChooseTitle }}</h2>
            <p>{{ $whyChooseSubtitle }}</p>
        </div>

        @if(count($whyChooseFeatures) > 0)
        <div class="features-grid">
            @foreach($whyChooseFeatures as $index => $feature)
            @php
                $featureTitle = ($isAr && !empty($feature['title_ar'])) ? $feature['title_ar'] : ($feature['title_en'] ?? '');
                $featureDesc = ($isAr && !empty($feature['desc_ar'])) ? $feature['desc_ar'] : ($feature['desc_en'] ?? '');
                $delay = ($index + 1) * 100;
            @endphp
            <div class="feature-box" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                <div class="feature-icon">
                    <i class="{{ $feature['icon'] ?? 'fas fa-star' }}"></i>
                </div>
                <h3>{{ $featureTitle }}</h3>
                <p>{{ $featureDesc }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<!-- ==================== Testimonials ==================== -->
<section class="section testimonials">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>What Our Customers Say</h2>
            <p>We're proud of our customers' trust and satisfaction with our services</p>
        </div>

        <div class="testimonials-grid">
            @foreach($testimonials as $index => $review)
            <div class="testimonial-card" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                <div class="testimonial-stars">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                    @endfor
                </div>
                <p class="testimonial-text">
                    "{{ $review->comment }}"
                </p>
                <div class="testimonial-author">
                    <div class="author-image">
                        <i class="fas fa-user-circle" style="font-size: 3.5rem; color: var(--primary-blue);"></i>
                    </div>
                    <div class="author-info">
                        <h4>{{ $review->user->name ?? 'Customer' }}</h4>
                        <p>Verified Buyer</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@push('scripts')
<script>
// Offers Countdown Timer
document.addEventListener('DOMContentLoaded', function() {
    const countdownElements = document.querySelectorAll('.offer-countdown');
    
    countdownElements.forEach(function(countdown) {
        const endDate = new Date(countdown.dataset.endDate).getTime();
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endDate - now;
            
            if (distance < 0) {
                countdown.innerHTML = '<span style="color:#fff;font-size:1.2rem;">Offer Expired!</span>';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            countdown.querySelector('.days').textContent = String(days).padStart(2, '0');
            countdown.querySelector('.hours').textContent = String(hours).padStart(2, '0');
            countdown.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
            countdown.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
});
</script>
@endpush
@endsection
