@extends('layouts.app')

@section('title', 'إتمام الشراء - Medicamo')

@section('content')
    <section class="checkout-page py-5">
        <div class="container">
            <h1 class="page-title mb-4">إتمام الشراء</h1>
            
            <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        {{-- Shipping Address --}}
                        <div class="checkout-section">
                            <h3><i class="fas fa-map-marker-alt"></i> عنوان التوصيل</h3>
                            
                            @if($addresses->count() > 0)
                            <div class="saved-addresses mb-3">
                                @foreach($addresses as $address)
                                <label class="address-option">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }}>
                                    <div class="address-card">
                                        <h5>{{ $address->label }}</h5>
                                        <p>{{ $address->full_name }} - {{ $address->phone }}</p>
                                        <p>{{ $address->full_address }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#newAddressForm">
                                <i class="fas fa-plus"></i> إضافة عنوان جديد
                            </button>
                            @endif
                            
                            <div class="collapse {{ $addresses->count() == 0 ? 'show' : '' }}" id="newAddressForm">
                                <div class="new-address-form mt-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">الاسم الكامل</label>
                                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', auth()->user()->name) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">رقم الهاتف</label>
                                            <input type="tel" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">العنوان</label>
                                            <input type="text" name="address_line_1" class="form-control" placeholder="الشارع، رقم المبنى، الشقة" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">المدينة</label>
                                            <input type="text" name="city" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">المحافظة</label>
                                            <select name="state" class="form-select" required>
                                                <option value="">اختر المحافظة</option>
                                                <option value="القاهرة">القاهرة</option>
                                                <option value="الجيزة">الجيزة</option>
                                                <option value="الإسكندرية">الإسكندرية</option>
                                                <option value="other">محافظات أخرى</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Payment Method --}}
                        <div class="checkout-section mt-4">
                            <h3><i class="fas fa-credit-card"></i> طريقة الدفع</h3>
                            <div class="payment-methods">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="cod" checked>
                                    <div class="payment-card">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <div>
                                            <h5>الدفع عند الاستلام</h5>
                                            <p>ادفع نقداً عند استلام طلبك</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        {{-- Order Notes --}}
                        <div class="checkout-section mt-4">
                            <h3><i class="fas fa-comment"></i> ملاحظات (اختياري)</h3>
                            <textarea name="notes" class="form-control" rows="3" placeholder="أي ملاحظات خاصة بالطلب..."></textarea>
                        </div>
                    </div>
                    
                    {{-- Order Summary --}}
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <h3>ملخص الطلب</h3>
                            
                            <div class="order-items">
                                @foreach($cart->items as $item)
                                <div class="order-item">
                                    <span class="item-name">{{ $item->product->name }} x {{ $item->quantity }}</span>
                                    <span class="item-price">EGP {{ number_format($item->total, 2) }}</span>
                                </div>
                                @endforeach
                            </div>
                            
                            <hr>
                            
                            <div class="summary-row">
                                <span>المجموع الفرعي</span>
                                <span>${{ number_format($cart->subtotal, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>الشحن</span>
                                <span>$50.00</span>
                            </div>
                            <hr>
                            <div class="summary-row total">
                                <span>الإجمالي</span>
                                <span>${{ number_format($cart->subtotal + 50, 2) }}</span>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                                <i class="fas fa-check"></i> تأكيد الطلب
                            </button>
                            
                            <p class="text-muted text-center mt-3">
                                <i class="fas fa-lock"></i> جميع البيانات مشفرة وآمنة
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
