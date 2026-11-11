@extends('layouts.app')

@php
$pageTitle = __('Checkout');
$pageDescription = app()->getLocale() == 'ar' ? 'أكمل طلبك في ميديكامو.' : 'Complete your order at Medicamo.';
$couponDiscount = session('coupon_discount', 0);
$couponCode = session('coupon_code');
@endphp

@section('content')
@push('styles')
<style>
    .coupon-section {
        background-color: white !important;
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1) !important;
    }
    .coupon-section label {
        color: #111827 !important; /* Dark black/gray */
        font-size: 1.1rem !important;
    }
    #couponCodeInput {
        background-color: #f9fafb !important;
        border: 1px solid #d1d5db !important;
        color: #111827 !important;
    }
    #couponCodeInput::placeholder {
        color: #6b7280 !important;
    }
    #applyCouponBtn {
        background-color: #1e3a8a !important; /* Primary blue */
        color: white !important;
        border: 1px solid #1e3a8a !important;
        font-weight: 600 !important;
    }
    #applyCouponBtn:hover {
        background-color: #1e40af !important;
        color: white !important;
    }
</style>
@endpush

<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">{{ __('Checkout') }}</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li>/</li>
            <li><a href="{{ route('cart.index') }}">{{ __('Cart') }}</a></li>
            <li>/</li>
            <li>{{ __('Checkout') }}</li>
        </ul>
    </div>
</section>

<!-- ==================== Checkout Section ==================== -->
<section class="checkout-section">
    <div class="container">
        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
            @csrf
            <input type="hidden" name="coupon_id" id="couponIdInput" value="{{ session('coupon_id') }}">
            <div class="row">
                <div class="col-lg-8">
                    <div class="checkout-form" data-aos="fade-up">
                        <h3><i class="fas fa-user"></i> {{ __('Customer Details') }}</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>{{ __('First Name') }} <span style="color: red;">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', auth()->user()->name ?? '') }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Last Name') }}</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Email') }} <span style="color: red;">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Phone Number') }} <span style="color: red;">*</span></label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                            @error('phone') <small style="color:red;display:block;margin-top:4px;">{{ $message }}</small> @enderror
                        </div>

                        <h3 style="margin-top: 2rem;"><i class="fas fa-map-marker-alt"></i> {{ __('Delivery Address') }}</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>{{ __('Governorate') }} <span style="color: red;">*</span></label>
                                <select name="governorate_id" id="governorateSelect" class="form-control" required>
                                    <option value="">{{ __('Select Governorate') }}</option>
                                    @foreach($governorates as $gov)
                                        <option value="{{ $gov->id }}" {{ (old('governorate_id') == $gov->id || (auth()->user()->governorate_id == $gov->id)) ? 'selected' : '' }}>
                                            {{ $gov->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ __('City') }} <span style="color: red;">*</span></label>
                                <select name="city_id" id="citySelect" class="form-control" required disabled>
                                    <option value="">{{ __('Select City') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Street Address') }} <span style="color: red;">*</span></label>
                            <input type="text" name="street_address" class="form-control" value="{{ old('street_address', auth()->user()->street_address ?? '') }}" required>
                        </div>
                        
                        <!-- Hidden fields for dynamic data -->
                        <input type="hidden" id="selectedShippingCost" value="{{ $shipping }}">

                        <h3 style="margin-top: 2rem;"><i class="fas fa-credit-card"></i> {{ __('Payment Method') }}</h3>
                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cod" checked>
                                <span><i class="fas fa-money-bill-wave"></i> {{ __('Cash on Delivery') }}</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card">
                                <span><i class="fas fa-credit-card"></i> {{ __('Credit/Debit Card') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-summary" data-aos="fade-left">
                        <h3>{{ __('Order Summary') }}</h3>
                        <div class="order-items">
                            @if($cart && $cart->items)
                            @foreach($cart->items as $item)
                            @php
                                $itemName = app()->getLocale() == 'ar' ? ($item->product->name_ar ?? $item->product->name) : $item->product->name;
                            @endphp
                            <div class="order-item">
                                <span>{{ $itemName }} x{{ $item->quantity }}</span>
                                <span>{{ number_format($item->price * $item->quantity, 2) }} EGP</span>
                            </div>
                            @endforeach
                            @endif
                        </div>

                        <!-- Coupon Section -->
                        <div class="coupon-section" style="margin: 1rem 0; padding: 1rem; background: #f5f5f5; border-radius: 8px;">
                            <label style="font-weight: 600; margin-bottom: 0.5rem; display: block;">
                                <i class="fas fa-ticket-alt"></i> {{ __('Have a Coupon?') }}
                            </label>
                            <div id="couponInputContainer" style="{{ $couponCode ? 'display:none' : '' }}">
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="text" id="couponCodeInput" class="form-control" placeholder="{{ __('Enter code') }}" style="flex: 1;">
                                    <button type="button" id="applyCouponBtn" class="btn btn-outline" style="white-space: nowrap;">
                                        {{ __('Apply') }}
                                    </button>
                                </div>
                                <p id="couponError" style="color: red; font-size: 0.85rem; margin-top: 0.5rem; display: none;"></p>
                            </div>
                            <div id="couponApplied" style="{{ $couponCode ? '' : 'display:none' }}">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="color: green;"><i class="fas fa-check-circle"></i> <strong id="appliedCouponCode">{{ $couponCode }}</strong></span>
                                    <button type="button" id="removeCouponBtn" class="btn" style="color: red; padding: 0;">
                                        <i class="fas fa-times"></i> {{ __('Remove') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="summary-row">
                            <span>{{ __('Subtotal') }}</span>
                            <span id="subtotalAmount">{{ number_format($cart->subtotal ?? 0, 2) }} EGP</span>
                        </div>
                        <div class="summary-row" id="discountRow" style="{{ $couponDiscount > 0 ? '' : 'display:none' }}">
                            <span style="color: green;">{{ __('Discount') }}</span>
                            <span style="color: green;" id="discountAmount">-{{ number_format($couponDiscount, 2) }} EGP</span>
                        </div>
                        <div class="summary-row">
                            <span>{{ __('Shipping') }}</span>
                            <span id="shippingDisplay">{{ number_format($shipping ?? 0, 2) }} EGP</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>{{ __('Total') }}</span>
                            <span id="totalAmount">{{ number_format(($cart->subtotal ?? 0) - $couponDiscount + ($shipping ?? 0), 2) }} EGP</span>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-check"></i> {{ __('Place Order') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const subtotal = {{ $cart->subtotal ?? 0 }};
    let shipping = {{ $shipping ?? 0 }};
    let discount = {{ $couponDiscount ?? 0 }};
    
    // Elements
    const governorateSelect = document.getElementById('governorateSelect');
    const citySelect = document.getElementById('citySelect');
    const shippingDisplay = document.getElementById('shippingDisplay');
    const totalAmount = document.getElementById('totalAmount');
    
    // User saved city ID
    const savedCityId = "{{ old('city_id', auth()->user()->city_id ?? '') }}";
    
    // Function to update total
    function updateTotal() {
        const total = subtotal - discount + shipping;
        totalAmount.textContent = total.toFixed(2) + ' EGP';
    }
    
    // Load cities when governorate changes
    governorateSelect.addEventListener('change', function() {
        const govId = this.value;
        citySelect.innerHTML = '<option value="">{{ __("Select City") }}</option>';
        citySelect.disabled = true;
        
        if (govId) {
            // Fetch cities
            fetch(`/api/destinations/${govId}/children`)
                .then(res => res.json())
                .then(cities => {
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        option.dataset.price = city.price;
                        
                        if (savedCityId && city.id == savedCityId) {
                            option.selected = true;
                            // Update shipping if this is the saved city
                            shipping = parseFloat(city.price || 0);
                            shippingDisplay.textContent = shipping.toFixed(2) + ' EGP';
                            updateTotal();
                        }
                        
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                    
                    // Trigger change if we just set the saved city
                    if (citySelect.value) {
                         // Already handled inside loop
                    }
                });
        }
    });
    
    // Update shipping when city changes
    citySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            shipping = parseFloat(selectedOption.dataset.price || 0);
            shippingDisplay.textContent = shipping.toFixed(2) + ' EGP';
            updateTotal();
        }
    });

    // Trigger initial load if governorate selected
    if (governorateSelect.value) {
        governorateSelect.dispatchEvent(new Event('change'));
    }
    
    // Apply Coupon
    document.getElementById('applyCouponBtn').addEventListener('click', function() {
        const code = document.getElementById('couponCodeInput').value.trim();
        if (!code) return;
        
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        this.disabled = true;
        
        fetch('{{ route("coupon.apply") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code: code })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('couponInputContainer').style.display = 'none';
                document.getElementById('couponApplied').style.display = 'block';
                document.getElementById('appliedCouponCode').textContent = data.coupon.code;
                document.getElementById('couponIdInput').value = data.coupon_id || '';
                document.getElementById('discountRow').style.display = 'flex';
                document.getElementById('discountAmount').textContent = '-' + data.discount_formatted;
                
                discount = data.discount;
                updateTotal();
                
                document.getElementById('couponError').style.display = 'none';
            } else {
                document.getElementById('couponError').textContent = data.message;
                document.getElementById('couponError').style.display = 'block';
            }
        })
        .catch(err => {
            document.getElementById('couponError').textContent = 'Error applying coupon';
            document.getElementById('couponError').style.display = 'block';
        })
        .finally(() => {
            this.innerHTML = '{{ __("Apply") }}';
            this.disabled = false;
        });
    });
    
    // Remove Coupon
    document.getElementById('removeCouponBtn').addEventListener('click', function() {
        fetch('{{ route("coupon.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('couponInputContainer').style.display = 'block';
            document.getElementById('couponApplied').style.display = 'none';
            document.getElementById('couponCodeInput').value = '';
            document.getElementById('couponIdInput').value = '';
            document.getElementById('discountRow').style.display = 'none';
            
            discount = 0;
            updateTotal();
        });
    });
    
    // Payment Method Check
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            if (paymentMethod && paymentMethod.value === 'card') {
                e.preventDefault();
                
                // Show Coming Soon message
                const message = '{{ app()->getLocale() == "ar" ? "خدمة الدفع بالبطاقة ستتوفر قريباً. يرجى اختيار الدفع عند الاستلام." : "Credit Card payment service will be available soon. Please choose Cash on Delivery." }}';
                
                // Use SweetAlert if available, otherwise standard alert
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: '{{ __("Coming Soon") }}',
                        text: message,
                        confirmButtonColor: '#1e3a8a',
                        confirmButtonText: '{{ __("OK") }}'
                    });
                } else {
                    alert(message);
                }
            }
        });
    }
});
</script>
@endpush
@endsection

