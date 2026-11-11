@extends('layouts.app')

@php
$pageTitle = 'My Wishlist';
$pageDescription = 'Your saved products at Medicamo.';
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">My Wishlist</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>/</li>
            <li>Wishlist</li>
        </ul>
    </div>
</section>

<!-- ==================== Wishlist Section ==================== -->
<section class="section">
    <div class="container">
        @if($wishlistItems->isEmpty())
        <div class="empty-state" data-aos="fade-up" style="text-align: center; padding: 4rem 2rem;">
            <i class="fas fa-heart" style="font-size: 5rem; color: #ddd; margin-bottom: 1.5rem;"></i>
            <h3>Your Wishlist is Empty</h3>
            <p style="color: #666; margin-bottom: 1.5rem;">Save your favorite products to view them later.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Browse Products
            </a>
        </div>
        @else
        <div class="wishlist-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
            @foreach($wishlistItems as $item)
            <div class="wishlist-item" data-product-id="{{ $item->product->id }}" data-aos="fade-up">
                <div class="product-card">
                    <div class="product-image">
                        @if($item->product->images && count($item->product->images) > 0)
                        <img src="{{ asset('storage/' . $item->product->images[0]) }}" alt="{{ $item->product->name }}">
                        @else
                        <img src="{{ asset('images/pro1.jpeg') }}" alt="{{ $item->product->name }}">
                        @endif
                        <div class="product-overlay">
                            <a href="{{ route('products.show', $item->product->slug) }}" class="overlay-btn">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="product-category">{{ $item->product->category->name ?? 'Uncategorized' }}</span>
                        <h3 class="product-title">{{ $item->product->name }}</h3>
                        <div class="product-footer">
                            <div>
                                @if($item->product->sale_price)
                                <span class="product-old-price">EGP {{ number_format($item->product->price, 2) }}</span>
                                <span class="product-price">EGP {{ number_format($item->product->sale_price, 2) }}</span>
                                @else
                                <span class="product-price">EGP {{ number_format($item->product->price, 2) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="wishlist-actions" style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <button class="btn btn-primary move-to-cart-btn" data-product-id="{{ $item->product->id }}" style="flex: 1;">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button class="btn btn-outline remove-wishlist-btn" data-product-id="{{ $item->product->id }}" style="color: red; border-color: red;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Move to Cart
    document.querySelectorAll('.move-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const card = this.closest('.wishlist-item');
            
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;
            
            fetch(`/wishlist/move-to-cart/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 300);
                    
                    // Update cart badge
                    const badge = document.querySelector('.cart-badge');
                    if (badge) badge.textContent = data.cart_count;
                    
                    alert('Product moved to cart!');
                }
            })
            .catch(err => {
                this.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
                this.disabled = false;
            });
        });
    });
    
    // Remove from Wishlist
    document.querySelectorAll('.remove-wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const card = this.closest('.wishlist-item');
            
            if (!confirm('Remove from wishlist?')) return;
            
            fetch(`/wishlist/remove/${productId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        // Check if wishlist is empty
                        if (document.querySelectorAll('.wishlist-item').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
            });
        });
    });
});
</script>
@endpush
@endsection
