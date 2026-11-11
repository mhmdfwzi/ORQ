@extends('layouts.app')

@php
$pageTitle = __('Privacy Policy');
$pageDescription = app()->getLocale() == 'ar' ? 'اقرأ سياسة الخصوصية لشركة ميديكامو.' : 'Read the Privacy Policy of Medicamo.';
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">{{ __('Privacy Policy') }}</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li>/</li>
            <li>{{ __('Privacy Policy') }}</li>
        </ul>
    </div>
</section>

<!-- ==================== Privacy Content ==================== -->
<section class="legal-section">
    <div class="container">
        <div class="legal-content" data-aos="fade-up">
            <p class="last-updated">Last Updated: {{ date('F Y') }}</p>

            <h3>1. Introduction</h3>
            <p>At Medicamo, we are committed to protecting your privacy. This policy explains how we collect, use, and
                safeguard your personal information when you use our website and services.</p>

            <h3>2. Information We Collect</h3>
            <p>We collect information you provide directly to us, such as:</p>
            <ul>
                <li>Name, email address, and phone number</li>
                <li>Shipping and billing addresses</li>
                <li>Payment information</li>
                <li>Order history and preferences</li>
            </ul>

            <h3>3. How We Use Your Information</h3>
            <p>We use the information we collect to:</p>
            <ul>
                <li>Process and fulfill your orders</li>
                <li>Send order confirmations and updates</li>
                <li>Provide customer support</li>
                <li>Send promotional communications (with your consent)</li>
                <li>Improve our website and services</li>
            </ul>

            <h3>4. Data Protection</h3>
            <p>We implement appropriate security measures to protect your personal information from unauthorized access,
                alteration, disclosure, or destruction.</p>

            <h3>5. Information Sharing</h3>
            <p>We do not sell or rent your personal information to third parties. We may share your information with:
            </p>
            <ul>
                <li>Shipping carriers to deliver your orders</li>
                <li>Payment processors to complete transactions</li>
                <li>Service providers who assist our operations</li>
            </ul>

            <h3>6. Cookies</h3>
            <p>We use cookies to enhance your browsing experience. You can choose to disable cookies in your browser
                settings, but some features of our website may not function properly.</p>

            <h3>7. Your Rights</h3>
            <p>You have the right to:</p>
            <ul>
                <li>Access your personal information</li>
                <li>Request correction of inaccurate data</li>
                <li>Request deletion of your data</li>
                <li>Opt-out of marketing communications</li>
            </ul>

            <h3>8. Changes to This Policy</h3>
            <p>We may update this privacy policy from time to time. We will notify you of any material changes by
                posting the new policy on this page.</p>

            <h3>9. Contact Us</h3>
            <p>If you have any questions about this privacy policy, please <a href="{{ route('contact') }}">contact us</a>.</p>
        </div>
    </div>
</section>
@endsection
