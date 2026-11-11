@extends('layouts.app')

@php
$pageTitle = 'Complete Profile';
$pageDescription = 'Add your phone number to complete registration.';
@endphp

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="auth-container" data-aos="fade-up">
            <div class="auth-header">
                <i class="fas fa-user-plus" style="font-size: 4rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                <h2>Complete Your Profile</h2>
                <p>Welcome, {{ $user->name }}! Please add your phone number to complete registration.</p>
            </div>

            @if(session('info'))
            <div class="alert alert-info" style="background: #dbeafe; color: #1e40af; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <i class="fas fa-info-circle"></i> {{ session('info') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('social.complete-profile') }}" method="POST" class="auth-form">
                @csrf
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Phone Number <span style="color: red;">*</span></label>
                    <input type="tel" 
                           name="phone" 
                           class="form-control" 
                           placeholder="Enter your phone number (e.g., 01012345678)"
                           value="{{ old('phone') }}"
                           style="width: 100%; padding: 0.8rem 1rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 1rem;"
                           required>
                    <small style="color: #666; display: block; margin-top: 0.5rem;">
                        We'll send a verification code to your WhatsApp.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i> Verify Phone & Continue
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #666; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i> Cancel and logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
