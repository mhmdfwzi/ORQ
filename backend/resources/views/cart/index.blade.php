@extends('layouts.app')

@php
$pageTitle = __('Shopping Cart');
$pageDescription = app()->getLocale() == 'ar' ? 'عرض وإدارة المنتجات في سلة التسوق الخاصة بك' : 'View and manage items in your shopping cart.';
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">{{ __('Shopping Cart') }}</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li>/</li>
            <li>{{ __('Cart') }}</li>
        </ul>
    </div>
</section>

<!-- ==================== Cart Section ==================== -->
<section class="cart-section">
    <div class="container">
        @if($cart && $cart->items->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <table class="cart-table" data-aos="fade-up">
                    <thead>
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart->items as $item)
                        @php
                            $itemName = app()->getLocale() == 'ar' ? ($item->product->name_ar ?? $item->product->name) : $item->product->name;
                            $catName = app()->getLocale() == 'ar' ? ($item->product->category->name_ar ?? $item->product->category->name) : ($item->product->category->name ?? '');
                        @endphp
                        <tr>
                            <td data-label="{{ __('Product') }}">
                                <div class="cart-product">
                                    <div class="cart-product-image">
                                        @if($item->product->images && count($item->product->images) > 0)
                                        <img src="{{ asset('storage/' . $item->product->images[0]) }}" alt="{{ $itemName }}">
                                        @else
                                        <img src="{{ asset('images/pro1.jpeg') }}" alt="{{ $itemName }}">
                                        @endif
                                    </div>
                                    <div class="cart-product-info">
                                        <h4>{{ $itemName }}</h4>
                                        <span>{{ $catName }}</span>
                                    </div>
                                </div>
                            </td>
                            <td data-label="{{ __('Price') }}">
                                <span class="cart-price">{{ number_format($item->price, 2) }} EGP</span>
                            </td>
                            <td data-label="{{ __('Quantity') }}">
                                <div class="quantity-controls" data-item-id="{{ $item->id }}" data-update-url="{{ route('cart.update', $item) }}">
                                    <button type="button" class="quantity-btn minus">−</button>
                                    <input type="number" class="quantity-input" value="{{ $item->quantity }}" min="1" readonly>
                                    <button type="button" class="quantity-btn plus">+</button>
                                </div>
                            </td>
                            <td data-label="{{ __('Total') }}">
                                <span class="cart-total-price">{{ number_format($item->price * $item->quantity, 2) }} EGP</span>
                            </td>
                            <td data-label="{{ __('Action') }}">
                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="remove-btn"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="cart-actions" data-aos="fade-up">
                    <a href="{{ route('products.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> {{ __('Continue Shopping') }}
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart-summary" data-aos="fade-left">
                    <h3>{{ __('Order Summary') }}</h3>
                    <div class="summary-row">
                        <span>{{ __('Subtotal') }}</span>
                        <span class="summary-value">{{ number_format($cart->subtotal, 2) }} EGP</span>
                    </div>
                    <div class="summary-row">
                        <span>{{ __('Shipping') }}</span>
                        <span class="summary-value">{{ number_format($shipping ?? 5.00, 2) }} EGP</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>{{ __('Total') }}</span>
                        <span class="summary-value">{{ number_format($cart->subtotal + ($shipping ?? 5.00), 2) }} EGP</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-credit-card"></i> {{ __('Proceed to Checkout') }}
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="empty-cart" data-aos="fade-up">
            <i class="fas fa-shopping-cart" style="font-size: 5rem; color: var(--medium-gray); margin-bottom: 2rem;"></i>
            <h2>{{ __('Your cart is empty') }}</h2>
            <p>{{ __('Looks like you haven\'t added any products to your cart yet.') }}</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> {{ __('Start Shopping') }}
            </a>
        </div>
        @endif
    </div>
</section>
@endsection
