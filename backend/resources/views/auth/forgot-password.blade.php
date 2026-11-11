@extends('layouts.app')

@php
$pageTitle = 'Forgot Password';
@endphp

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card" data-aos="fade-up">
                <div class="auth-header">
                    <div class="auth-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h2>Forgot Password?</h2>
                    <p>Enter your phone number to receive a reset code</p>
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

                <form method="POST" action="{{ route('password.send-otp') }}" class="auth-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-control" 
                                   placeholder="01XXXXXXXXX"
                                   value="{{ old('phone') }}"
                                   required>
                        </div>
                    </div>

                    <button type="submit" class="btn-auth-primary">
                        <i class="fas fa-paper-plane"></i>
                        Send Reset Code
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Remember your password? <a href="{{ route('login') }}">Login</a></p>
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
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-green));
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
}

.input-with-icon {
    position: relative;
}

.input-with-icon i {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--medium-gray);
}

.input-with-icon input {
    width: 100%;
    padding: 0.875rem 1rem;
    padding-right: 3rem;
    border: 2px solid var(--light-gray);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.input-with-icon input:focus {
    outline: none;
    border-color: var(--primary-blue);
}

.btn-auth-primary {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, var(--primary-blue), #1e3a8a);
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
    box-shadow: 0 6px 20px rgba(30, 58, 138, 0.3);
}

.auth-footer {
    text-align: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--light-gray);
}

.auth-footer a {
    color: var(--primary-blue);
    font-weight: 600;
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
