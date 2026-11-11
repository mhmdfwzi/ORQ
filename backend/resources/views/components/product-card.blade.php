@props(['product', 'showDescription' => true, 'delay' => 0])

<div class="product-card product-card-enhanced" data-aos="fade-up" data-aos-delay="{{ $delay }}"
     data-price="{{ $product->sale_price ?? $product->price }}"
     data-name="{{ app()->getLocale() == 'ar' ? ($product->name_ar ?? $product->name) : $product->name }}"
     data-category="{{ app()->getLocale() == 'ar' ? ($product->category->name_ar ?? $product->category->name ?? '') : ($product->category->name ?? '') }}"
     data-date="{{ $product->created_at }}">
    
    <!-- Unified Action Buttons -->
    <div class="product-actions">

        <button class="action-btn wishlist-btn" data-product-id="{{ $product->id }}" title="Add to Wishlist">
            <i class="far fa-heart"></i>
        </button>
        <button class="action-btn cart-btn" data-product-id="{{ $product->id }}" title="Quick Add">
            <i class="fas fa-cart-plus"></i>
        </button>
    </div>
    
    <div class="product-image">
        <!-- Clickable Image -->
        <a href="{{ route('products.show', $product) }}" class="product-image-link">
            @if($product->images && count($product->images) > 0)
            <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}">
            @else
            <img src="{{ asset('images/pro1.jpeg') }}" alt="{{ $product->name }}">
            @endif
            <div class="product-image-overlay">
                <span class="view-details-text"><i class="fas fa-eye"></i> {{ __('View Details') }}</span>
            </div>
        </a>
    </div>
    
    <div class="product-info">
        <span class="product-category">{{ app()->getLocale() == 'ar' ? ($product->category->name_ar ?? $product->category->name ?? 'غير مصنف') : ($product->category->name ?? 'Uncategorized') }}</span>
        <!-- Clickable Title -->
        <h3 class="product-title">
            <a href="{{ route('products.show', $product) }}" class="product-title-link">{{ app()->getLocale() == 'ar' ? ($product->name_ar ?? $product->name) : $product->name }}</a>
        </h3>
        @if($showDescription)
        @php
            $desc = app()->getLocale() == 'ar' ? ($product->short_description_ar ?? $product->short_description) : $product->short_description;
        @endphp
        @if($desc)
        <p class="product-description">{{ Str::limit($desc, 60) }}</p>
        @endif
        @endif
        <div class="product-footer">
            <div class="product-pricing-inline">
                @if($product->sale_price)
                <span class="product-old-price">EGP {{ number_format($product->price, 2) }}</span>
                <span class="product-price">EGP {{ number_format($product->sale_price, 2) }}</span>
                @else
                <span class="product-price">EGP {{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            <a href="{{ route('products.show', $product) }}" class="btn-add-cart-main">
                <i class="fas fa-shopping-bag"></i>
                {{ __('Buy Now') }}
            </a>
        </div>
    </div>
</div>
