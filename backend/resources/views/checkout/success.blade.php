@extends('layouts.app')

@php
$pageTitle = 'Order Confirmed';
$pageDescription = 'Your order has been placed successfully.';
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">Order Confirmed</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>/</li>
            <li>Order Confirmation</li>
        </ul>
    </div>
</section>

<!-- ==================== Confirmation Section ==================== -->
<section class="confirmation-section">
    <div class="container">
        <div class="confirmation-card" data-aos="fade-up">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Thank You for Your Order!</h2>
            <p>Your order has been placed successfully. We will notify you via email and WhatsApp with the shipping details.</p>

            <div class="order-number">
                <span>Order Number:</span>
                <strong>#{{ $order->order_number }}</strong>
            </div>

            <div class="order-details">
                <div class="detail-item">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <strong>Order Date</strong>
                        <span>{{ $order->created_at->format('F j, Y') }}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-dollar-sign"></i>
                    <div>
                        <strong>Total Amount</strong>
                        <span>${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-credit-card"></i>
                    <div>
                        <strong>Payment Method</strong>
                        <span>
                            @switch($order->payment_method)
                                @case('cod')
                                    Cash on Delivery
                                    @break
                                @case('card')
                                    Credit Card
                                    @break
                                @case('paypal')
                                    PayPal
                                    @break
                                @default
                                    {{ ucfirst($order->payment_method) }}
                            @endswitch
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-shipping-fast"></i>
                    <div>
                        <strong>Expected Delivery</strong>
                        <span>3-5 Business Days</span>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="order-items-summary" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee;">
                <h4><i class="fas fa-shopping-bag"></i> Order Items</h4>
                <table style="width: 100%; margin-top: 1rem;">
                    @foreach($order->items as $item)
                    <tr>
                        <td style="padding: 0.5rem 0;">{{ $item->product_name }} x {{ $item->quantity }}</td>
                        <td style="text-align: right; padding: 0.5rem 0;">EGP {{ number_format($item->total_price, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr style="border-top: 1px solid #ddd; font-weight: bold;">
                        <td style="padding-top: 1rem;">Total</td>
                        <td style="text-align: right; padding-top: 1rem;">EGP {{ number_format($order->total, 2) }}</td>
                    </tr>
                </table>
            </div>

            <div class="confirmation-actions">
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-outline">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
