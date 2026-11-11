@extends('layouts.app')

@php
$pageTitle = __('Application Submitted');
@endphp

@section('content')
<section class="success-section">
    <div class="container">
        <div class="success-card" data-aos="zoom-in">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>{{ __('Application Submitted!') }}</h1>
            <p class="success-message">
                {{ __('Thank you for applying for') }} <strong>{{ $job->localized_title }}</strong>.
                {{ __('We have received your application and will review it shortly.') }}
            </p>
            <p class="success-note">
                <i class="fas fa-info-circle"></i>
                {{ __('We will contact you via email or phone if your profile matches our requirements.') }}
            </p>
            <div class="success-actions">
                <a href="{{ route('jobs.index') }}" class="btn-secondary-link">
                    <i class="fas fa-briefcase"></i> {{ __('View More Jobs') }}
                </a>
                <a href="{{ route('home') }}" class="btn-primary-link">
                    <i class="fas fa-home"></i> {{ __('Back to Home') }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.success-section {
    padding: 4rem 0;
    min-height: 60vh;
    display: flex;
    align-items: center;
}
.success-card {
    max-width: 550px;
    margin: 0 auto;
    text-align: center;
    background: #fff;
    border-radius: 20px;
    padding: 3rem 2rem;
    box-shadow: 0 8px 40px rgba(0,0,0,0.1);
}
.success-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    animation: successPulse 2s ease-in-out infinite;
}
.success-icon i { font-size: 2.5rem; color: #fff; }
@keyframes successPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
    50% { box-shadow: 0 0 0 15px rgba(16,185,129,0); }
}
.success-card h1 {
    color: var(--primary-blue);
    font-size: 1.75rem;
    margin-bottom: 1rem;
}
.success-message {
    font-size: 1rem;
    color: var(--dark-gray);
    line-height: 1.7;
    margin-bottom: 1.25rem;
}
.success-note {
    background: #f0f9ff;
    border-radius: 10px;
    padding: 0.85rem 1.25rem;
    font-size: 0.9rem;
    color: #0369a1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-align: left;
    margin-bottom: 1.5rem;
}
.success-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}
.btn-primary-link, .btn-secondary-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s;
}
.btn-primary-link {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-green));
    color: #fff;
}
.btn-primary-link:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(13,110,253,0.3); color: #fff; }
.btn-secondary-link {
    background: #f1f5f9;
    color: var(--primary-blue);
}
.btn-secondary-link:hover { background: #e2e8f0; color: var(--primary-blue); }
</style>
@endpush
