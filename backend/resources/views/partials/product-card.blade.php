{{-- Product Card Component --}}
<div class="product-card" data-aos="fade-up" data-aos-delay="{{ $delay ?? 100 }}">
    @if($product->sale_price)
        <span class="product-badge sale">خصم {{ $product->discount_percentage }}%</span>
    @elseif($product->created_at->diffInDays() < 7)
        <span class="product-badge new">جديد</span>
    @endif
    
    <div class="product-image">
        @if($product->images && count($product->images) > 0)
            <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}">
        @else
            <img src="{{ asset('assets/images/product-placeholder.jpg') }}" alt="{{ $product->name }}">
        @endif
        
        <div class="product-overlay">
            <a href="{{ route('products.show', $product->slug) }}" class="overlay-btn" title="عرض سريع">
                <i class="fas fa-eye"></i>
            </a>
            <button class="overlay-btn add-to-cart-btn" data-product-id="{{ $product->id }}" title="أضف للسلة">
                <i class="fas fa-shopping-cart"></i>
            </button>
            <button class="overlay-btn add-to-wishlist-btn" data-product-id="{{ $product->id }}" title="أضف للمفضلة">
                <i class="fas fa-heart"></i>
            </button>
        </div>
    </div>
    
    <div class="product-info">
        <span class="product-category">{{ $product->category->name }}</span>
        <h3 class="product-title">
            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
        </h3>
        <p class="product-description">{{ Str::limit($product->short_description, 60) }}</p>
        
        <div class="product-footer">
            <div class="product-pricing">
                @if($product->sale_price)
                    <span class="product-old-price">{{ number_format($product->price, 2) }} EGP</span>
                @endif
                <span class="product-price">
                    {{ number_format($product->current_price, 2) }} <span class="currency">EGP</span>
                </span>
            </div>
            <button class="add-to-cart-btn" data-product-id="{{ $product->id }}">
                <i class="fas fa-cart-plus"></i>
                أضف للسلة
            </button>
        </div>
    </div>
</div>
