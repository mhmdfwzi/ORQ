@extends('layouts.app')

@php
$pageTitle = 'Verify OTP';
@endphp

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card" data-aos="fade-up">
                <div class="auth-header">
                    <div class="auth-icon auth-icon-otp">
                        <i class="fas fa-sms"></i>
                    </div>
                    <h2>Enter Verification Code</h2>
                    <p>We sent a code to <strong>{{ $phone }}</strong></p>
                </div>

                @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ $formAction }}" class="auth-form">
                    @csrf
                    <input type="hidden" name="phone" value="{{ $phone }}">
                    
                    <div class="form-group">
                        <label for="otp">6-Digit Code</label>
                        <div class="otp-inputs">
                            <input type="text" 
                                   id="otp" 
                                   name="otp" 
                                   class="otp-input" 
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   placeholder="------"
                                   autocomplete="one-time-code"
                                   inputmode="numeric"
                                   required>
                        </div>
                    </div>

                    <button type="submit" class="btn-auth-primary">
                        <i class="fas fa-check"></i>
                        Verify Code
                    </button>
                </form>

                <div class="otp-resend">
                    <p>Didn't receive the code?</p>
                    <form method="POST" action="{{ $resendAction }}">
                        @csrf
                        <input type="hidden" name="phone" value="{{ $phone }}">
                        <button type="submit" class="btn-resend">
                            <i class="fas fa-redo"></i>
                            Resend Code
                        </button>
                    </form>
                </div>

                <div class="auth-footer">
                    <a href="{{ $backRoute }}">
                        <i class="fas fa-arrow-left"></i> {{ $backLabel ?? 'Change Phone Number' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.auth-section {
    min-height: calc(100vh - 150px);
    display: flex;
    align-items: center;
    padding: 2rem 0;
    background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);
}

.auth-container {
    max-width: 420px;
    margin: 0 auto;
    width: 100%;
}

.auth-card {
    background: #fff;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--secondary-green), #059669);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.auth-icon i {
    font-size: 1.75rem;
    color: #fff;
}

.auth-header h2 {
    color: var(--primary-blue);
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.auth-header p {
    color: var(--medium-gray);
    font-size: 0.95rem;
    margin: 0;
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: var(--primary-blue);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-align: center;
}

.otp-input {
    width: 100%;
    padding: 1rem;
    border: 2px solid var(--light-gray);
    border-radius: 12px;
    font-size: 1.75rem;
    font-weight: 700;
    text-align: center;
    letter-spacing: 0.5rem;
    transition: all 0.3s ease;
}

.otp-input:focus {
    outline: none;
    border-color: var(--secondary-green);
}

.btn-auth-primary {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, var(--secondary-green), #059669);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-auth-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
}

.otp-resend {
    text-align: center;
    margin-top: 1.5rem;
}

.otp-resend p {
    color: var(--medium-gray);
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.btn-resend {
    background: none;
    border: none;
    color: var(--primary-blue);
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-resend:hover {
    color: var(--secondary-green);
}

.auth-footer {
    text-align: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--light-gray);
}

.auth-footer a {
    color: var(--medium-gray);
    font-size: 0.9rem;
}

.alert {
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
}
</style>
@endpush
