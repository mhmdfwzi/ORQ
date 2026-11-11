@extends('layouts.app')

@php
$pageTitle = __('Terms & Conditions');
$pageDescription = app()->getLocale() == 'ar' ? 'اقرأ الشروط والأحكام لشركة ميديكامو.' : 'Read the Terms and Conditions of Medicamo.';
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">{{ __('Terms & Conditions') }}</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li>/</li>
            <li>{{ __('Terms & Conditions') }}</li>
        </ul>
    </div>
</section>

<!-- ==================== Terms Content ==================== -->
<section class="legal-section">
    <div class="container">
        <div class="legal-content" data-aos="fade-up">
            <p class="last-updated">Last Updated: {{ date('F Y') }}</p>

            <h3>1. Agreement to Terms</h3>
            <p>By accessing and using the Medicamo website, you agree to be bound by these Terms and Conditions. If you do not agree with any part of these terms, please do not use our website.</p>

            <h3>2. Use of Our Services</h3>
            <p>You agree to use our services only for lawful purposes and in accordance with these Terms. You must not:</p>
            <ul>
                <li>Use our services in any way that violates applicable laws</li>
                <li>Attempt to gain unauthorized access to our systems</li>
                <li>Interfere with the proper working of the website</li>
                <li>Use automated systems to access our website without permission</li>
            </ul>

            <h3>3. Products and Pricing</h3>
            <p>We strive to ensure that all product information and prices are accurate. However, errors may occur. We reserve the right to:</p>
            <ul>
                <li>Correct any errors in pricing or product information</li>
                <li>Cancel orders that contain pricing errors</li>
                <li>Limit quantities of products available for purchase</li>
            </ul>

            <h3>4. Orders and Payment</h3>
            <p>By placing an order, you warrant that:</p>
            <ul>
                <li>You are legally capable of entering into binding contracts</li>
                <li>The information you provide is accurate and complete</li>
                <li>You are authorized to use the payment method provided</li>
            </ul>

            <h3>5. Shipping and Delivery</h3>
            <p>We will make every effort to deliver your order within the estimated timeframe. However, delivery times are not guaranteed and may vary based on location and circumstances beyond our control.</p>

            <h3>6. Returns and Refunds</h3>
            <p>Our return policy allows returns within 30 days of purchase for most items. Products must be unused and in original packaging. Please contact our customer service for return authorization.</p>

            <h3>7. Intellectual Property</h3>
            <p>All content on this website, including text, graphics, logos, and images, is the property of Medicamo and protected by copyright laws. You may not reproduce or distribute this content without our written permission.</p>

            <h3>8. Limitation of Liability</h3>
            <p>Medicamo shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of our services or products.</p>

            <h3>9. Changes to Terms</h3>
            <p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting to our website.</p>

            <h3>10. Contact Information</h3>
            <p>For questions about these Terms and Conditions, please <a href="{{ route('contact') }}">contact us</a>.</p>
        </div>
    </div>
</section>
@endsection
