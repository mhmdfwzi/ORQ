@extends('layouts.app')

@php
$pageTitle = __('My Profile');
$pageDescription = app()->getLocale() == 'ar' ? 'إدارة حسابك في ميديكامو وعرض طلباتك.' : 'Manage your Medicamo account and view your orders.';
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">{{ __('My Profile') }}</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li>/</li>
            <li>{{ __('My Profile') }}</li>
        </ul>
    </div>
</section>

<!-- ==================== Profile Section ==================== -->
<section class="profile-section">
    <div class="container">
        <div class="profile-layout">
            <!-- Sidebar -->
            <div class="profile-sidebar" data-aos="fade-right">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <i class="fas fa-user-circle" style="font-size: 80px; color: var(--primary-blue);"></i>
                    </div>
                    <h3 class="profile-name">{{ auth()->user()->name }}</h3>
                    <p class="profile-email">{{ auth()->user()->email }}</p>
                    <span class="member-badge">
                        <i class="fas fa-crown"></i> Member
                    </span>
                </div>

                <nav class="profile-nav">
                    <a href="#dashboard" class="profile-nav-link active" onclick="showPanel('dashboard')">
                        <i class="fas fa-home"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                    <a href="#orders" class="profile-nav-link" onclick="showPanel('orders')">
                        <i class="fas fa-shopping-bag"></i>
                        <span>{{ __('My Orders') }}</span>
                        <span class="nav-badge">{{ $ordersCount ?? 0 }}</span>
                    </a>
                    <a href="#settings" class="profile-nav-link" onclick="showPanel('settings')">
                        <i class="fas fa-user-cog"></i>
                        <span>{{ __('Account Settings') }}</span>
                    </a>
                    <a href="#addresses" class="profile-nav-link" onclick="showPanel('addresses')">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ __('Addresses') }}</span>
                    </a>
                    <a href="#wishlist" class="profile-nav-link" onclick="showPanel('wishlist')">
                        <i class="fas fa-heart"></i>
                        <span>{{ __('Wishlist') }}</span>
                        <span class="nav-badge">{{ $wishlistCount ?? 0 }}</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="profile-nav-link logout" style="width: 100%; text-align: left; background: none; border: none;">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>{{ __('Logout') }}</span>
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="profile-content" data-aos="fade-left">
                <!-- Dashboard Section -->
                <div id="dashboard" class="profile-panel active">
                    <div class="panel-header">
                        <h2><i class="fas fa-home"></i> {{ __('Dashboard') }}</h2>
                        <p>{{ __('Welcome back') }}, {{ auth()->user()->name }}! {{ __('Here\'s your account overview.') }}</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="dashboard-stats">
                        <a href="#orders" class="stat-card" onclick="filterOrders('all'); showPanel('orders');">
                            <div class="stat-icon orders">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="stat-info">
                                <h4>{{ $totalOrders ?? 0 }}</h4>
                                <p>{{ __('Total Orders') }}</p>
                            </div>
                        </a>
                        <a href="#orders" class="stat-card" onclick="filterOrders('pending'); showPanel('orders');">
                            <div class="stat-icon pending">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-info">
                                <h4>{{ $pendingOrders ?? 0 }}</h4>
                                <p>{{ __('Pending') }}</p>
                            </div>
                        </a>
                        <a href="#orders" class="stat-card" onclick="filterOrders('delivered'); showPanel('orders');">
                            <div class="stat-icon delivered">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-info">
                                <h4>{{ $deliveredOrders ?? 0 }}</h4>
                                <p>{{ __('Delivered') }}</p>
                            </div>
                        </a>
                        <a href="#wishlist" class="stat-card" onclick="showPanel('wishlist');">
                            <div class="stat-icon wishlist">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="stat-info">
                                <h4>{{ $wishlistCount ?? 0 }}</h4>
                                <p>{{ __('Wishlist') }}</p>
                            </div>
                        </a>
                    </div>

                    <!-- Recent Orders -->
                    <div class="recent-orders">
                        <div class="section-header">
                            <h3>{{ __('Recent Orders') }}</h3>
                            <a href="#orders" class="view-all-link" onclick="showPanel('orders')">{{ __('View All') }} <i class="fas fa-arrow-right"></i></a>
                        </div>

                        <div class="orders-list">
                            @forelse($recentOrders ?? [] as $order)
                            <div class="order-item">
                                <div class="order-product">
                                    <div class="order-details">
                                        <h4>{{ __('Order') }} #{{ $order->order_number }}</h4>
                                        <p>{{ $order->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="order-total">EGP {{ number_format($order->total, 2) }}</div>
                                <span class="order-status {{ strtolower($order->status) }}">{{ __(ucfirst($order->status)) }}</span>
                            </div>
                            @empty
                            <p>{{ __('No orders yet.') }} <a href="{{ route('products.index') }}">{{ __('Start shopping!') }}</a></p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Orders Section -->
                <div id="orders" class="profile-panel">
                    <div class="panel-header">
                        <h2><i class="fas fa-shopping-bag"></i> {{ __('My Orders') }}</h2>
                        <p>{{ __('View and track all your orders') }}</p>
                    </div>

                    <div class="orders-list">
                        @forelse($orders ?? [] as $order)
                        <div class="order-item">
                            <div class="order-product">
                                <div class="order-details">
                                    <h4>{{ __('Order') }} #{{ $order->order_number }}</h4>
                                    <p>{{ $order->created_at->format('M d, Y') }} - {{ $order->items->count() }} {{ __('items') }}</p>
                                </div>
                            </div>
                            <div class="order-total">EGP {{ number_format($order->total, 2) }}</div>
                            <span class="order-status {{ strtolower($order->status) }}">{{ __(ucfirst($order->status)) }}</span>
                        </div>
                        @empty
                        <p>{{ __('No orders yet.') }} <a href="{{ route('products.index') }}">{{ __('Start shopping!') }}</a></p>
                        @endforelse
                    </div>
                </div>

                <!-- Settings Section -->
                <div id="settings" class="profile-panel">
                    <div class="panel-header">
                        <h2><i class="fas fa-user-cog"></i> {{ __('Account Settings') }}</h2>
                        <p>{{ __('Update your personal information') }}</p>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" class="settings-form">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>{{ __('Full Name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Email') }}</label>
                            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Phone') }}</label>
                            <input type="tel" name="phone" class="form-control" value="{{ auth()->user()->phone }}">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Save Changes') }}
                        </button>
                    </form>
                </div>

                <!-- Addresses Section -->
                <div id="addresses" class="profile-panel">
                    <div class="panel-header">
                        <h2><i class="fas fa-map-marker-alt"></i> {{ __('My Addresses') }}</h2>
                        <p>{{ __('Manage your delivery addresses') }}</p>
                    </div>

                    <div class="addresses-list">
                        @forelse($addresses ?? [] as $address)
                        <div class="address-card">
                            <h4>{{ $address->label ?? __('Home') }}</h4>
                            <p>{{ $address->full_address }}</p>
                            @if($address->is_default)
                            <span class="default-badge">{{ __('Default') }}</span>
                            @endif
                        </div>
                        @empty
                        <p>{{ __('No addresses saved yet.') }}</p>
                        @endforelse
                    </div>
                </div>

                <!-- Wishlist Section -->
                <div id="wishlist" class="profile-panel">
                    <div class="panel-header">
                        <h2><i class="fas fa-heart"></i> {{ __('My Wishlist') }}</h2>
                        <p>{{ __('Products you\'ve saved for later') }}</p>
                    </div>

                    <div class="products-grid">
                        @forelse($wishlist ?? [] as $item)
                        @php
                            $wishlistItemName = app()->getLocale() == 'ar' ? ($item->product->name_ar ?? $item->product->name) : $item->product->name;
                        @endphp
                        <a href="{{ route('products.show', $item->product->slug) }}" class="product-card">
                            <div class="product-image">
                                @if($item->product->images && count($item->product->images) > 0)
                                <img src="{{ asset('storage/' . $item->product->images[0]) }}" alt="{{ $wishlistItemName }}">
                                @else
                                <img src="{{ asset('images/pro1.jpeg') }}" alt="{{ $wishlistItemName }}">
                                @endif
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">{{ $wishlistItemName }}</h3>
                                <span class="product-price">{{ number_format($item->product->current_price, 2) }} EGP</span>
                            </div>
                        </a>
                        @empty
                        <p>{{ __('Your wishlist is empty.') }} <a href="{{ route('products.index') }}">{{ __('Browse products') }}</a></p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function showPanel(panelId) {
    // Hide all panels
    document.querySelectorAll('.profile-panel').forEach(panel => {
        panel.classList.remove('active');
    });
    // Show selected panel
    document.getElementById(panelId).classList.add('active');
    // Update nav
    document.querySelectorAll('.profile-nav-link').forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + panelId) {
            link.classList.add('active');
        }
    });
}

function filterOrders(status) {
    const orders = document.querySelectorAll('#orders .order-item');
    orders.forEach(order => {
        const orderStatus = order.querySelector('.order-status');
        if (status === 'all' || (orderStatus && orderStatus.classList.contains(status))) {
            order.style.display = 'flex';
        } else {
            order.style.display = 'none';
        }
    });
}
</script>
@endpush

@push('styles')
<style>
    a.stat-card {
        text-decoration: none;
        color: inherit;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: block;
    }
    a.stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    #wishlist a.product-card {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    #wishlist a.product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
@endpush
@endsection
