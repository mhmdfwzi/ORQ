@extends('layouts.app')

@php
$pageTitle = __('About Us');
$pageDescription = app()->getLocale() == 'ar' ? 'تعرف على ميديكامو - شركة حلول طبية ذكية' : 'Learn about Medicamo - A smart medical solutions company';
@endphp

@section('content')
@php
    $isAr = app()->getLocale() == 'ar';
    
    $aboutWhoWeAreEn = \App\Models\Setting::getValue('about_who_we_are', '<strong>Medicamo</strong> is a smart medical solutions company operating in the fields of Dietary Supplements, Medical Cosmeceuticals, and Third-Party Manufacturing');
    $aboutWhoWeAreAr = \App\Models\Setting::getValue('about_who_we_are_ar', '');
    $aboutWhoWeAre = ($isAr && $aboutWhoWeAreAr) ? $aboutWhoWeAreAr : $aboutWhoWeAreEn;
    
    $aboutVisionEn = \App\Models\Setting::getValue('about_vision', 'To become one of the leading Egyptian companies in the fields of dietary supplements, medical cosmeceuticals, and pharmaceuticals, recognized for product quality, innovation, and a science-driven approach.');
    $aboutVisionAr = \App\Models\Setting::getValue('about_vision_ar', '');
    $aboutVision = ($isAr && $aboutVisionAr) ? $aboutVisionAr : $aboutVisionEn;
    
    $aboutMissionEn = \App\Models\Setting::getValue('about_mission', 'At Medicamo, we are committed to delivering safe, effective, and scientifically grounded health and beauty products through high-quality manufacturing, professional management, and strong partnerships.');
    $aboutMissionAr = \App\Models\Setting::getValue('about_mission_ar', '');
    $aboutMission = ($isAr && $aboutMissionAr) ? $aboutMissionAr : $aboutMissionEn;
    
    $aboutPageImage = \App\Models\Setting::getValue('about_page_image', '');
@endphp
<!-- ==================== About Hero ==================== -->
<section class="about-hero">
    <div class="container">
        <h1 data-aos="fade-up">{{ __('Who We Are') }}</h1>
        <div data-aos="fade-up" data-aos-delay="100">
            {!! strip_tags($aboutWhoWeAre) !!}
        </div>
    </div>
</section>

<!-- ==================== Vision & Mission ==================== -->
<section class="about-story">
    <div class="container">
        <div class="story-grid">
            <div class="story-image" data-aos="fade-left">
                <img src="{{ $aboutPageImage ? asset('storage/' . $aboutPageImage) : asset('images/about-story.png') }}" alt="{{ __('Medicamo Laboratory') }}">
            </div>

            <div class="story-content" data-aos="fade-right">
                <h2 class="fw-bold mb-4"><i class="fas fa-eye text-primary me-2"></i> {{ __('Our Vision') }}</h2>
                <p class="lead text-secondary mb-4">
                    {{ $aboutVision }}
                </p>
                
                <h2 class="fw-bold mb-4 mt-5"><i class="fas fa-bullseye text-success me-2"></i> {{ __('Our Mission') }}</h2>
                <p class="text-secondary mb-4">
                    {{ $aboutMission }}
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== Core Values ==================== -->
<section class="values-section">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>{{ __('Our Core Values') }}</h2>
            <p>{{ __('The principles that guide everything we do') }}</p>
        </div>

        <div class="values-grid">
            <div class="value-card" data-aos="fade-up" data-aos-delay="100">
                <div class="value-icon">
                    <i class="fas fa-award"></i>
                </div>
                <h3>{{ __('Excellence') }}</h3>
                <p>{{ __('We strive for excellence in every product and process') }}</p>
            </div>

            <div class="value-card" data-aos="fade-up" data-aos-delay="150">
                <div class="value-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>{{ __('Integrity & Trust') }}</h3>
                <p>{{ __('Building trust through honest and ethical practices') }}</p>
            </div>

            <div class="value-card" data-aos="fade-up" data-aos-delay="200">
                <div class="value-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3>{{ __('Innovation') }}</h3>
                <p>{{ __('Continuously seeking new and better solutions') }}</p>
            </div>

            <div class="value-card" data-aos="fade-up" data-aos-delay="250">
                <div class="value-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h3>{{ __('Respect') }}</h3>
                <p>{{ __('Treating everyone with dignity and fairness') }}</p>
            </div>

            <div class="value-card" data-aos="fade-up" data-aos-delay="300">
                <div class="value-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h3>{{ __('Accountability') }}</h3>
                <p>{{ __('Taking ownership of our actions and results') }}</p>
            </div>

            <div class="value-card" data-aos="fade-up" data-aos-delay="350">
                <div class="value-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>{{ __('Collaboration') }}</h3>
                <p>{{ __('Working together as a team to achieve shared goals') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== Guiding Principles ==================== -->
<section class="section" style="background: var(--light-gray);">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>{{ __('Guiding Principles') }}</h2>
            <p>{{ __('The strategic principles that drive our decisions') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="principle-card">
                    <div class="principle-number">1</div>
                    <h4>Product First</h4>
                    <p>No marketing expansion without consistent, proven quality</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="150">
                <div class="principle-card">
                    <div class="principle-number">2</div>
                    <h4>Systems Before Individuals</h4>
                    <p>The company is driven by policies and procedures, not individuals alone</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="principle-card">
                    <div class="principle-number">3</div>
                    <h4>Building Capable Teams</h4>
                    <p>Building departments that achieve strategic objectives</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="250">
                <div class="principle-card">
                    <div class="principle-number">4</div>
                    <h4>Measurement Before Decision</h4>
                    <p>No critical decision is made without data and reports</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="principle-card">
                    <div class="principle-number">5</div>
                    <h4>Training Before Expansion</h4>
                    <p>Developing knowledgeable teams before increasing operational complexity</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="350">
                <div class="principle-card">
                    <div class="principle-number">6</div>
                    <h4>Operational Flexibility</h4>
                    <p>Flexibility with strict quality standards</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mx-auto" data-aos="fade-up" data-aos-delay="400">
                <div class="principle-card">
                    <div class="principle-number">7</div>
                    <h4>Smart Strategic Growth</h4>
                    <p>Strategic expansion, not random growth</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== Strategic Pillars ==================== -->
<section class="section" style="background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-light));">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2 style="color: var(--white);">Strategic Pillars</h2>
            <p style="color: rgba(255,255,255,0.9);">The foundations we build our future on</p>
        </div>

        <div class="features-grid">
            <div class="feature-box"
                style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);"
                data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon" style="background: var(--white); color: var(--primary-blue);">
                    <i class="fas fa-cogs"></i>
                </div>
                <h3 style="color: var(--white);">Operational Excellence</h3>
                <p style="color: rgba(255,255,255,0.9);">Supply Chain & Operations Efficiency</p>
            </div>

            <div class="feature-box"
                style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);"
                data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon" style="background: var(--white); color: var(--secondary-green);">
                    <i class="fas fa-sitemap"></i>
                </div>
                <h3 style="color: var(--white);">Organizational Development</h3>
                <p style="color: rgba(255,255,255,0.9);">Institutional Growth & Structure</p>
            </div>

            <div class="feature-box"
                style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);"
                data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon" style="background: var(--white); color: var(--accent-gold);">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3 style="color: var(--white);">People & Leadership</h3>
                <p style="color: rgba(255,255,255,0.9);">Human Resources & Leadership Development</p>
            </div>

            <div class="feature-box"
                style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);"
                data-aos="fade-up" data-aos-delay="400">
                <div class="feature-icon" style="background: var(--white); color: var(--primary-blue);">
                    <i class="fas fa-star"></i>
                </div>
                <h3 style="color: var(--white);">Product Excellence</h3>
                <p style="color: rgba(255,255,255,0.9);">Quality & Innovation in Every Product</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== Team ==================== -->
@if(\App\Models\Setting::getValue('show_team_section', '0') == '1')
<section class="team-section">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Our Team</h2>
            <p>Meet the professional team behind Medicamo</p>
        </div>

        <div class="team-grid">
            @forelse($teamMembers as $member)
            <div class="team-member" data-aos="fade-up" data-aos-delay="{{ ($loop->index + 1) * 100 }}">
                <div class="team-image">
                    @if($member->image)
                    <img src="{{ asset('storage/' . $member->image) }}" alt="{{ $member->name }}">
                    @else
                    <img src="{{ asset('images/team-ceo.png') }}" alt="{{ $member->name }}">
                    @endif
                </div>
                <div class="team-info">
                    <h4>{{ $member->name }}</h4>
                    <p>{{ $member->position }}</p>
                    @if($member->bio)
                    <p class="text-muted small mb-2">{{ Str::limit($member->bio, 100) }}</p>
                    @endif
                    <div class="team-social">
                        @if($member->social_links)
                            @foreach($member->social_links as $platform => $url)
                                <a href="{{ $url }}" target="_blank" title="{{ ucfirst($platform) }}">
                                    @if(in_array(strtolower($platform), ['facebook', 'twitter', 'linkedin', 'instagram', 'youtube', 'github']))
                                        <i class="fab fa-{{ strtolower($platform) }}"></i>
                                    @elseif(strtolower($platform) == 'website')
                                        <i class="fas fa-globe"></i>
                                    @else
                                        <i class="fas fa-link"></i>
                                    @endif
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>No team members found.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endif

<!-- ==================== Certifications ==================== -->
<section class="section" style="background: var(--light-gray);">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Certifications & Licenses</h2>
            <p>We're proud to have achieved the highest standards and accreditations</p>
        </div>

        <div class="features-grid">
            <div class="feature-box" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3>Health Authority</h3>
                <p>Licensed and certified by official health authorities</p>
            </div>

            <div class="feature-box" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon">
                    <i class="fas fa-award"></i>
                </div>
                <h3>ISO 9001:2015</h3>
                <p>Certified with international quality management standards</p>
            </div>

            <div class="feature-box" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>GMP</h3>
                <p>Good Manufacturing Practice standards for pharmaceutical products</p>
            </div>

            <div class="feature-box" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>SSL Secure</h3>
                <p>Secure website with SSL certificate to protect your data</p>
            </div>
        </div>
    </div>
</section>
@endsection
