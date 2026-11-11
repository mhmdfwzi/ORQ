@extends('layouts.app')

@php
$pageTitle = __('Contact Us');
$pageDescription = app()->getLocale() == 'ar' ? 'تواصل مع ميديكامو. نحن هنا لمساعدتك.' : 'Get in touch with Medicamo. We are here to help you.';
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">{{ __('Contact Us') }}</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li>/</li>
            <li>{{ __('Contact') }}</li>
        </ul>
    </div>
</section>

<!-- ==================== Contact Section ==================== -->
<section class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form" data-aos="fade-left">
                <h3><i class="fas fa-envelope"></i> {{ __('Send Us a Message') }}</h3>
                <p style="color: var(--medium-gray); margin-bottom: 2rem;">
                    {{ __('We\'re here to answer your inquiries. Please fill out the form and we\'ll get back to you as soon as possible.') }}
                </p>

                @if(session('success'))
                <div class="alert alert-success" style="margin-bottom: 1.5rem; padding: 1rem; background: #d1fae5; color: #065f46; border-radius: 10px;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; background: #fee2e2; color: #991b1b; border-radius: 10px;">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>{{ __('Full Name') }} <span style="color: red;">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('Enter your full name') }}" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group">
                        <label>{{ __('Email') }} <span style="color: red;">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="example@email.com" value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group">
                        <label>{{ __('Phone Number') }}</label>
                        <input type="tel" name="phone" class="form-control" placeholder="{{ $settings['contact_phone'] ?? '+20 xxx xxx xxxx' }}" value="{{ old('phone') }}">
                    </div>

                    <div class="form-group">
                        <label>{{ __('Subject') }} <span style="color: red;">*</span></label>
                        <select name="subject" class="form-control" required>
                            <option value="">{{ __('Select Subject') }}</option>
                            <option value="order" {{ old('subject') == 'order' ? 'selected' : '' }}>{{ __('Order Inquiry') }}</option>
                            <option value="product" {{ old('subject') == 'product' ? 'selected' : '' }}>{{ __('Product Inquiry') }}</option>
                            <option value="complaint" {{ old('subject') == 'complaint' ? 'selected' : '' }}>{{ __('Complaint') }}</option>
                            <option value="suggestion" {{ old('subject') == 'suggestion' ? 'selected' : '' }}>{{ __('Suggestion') }}</option>
                            <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>{{ __('Message') }} <span style="color: red;">*</span></label>
                        <textarea name="message" class="form-control" rows="5" placeholder="{{ __('Write your message here...') }}"
                            required>{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i>
                        {{ __('Send Message') }}
                    </button>
                </form>
            </div>

            <!-- Contact Info Cards - Dynamic from Settings -->
            <div class="contact-info-cards" data-aos="fade-right">
                <!-- Address Card -->
                <div class="contact-card">
                    <div class="contact-card-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-card-info">
                        <h4>{{ __('Our Address') }}</h4>
                        <p>{{ $settings['contact_address'] ?? $settings['company_address'] ?? __('Address not configured') }}</p>
                    </div>
                </div>

                <!-- Phone Card -->
                <div class="contact-card">
                    <div class="contact-card-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-card-info">
                        <h4>Call Us</h4>
                        @if(!empty($settings['contact_phone']))
                        <p><a href="tel:{{ $settings['contact_phone'] }}" style="color: inherit;">{{ $settings['contact_phone'] }}</a></p>
                        @endif
                        @if(!empty($settings['contact_phone_2']))
                        <p><a href="tel:{{ $settings['contact_phone_2'] }}" style="color: inherit;">{{ $settings['contact_phone_2'] }}</a></p>
                        @endif
                        @if(empty($settings['contact_phone']) && empty($settings['contact_phone_2']))
                        <p>Phone not configured</p>
                        @endif
                    </div>
                </div>

                <!-- Email Card -->
                <div class="contact-card">
                    <div class="contact-card-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-card-info">
                        <h4>Email Us</h4>
                        @if(!empty($settings['contact_email']))
                        <p><a href="mailto:{{ $settings['contact_email'] }}" style="color: inherit;">{{ $settings['contact_email'] }}</a></p>
                        @endif
                        @if(!empty($settings['contact_email_2']) || !empty($settings['support_email']))
                        <p><a href="mailto:{{ $settings['contact_email_2'] ?? $settings['support_email'] }}" style="color: inherit;">{{ $settings['contact_email_2'] ?? $settings['support_email'] }}</a></p>
                        @endif
                        @if(empty($settings['contact_email']) && empty($settings['contact_email_2']))
                        <p>Email not configured</p>
                        @endif
                    </div>
                </div>

                <!-- Working Hours Card -->
                <div class="contact-card">
                    <div class="contact-card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="contact-card-info">
                        <h4>Working Hours</h4>
                        @if(!empty($settings['working_hours']))
                        <p>{!! nl2br(e($settings['working_hours'])) !!}</p>
                        @else
                        <p>{{ $settings['working_hours_weekday'] ?? 'Saturday - Thursday: 9:00 AM - 10:00 PM' }}</p>
                        <p>{{ $settings['working_hours_weekend'] ?? 'Friday: Closed' }}</p>
                        @endif
                    </div>
                </div>

                <!-- WhatsApp Card -->
                @if(!empty($settings['whatsapp_number']) || !empty($settings['contact_whatsapp']))
                <div class="contact-card"
                    style="background: linear-gradient(135deg, #25D366, #128C7E);">
                    <div class="contact-card-icon" style="background: var(--white); color: #25D366;">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="contact-card-info">
                        <h4 style="color: var(--white);">WhatsApp</h4>
                        <p style="color: rgba(255,255,255,0.9);">Chat with us on WhatsApp</p>
                        @php
                            $whatsappNumber = $settings['whatsapp_number'] ?? $settings['contact_whatsapp'] ?? '';
                            $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsappNumber);
                        @endphp
                        <a href="https://wa.me/{{ $cleanWhatsapp }}" target="_blank" class="btn btn-outline"
                            style="margin-top: 1rem; color: var(--white); border-color: var(--white);">
                            <i class="fab fa-whatsapp"></i>
                            Start Chat
                        </a>
                    </div>
                </div>
                @endif

                <!-- Social Media Links -->
                @if(!empty($settings['facebook_url']) || !empty($settings['instagram_url']) || !empty($settings['twitter_url']))
                <div class="contact-card">
                    <div class="contact-card-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-share-alt" style="color: #fff;"></i>
                    </div>
                    <div class="contact-card-info">
                        <h4>Follow Us</h4>
                        <div class="social-links-contact">
                            @if(!empty($settings['facebook_url']))
                            <a href="{{ $settings['facebook_url'] }}" target="_blank" class="social-link-btn facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            @endif
                            @if(!empty($settings['instagram_url']))
                            <a href="{{ $settings['instagram_url'] }}" target="_blank" class="social-link-btn instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            @endif
                            @if(!empty($settings['twitter_url']))
                            <a href="{{ $settings['twitter_url'] }}" target="_blank" class="social-link-btn twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            @endif
                            @if(!empty($settings['youtube_url']))
                            <a href="{{ $settings['youtube_url'] }}" target="_blank" class="social-link-btn youtube">
                                <i class="fab fa-youtube"></i>
                            </a>
                            @endif
                            @if(!empty($settings['linkedin_url']))
                            <a href="{{ $settings['linkedin_url'] }}" target="_blank" class="social-link-btn linkedin">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Map -->
        @if(!empty($settings['google_map_embed']) || !empty($settings['map_embed_url']))
        <div class="map-container" data-aos="fade-up">
            <iframe
                src="{{ $settings['google_map_embed'] ?? $settings['map_embed_url'] }}"
                width="100%" height="400" style="border:0; border-radius: 15px;" allowfullscreen=""
                loading="lazy"></iframe>
        </div>
        @elseif(!empty($settings['map_latitude']) && !empty($settings['map_longitude']))
        <div class="map-container" data-aos="fade-up">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3000!2d{{ $settings['map_longitude'] }}!3d{{ $settings['map_latitude'] }}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2seg"
                width="100%" height="400" style="border:0; border-radius: 15px;" allowfullscreen=""
                loading="lazy"></iframe>
        </div>
        @endif
    </div>
</section>

@push('styles')
<style>
.social-links-contact {
    display: flex;
    gap: 0.75rem;
    margin-top: 0.75rem;
}

.social-link-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.social-link-btn:hover {
    transform: scale(1.1);
    color: #fff;
}

.social-link-btn.facebook { background: #3b5998; }
.social-link-btn.instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
.social-link-btn.twitter { background: #1da1f2; }
.social-link-btn.youtube { background: #ff0000; }
.social-link-btn.linkedin { background: #0077b5; }
</style>
@endpush
@endsection
