@extends('layouts.app')

@php
$pageTitle = __('Register');
$pageDescription = app()->getLocale() == 'ar' ? 'إنشاء حساب جديد في ميديكامو' : 'Create your Medicamo account.';
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">{{ __('Create Account') }}</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li>/</li>
            <li>{{ __('Register') }}</li>
        </ul>
    </div>
</section>

<!-- ==================== Register Section ==================== -->
<section class="auth-section">
    <div class="container">
        <div class="auth-card" data-aos="fade-up">
            <h2>{{ __('Create New Account') }}</h2>
            <p class="auth-subtitle">{{ __('Join us to enjoy exclusive offers') }}</p>

            @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>{{ __('Full Name') }} <span style="color: red;">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="{{ __('Enter your full name') }}" value="{{ old('name') }}" required>
                </div>

                @php
                    $emailRequired = \App\Models\Setting::getValue('registration_email_required', 1);
                @endphp

                <div class="form-group">
                    <label>{{ __('Email') }} @if($emailRequired) <span style="color: red;">*</span> @endif</label>
                    <input type="email" name="email" class="form-control" placeholder="{{ __('Enter your email') }}" value="{{ old('email') }}" @if($emailRequired) required @endif>
                </div>

                <div class="form-group">
                    <label>{{ __('Phone Number') }} <span style="color: red;">*</span></label>
                    <input type="tel" name="phone" class="form-control" placeholder="+20 xxx xxx xxxx" value="{{ old('phone') }}" required>
                </div>

                <div class="form-group">
                    <label>{{ __('Password') }} <span style="color: red;">*</span></label>
                    <div class="password-toggle">
                        <input type="password" name="password" class="form-control" placeholder="{{ __('Enter password') }}" required>
                        <button type="button" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ __('Confirm Password') }} <span style="color: red;">*</span></label>
                    <div class="password-toggle">
                        <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('Confirm password') }}" required>
                        <button type="button" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms" required> {{ __('I agree to the') }} <a href="{{ route('terms') }}">{{ __('Terms & Conditions') }}</a> {{ __('and') }}
                        <a href="{{ route('privacy') }}">{{ __('Privacy Policy') }}</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-user-plus"></i> {{ __('Create Account') }}
                </button>
            </form>

            <div class="auth-divider">
                <span>{{ __('Or register with') }}</span>
            </div>

            <div class="social-login">
                <a href="{{ route('social.redirect', 'google') }}" class="social-btn google">
                    <i class="fab fa-google"></i> Google
                </a>
                <a href="{{ route('social.redirect', 'facebook') }}" class="social-btn facebook">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
            </div>

            <p class="auth-footer">
                {{ __('Already have an account?') }} <a href="{{ route('login') }}">{{ __('Login') }}</a>
            </p>
        </div>
    </div>
</section>

@push('scripts')
<script>
function togglePassword(btn) {
    var input = btn.previousElementSibling;
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
