@extends('layouts.app')

@php
$pageTitle = 'Track Order #' . $order->order_number;
$pageDescription = 'Track your order status at Medicamo.';
$timeline = $order->getStatusTimeline();
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">Track Order</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>/</li>
            <li><a href="{{ route('profile.orders') }}">Orders</a></li>
            <li>/</li>
            <li>Track #{{ $order->order_number }}</li>
        </ul>
    </div>
</section>

<!-- ==================== Tracking Section ==================== -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Order Status Timeline -->
                <div class="tracking-card" data-aos="fade-up" style="background: #fff; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h3 style="margin-bottom: 2rem;"><i class="fas fa-route"></i> Order Status</h3>
                    
                    <div class="status-timeline" style="position: relative;">
                        @foreach($timeline as $index => $step)
                        <div class="timeline-step" style="display: flex; align-items: flex-start; margin-bottom: 2rem; position: relative;">
                            <!-- Vertical Line -->
                            @if($index < count($timeline) - 1)
                            <div style="position: absolute; left: 20px; top: 45px; width: 2px; height: calc(100% + 1rem); background: {{ $step['completed'] ? 'var(--secondary-green, #10b981)' : '#e5e7eb' }};"></div>
                            @endif
                            
                            <!-- Icon -->
                            <div style="width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; {{ $step['completed'] ? 'background: var(--secondary-green, #10b981); color: #fff;' : 'background: #e5e7eb; color: #9ca3af;' }} font-size: 1rem; z-index: 1;">
                                <i class="fas {{ $step['icon'] }}"></i>
                            </div>
                            
                            <!-- Content -->
                            <div style="margin-left: 1rem; flex: 1;">
                                <h5 style="margin: 0; {{ $step['completed'] ? 'color: var(--secondary-green, #10b981);' : 'color: #9ca3af;' }}">
                                    {{ $step['label'] }}
                                    @if($step['completed'] && $order->status === $step['status'])
                                    <span style="background: var(--secondary-green, #10b981); color: #fff; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; margin-left: 0.5rem;">Current</span>
                                    @endif
                                </h5>
                                @if($step['date'])
                                <p style="margin: 0.25rem 0 0; color: #666; font-size: 0.9rem;">
                                    {{ $step['date']->format('M d, Y h:i A') }}
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($order->tracking_number)
                    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                        <h5><i class="fas fa-truck"></i> Tracking Information</h5>
                        <p style="margin: 0.5rem 0;"><strong>Carrier:</strong> {{ $order->shipping_carrier ?? 'Standard Shipping' }}</p>
                        <p style="margin: 0.5rem 0;"><strong>Tracking Number:</strong> 
                            <span style="font-family: monospace; background: #f5f5f5; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ $order->tracking_number }}</span>
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="order-summary-card" data-aos="fade-left" style="background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h4>Order Summary</h4>
                    <p style="color: #666; margin-bottom: 1rem;">{{ $order->order_number }}</p>
                    
                    <div class="order-items" style="max-height: 200px; overflow-y: auto; margin-bottom: 1rem;">
                        @foreach($order->items as $item)
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0; border-bottom: 1px solid #f5f5f5;">
                            <div style="width: 40px; height: 40px; border-radius: 6px; overflow: hidden; background: #f5f5f5; flex-shrink: 0;">
                                @if($item->product && $item->product->images && count($item->product->images) > 0)
                                <img src="{{ asset('storage/' . $item->product->images[0]) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                <img src="{{ asset('images/pro1.jpeg') }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <p style="margin: 0; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item->product->name ?? 'Product' }}</p>
                                <small style="color: #666;">Qty: {{ $item->quantity }}</small>
                            </div>
                            <span style="font-weight: 600;">EGP {{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <div style="border-top: 2px solid #e5e7eb; padding-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Subtotal</span>
                            <span>${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: green;">
                            <span>Discount</span>
                            <span>-${{ number_format($order->discount, 2) }}</span>
                        </div>
                        @endif
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Shipping</span>
                            <span>${{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.1rem; padding-top: 0.5rem; border-top: 1px solid #e5e7eb;">
                            <span>Total</span>
                            <span>${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Need Help -->
                <div class="help-card" data-aos="fade-left" data-aos-delay="100" style="background: #fff; border-radius: 12px; padding: 1.5rem; margin-top: 1rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h5><i class="fas fa-headset"></i> Need Help?</h5>
                    <p style="color: #666; font-size: 0.9rem;">Contact our support team for any questions about your order.</p>
                    <a href="{{ route('contact') }}" class="btn btn-outline" style="width: 100%;">
                        <i class="fas fa-envelope"></i> Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
