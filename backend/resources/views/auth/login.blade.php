@extends('layouts.app')

@php
$pageTitle = __('Login');
$pageDescription = app()->getLocale() == 'ar' ? 'تسجيل الدخول إلى حسابك في ميديكامو' : 'Login to your Medicamo account.';
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">{{ __('Login') }}</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li>/</li>
            <li>{{ __('Login') }}</li>
        </ul>
    </div>
</section>

<!-- ==================== Login Section ==================== -->
<section class="auth-section">
    <div class="container">
        <div class="auth-card" data-aos="fade-up">
            <h2>{{ __('Welcome Back') }}</h2>
            <p class="auth-subtitle">{{ __('Login to access your account') }}</p>

            @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group mb-4">
                    <label class="form-label fw-bold">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="tel" name="phone" class="form-control ps-5" placeholder="{{ __('Enter your phone number') }}" value="{{ old('phone') }}" required style="height: 50px;">
                        <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                            <i class="fas fa-phone-alt"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label fw-bold">{{ __('Password') }} <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="password" name="password" class="form-control ps-5 pe-5" placeholder="{{ __('Enter your password') }}" required style="height: 50px;">
                        <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                            <i class="fas fa-lock"></i>
                        </span>
                        <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-2 text-decoration-none text-muted" onclick="togglePassword(this)" style="z-index: 10;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="checkbox-label d-flex align-items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="form-check-input mt-0"> {{ __('Remember me') }}
                    </label>
                    <a href="{{ route('password.forgot') }}" class="forgot-password text-decoration-none fw-semibold">{{ __('Forgot Password?') }}</a>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" style="height: 50px; font-size: 1.1rem; border-radius: 8px;">
                    <i class="fas fa-sign-in-alt me-2"></i> {{ __('Login') }}
                </button>
            </form>

            <div class="auth-divider my-4 text-center position-relative">
                <hr class="position-absolute w-100 top-50 start-0 translate-middle-y z-n1">
                <span class="bg-white px-3 text-muted fw-medium">{{ __('Or login with') }}</span>
            </div>

            <div class="social-login d-grid gap-2">
                <a href="{{ route('social.redirect', 'google') }}" class="btn btn-outline-dark fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" style="height: 50px; border-radius: 8px; border-color: #ddd;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48" class="me-2">
                        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                    </svg>
                    <span>{{ __('Continue with Google') }}</span>
                </a>
            </div>

            <div class="text-center mt-4">
                <p class="auth-footer text-muted mb-0">
                    {{ __('Don\'t have an account?') }} <a href="{{ route('register') }}" class="fw-bold text-decoration-none">{{ __('Register Now') }}</a>
                </p>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function togglePassword(btn) {
    var input = btn.closest('.position-relative').querySelector('input');
    var icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
@endsection
