@extends('layouts.app')

@php
$pageTitle = 'My Orders';
$pageDescription = 'View your order history at Medicamo.';
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">My Orders</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>/</li>
            <li><a href="{{ route('profile.index') }}">Profile</a></li>
            <li>/</li>
            <li>Orders</li>
        </ul>
    </div>
</section>

<!-- ==================== Orders Section ==================== -->
<section class="section">
    <div class="container">
        @if($orders->isEmpty())
        <div class="empty-state" data-aos="fade-up" style="text-align: center; padding: 4rem 2rem;">
            <i class="fas fa-shopping-bag" style="font-size: 5rem; color: #ddd; margin-bottom: 1.5rem;"></i>
            <h3>No Orders Yet</h3>
            <p style="color: #666; margin-bottom: 1.5rem;">Start shopping to see your orders here.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Start Shopping
            </a>
        </div>
        @else
        <div class="orders-list">
            @foreach($orders as $order)
            <div class="order-card" data-aos="fade-up" style="background: #fff; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <div class="order-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h4 style="margin: 0;">Order #{{ $order->order_number }}</h4>
                        <p style="color: #666; margin: 0.25rem 0 0;">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge badge-{{ $order->status_color }}" style="padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; background: {{ $order->status_color === 'success' ? '#10b981' : ($order->status_color === 'warning' ? '#f59e0b' : '#3b82f6') }}; color: #fff;">
                            {{ ucfirst($order->status) }}
                        </span>
                        <p style="margin: 0.5rem 0 0; font-weight: 600; font-size: 1.1rem;">EGP {{ number_format($order->total, 2) }}</p>
                    </div>
                </div>
                
                <!-- Order Items Preview -->
                <div class="order-items-preview" style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                    @foreach($order->items->take(3) as $item)
                    <div style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; background: #f5f5f5;">
                        @if($item->product && $item->product->images && count($item->product->images) > 0)
                        <img src="{{ asset('storage/' . $item->product->images[0]) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                        <img src="{{ asset('images/pro1.jpeg') }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @endif
                    </div>
                    @endforeach
                    @if($order->items->count() > 3)
                    <div style="width: 50px; height: 50px; border-radius: 8px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #666; font-size: 0.85rem;">
                        +{{ $order->items->count() - 3 }}
                    </div>
                    @endif
                </div>
                
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="{{ route('profile.order', $order) }}" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    @if($order->tracking_number)
                    <a href="{{ route('order.track', $order) }}" class="btn btn-outline" style="flex: 1;">
                        <i class="fas fa-truck"></i> Track Order
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        
        {{ $orders->links() }}
        @endif
    </div>
</section>
@endsection
