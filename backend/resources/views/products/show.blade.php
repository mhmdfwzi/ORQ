@extends('layouts.app')

@php
$productName = app()->getLocale() == 'ar' ? ($product->name_ar ?? $product->name) : $product->name;
$productDesc = app()->getLocale() == 'ar' ? ($product->short_description_ar ?? $product->short_description) : $product->short_description;
$pageTitle = $productName;
$pageDescription = $productDesc ?? __('View product details and add to cart.');
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">{{ __('Product Details') }}</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li>/</li>
            <li><a href="{{ route('products.index') }}">{{ __('Products') }}</a></li>
            <li>/</li>
            <li>{{ $productName }}</li>
        </ul>
    </div>
</section>

<!-- ==================== Product Details ==================== -->
<section class="product-details">
    <div class="container">
        <div class="product-details-grid">
            <!-- Product Images -->
            <div class="product-gallery" data-aos="fade-right">
                <div class="main-image">
                    @if($product->images && count($product->images) > 0)
                    <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}"
                        style="max-width: 100%; border-radius: 15px;" id="mainImage">
                    @else
                    <img src="{{ asset('images/pro1.jpeg') }}" alt="{{ $product->name }}"
                        style="max-width: 100%; border-radius: 15px;" id="mainImage">
                    @endif
                </div>

                <div class="thumbnail-gallery">
                    @if($product->images && count($product->images) > 0)
                    @foreach($product->images as $index => $image)
                    <div class="thumbnail {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}"
                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                    </div>
                    @endforeach
                    @else
                    <div class="thumbnail active">
                        <img src="{{ asset('images/pro1.jpeg') }}" alt="{{ $product->name }}"
                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Information -->
            <div class="product-details-info" data-aos="fade-left">
                <h1>{{ $productName }}</h1>

                <div class="product-rating">
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($product->average_rating))
                            <i class="fas fa-star"></i>
                            @elseif($i - 0.5 <= $product->average_rating)
                            <i class="fas fa-star-half-alt"></i>
                            @else
                            <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="rating-text">({{ number_format($product->average_rating, 1) }} {{ __('out of') }} 5 | {{ $product->reviews_count ?? 0 }} {{ __('reviews') }})</span>
                </div>

                <div class="product-details-price">
                    @if($product->sale_price)
                    <span class="current-price">{{ number_format($product->sale_price, 2) }} EGP</span>
                    <span class="old-price">{{ number_format($product->price, 2) }} EGP</span>
                    @php
                    $discount = round((($product->price - $product->sale_price) / $product->price) * 100);
                    @endphp
                    <span class="discount-badge">{{ $discount }}% {{ __('Off') }}</span>
                    @else
                    <span class="current-price">{{ number_format($product->price, 2) }} EGP</span>
                    @endif
                </div>

                <!-- Mobile: Quick Add Section (Under Price) -->
                <div class="mobile-quick-add">
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="mobile-add-form ajax-add-to-cart-form">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary btn-add-cart-mobile">
                            <i class="fas fa-shopping-cart"></i> {{ __('Add to Cart') }}
                        </button>
                    </form>
                    <button type="button" class="btn-wishlist-mobile toggle-wishlist-btn" data-product-id="{{ $product->id }}">
                        <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart"></i>
                    </button>
                </div>

                @if($product->short_description)
                @php
                    $shortDesc = app()->getLocale() == 'ar' ? ($product->short_description_ar ?? $product->short_description) : $product->short_description;
                @endphp
                <p class="product-short-description">
                    {{ $shortDesc }}
                </p>
                @endif

                <div class="product-details-description">
                    <h3>{{ __('Product Description') }}</h3>
                    @php
                        $fullDesc = app()->getLocale() == 'ar' ? ($product->description_ar ?? $product->description) : $product->description;
                    @endphp
                    {!! $fullDesc !!}
                </div>

                <div class="product-meta">
                    <div class="product-meta-item">
                        <span class="meta-label">{{ __('Category') }}:</span>
                        @php
                            $catName = app()->getLocale() == 'ar' ? ($product->category->name_ar ?? $product->category->name) : ($product->category->name ?? __('Uncategorized'));
                        @endphp
                        <span class="meta-value">{{ $catName }}</span>
                    </div>
                    @if($product->size)
                    <div class="product-meta-item">
                        <span class="meta-label">{{ __('Size') }}:</span>
                        <span class="meta-value">{{ $product->size }}</span>
                    </div>
                    @endif
                    @if($product->manufacturer)
                    <div class="product-meta-item">
                        <span class="meta-label">{{ __('Manufacturer') }}:</span>
                        <span class="meta-value">{{ $product->manufacturer }}</span>
                    </div>
                    @endif
                    <div class="product-meta-item">
                        <span class="meta-label">{{ __('Availability') }}:</span>
                        @if($product->stock_quantity > 0)
                        <span class="meta-value" style="color: var(--secondary-green);">
                            <i class="fas fa-check-circle"></i> {{ __('In Stock') }}
                        </span>
                        @else
                        <span class="meta-value" style="color: red;">
                            <i class="fas fa-times-circle"></i> {{ __('Out of Stock') }}
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Desktop: Full Add to Cart Section -->
                <div class="desktop-add-section">
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="ajax-add-to-cart-form">
                        @csrf
                        <div class="quantity-selector">
                            <label>{{ __('Quantity') }}:</label>
                            <div class="quantity-controls">
                                <button type="button" class="quantity-btn" onclick="decreaseQty()">−</button>
                                <input type="number" name="quantity" class="quantity-input" value="1" min="1" id="qtyInput">
                                <button type="button" class="quantity-btn" onclick="increaseQty()">+</button>
                            </div>
                        </div>

                        <div class="product-details-buttons">
                            <button type="submit" class="btn btn-primary btn-large add-to-cart-btn">
                                <i class="fas fa-shopping-cart"></i>
                                {{ __('Add to Cart') }}
                            </button>
                            <button type="button" class="btn-wishlist toggle-wishlist-btn" data-product-id="{{ $product->id }}">
                                <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ==================== Customer Reviews ==================== -->
        <div class="product-reviews-section" style="margin-top: 4rem;" data-aos="fade-up">
            <div class="section-title">
                <h2>{{ __('Customer Reviews') }}</h2>
                <p>{{ __('See what our customers say about this product') }}</p>
            </div>

            {{-- Reviews Summary --}}
            <div class="reviews-summary" style="display:flex;align-items:center;gap:2rem;margin-bottom:2rem;padding:1.5rem;background:#f8f9fa;border-radius:12px;">
                <div style="text-align:center;min-width:120px;">
                    <div style="font-size:3rem;font-weight:800;color:var(--primary-blue);">{{ number_format($product->average_rating, 1) }}</div>
                    <div class="stars" style="color:#ffc107;font-size:1.1rem;">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($product->average_rating))
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <div style="color:#666;font-size:0.9rem;margin-top:4px;">{{ $product->approvedReviews->count() }} {{ __('reviews') }}</div>
                </div>

                {{-- Rating Bars --}}
                <div style="flex:1;">
                    @for($star = 5; $star >= 1; $star--)
                        @php
                            $count = $product->approvedReviews->where('rating', $star)->count();
                            $total = $product->approvedReviews->count();
                            $pct = $total > 0 ? ($count / $total * 100) : 0;
                        @endphp
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                            <span style="font-size:0.85rem;min-width:15px;color:#666;">{{ $star }}</span>
                            <i class="fas fa-star" style="color:#ffc107;font-size:0.75rem;"></i>
                            <div style="flex:1;height:8px;background:#e9ecef;border-radius:4px;overflow:hidden;">
                                <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg,#ffc107,#ffb300);border-radius:4px;transition:width 0.5s;"></div>
                            </div>
                            <span style="font-size:0.8rem;color:#999;min-width:20px;">{{ $count }}</span>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Reviews List --}}
            @if($product->approvedReviews->count() > 0)
                <div class="reviews-list" style="display:flex;flex-direction:column;gap:1rem;">
                    @foreach($product->approvedReviews->sortByDesc('created_at') as $review)
                    <div style="padding:1.2rem 1.5rem;background:#fff;border:1px solid #eee;border-radius:12px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <i class="fas fa-user-circle" style="font-size:2rem;color:var(--primary-blue);"></i>
                                <div>
                                    <strong style="font-size:0.95rem;">{{ $review->user->name ?? __('Customer') }}</strong>
                                    <div style="font-size:0.8rem;color:#999;">{{ $review->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="stars" style="color:#ffc107;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                            </div>
                        </div>
                        @if($review->title)
                            <h5 style="margin:0 0 6px;font-size:0.95rem;">{{ $review->title }}</h5>
                        @endif
                        @if($review->comment)
                            <p style="margin:0;color:#555;font-size:0.9rem;line-height:1.6;">{{ $review->comment }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:2rem;color:#999;">
                    <i class="far fa-comment-dots" style="font-size:2.5rem;margin-bottom:10px;display:block;"></i>
                    {{ __('No reviews yet. Be the first to review this product!') }}
                </div>
            @endif

            {{-- Write a Review Form --}}
            <div style="margin-top:2rem;padding:1.5rem;background:#f8f9fa;border-radius:12px;">
                <h4 style="margin:0 0 1rem;"><i class="fas fa-pen me-2"></i>{{ __('Write a Review') }}</h4>

                @auth
                    @php
                        $alreadyReviewed = $product->approvedReviews->where('user_id', auth()->id())->count() > 0
                            || \App\Models\Review::where('user_id', auth()->id())->where('product_id', $product->id)->exists();
                    @endphp

                    @if($alreadyReviewed)
                        <p style="color:var(--primary-blue);"><i class="fas fa-check-circle me-1"></i> {{ __('You have already reviewed this product.') }}</p>
                    @else
                        @if(session('success'))
                            <div style="padding:12px 16px;background:#d4edda;color:#155724;border-radius:8px;margin-bottom:1rem;">
                                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div style="padding:12px 16px;background:#f8d7da;color:#721c24;border-radius:8px;margin-bottom:1rem;">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('reviews.store') }}" method="POST" id="reviewForm">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            {{-- Star Rating --}}
                            <div style="margin-bottom:1rem;">
                                <label style="display:block;font-weight:600;margin-bottom:6px;">{{ __('Your Rating') }} <span style="color:red;">*</span></label>
                                <div class="star-rating-input" style="display:flex;gap:4px;font-size:1.8rem;cursor:pointer;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="far fa-star" data-rating="{{ $i }}" style="color:#ffc107;transition:0.15s;"></i>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="ratingInput" value="" required>
                                @error('rating') <small style="color:red;">{{ $message }}</small> @enderror
                            </div>

                            {{-- Title --}}
                            <div style="margin-bottom:1rem;">
                                <label style="display:block;font-weight:600;margin-bottom:6px;">{{ __('Review Title') }}</label>
                                <input type="text" name="title" class="form-control" placeholder="{{ __('e.g. Great product!') }}"
                                       style="width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:8px;font-size:0.95rem;"
                                       value="{{ old('title') }}" maxlength="255">
                            </div>

                            {{-- Comment --}}
                            <div style="margin-bottom:1rem;">
                                <label style="display:block;font-weight:600;margin-bottom:6px;">{{ __('Your Review') }}</label>
                                <textarea name="comment" rows="4" class="form-control"
                                          placeholder="{{ __('Share your experience with this product...') }}"
                                          style="width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:8px;font-size:0.95rem;resize:vertical;"
                                          maxlength="1000">{{ old('comment') }}</textarea>
                            </div>

                            <button type="submit" style="background:var(--primary-blue);color:#fff;border:none;padding:10px 28px;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer;transition:0.2s;">
                                <i class="fas fa-paper-plane me-1"></i> {{ __('Submit Review') }}
                            </button>
                            <small style="display:block;margin-top:8px;color:#999;">
                                <i class="fas fa-info-circle me-1"></i>{{ __('Your review will be published after admin approval.') }}
                            </small>
                        </form>
                    @endif
                @else
                    <p style="color:#666;">
                        <i class="fas fa-sign-in-alt me-1"></i>
                        {!! __('Please :login to write a review.', ['login' => '<a href="' . route('login') . '" style="color:var(--primary-blue);font-weight:600;">' . __('log in') . '</a>']) !!}
                    </p>
                @endauth
            </div>
        </div>

        <!-- Similar Products -->
        @if($relatedProducts->count() > 0)
        <div class="section-title" style="margin-top: 5rem;" data-aos="fade-up">
            <h2>{{ __('Similar Products') }}</h2>
            <p>{{ __('You may also like these products') }}</p>
        </div>

        <div class="products-grid">
            @foreach($relatedProducts as $index => $related)
            <x-product-card :product="$related" :delay="($index + 1) * 100" />
            @endforeach
        </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
function decreaseQty() {
    var input = document.getElementById('qtyInput');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}
function increaseQty() {
    var input = document.getElementById('qtyInput');
    input.value = parseInt(input.value) + 1;
}

document.addEventListener('DOMContentLoaded', function() {
    // AJAX Add to Cart
    const addCartForms = document.querySelectorAll('.ajax-add-to-cart-form');
    
    addCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update header cart count (Desktop & Mobile)
                    const cartBadges = document.querySelectorAll('.cart-badge, .mobile-menu-badge');
                    if (data.cartCount !== undefined) {
                        cartBadges.forEach(badge => {
                            badge.textContent = data.cartCount;
                            // Ensure badge is visible if count > 0, though implementation might vary (some CSS hides empty)
                            // Assuming CSS handles visibility based on content or we can force it
                            if (data.cartCount > 0) {
                                badge.style.display = 'inline-flex'; // or block/inline-block mainly
                            } else {
                                badge.style.display = 'none';
                            }
                        });
                    }
                    
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Success") }}',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        // Fallback toast or alert
                        alert(data.message);
                    }
                } else {
                     if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: data.message || '{{ __("Something went wrong") }}'
                        });
                    } else {
                        alert(data.message || 'Error occurred');
                    }
                }
            })
            .catch(err => {
                console.error(err);
                alert('{{ __("An error occurred. Please try again.") }}');
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    });

    // Wishlist Toggle
    const wishlistBtns = document.querySelectorAll('.toggle-wishlist-btn');
    
    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const icon = this.querySelector('i');
            
            // Optimistic UI update
            const isCurrentlyAdded = icon.classList.contains('fas');
            
            // Add spinning effect? Maybe too much. Just toggle for now.
            // Actually, let's wait for response to be sure, or do optimistic.
            // Let's do optimistic.
            if (isCurrentlyAdded) {
                icon.classList.remove('fas');
                icon.classList.add('far');
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas');
            }
            
            fetch('/wishlist/toggle/' + productId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (res.status === 401) {
                    window.location.href = '{{ route("login") }}';
                    throw new Error('Unauthorized');
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                     // Sync all buttons for this product (desktop & mobile)
                     document.querySelectorAll(`.toggle-wishlist-btn[data-product-id="${productId}"] i`).forEach(i => {
                         if (data.action === 'added') {
                             i.classList.remove('far');
                             i.classList.add('fas');
                         } else {
                             i.classList.remove('fas');
                             i.classList.add('far');
                         }
                     });
                     
                     // Show toast
                     const message = data.action === 'added' ? '{{ __("Added to wishlist") }}' : '{{ __("Removed from wishlist") }}';
                     // Toast logic here if available, or just console
                } else {
                    // Revert on failure
                    if (isCurrentlyAdded) {
                        icon.classList.add('fas');
                        icon.classList.remove('far');
                    } else {
                        icon.classList.add('far');
                        icon.classList.remove('fas');
                    }
                }
            })
            .catch(err => {
                if (err.message !== 'Unauthorized') {
                    // Revert
                     if (isCurrentlyAdded) {
                        icon.classList.add('fas');
                        icon.classList.remove('far');
                    } else {
                        icon.classList.add('far');
                        icon.classList.remove('fas');
                    }
                }
            });
        });
    });
    // Star Rating Input
    const starInputs = document.querySelectorAll('.star-rating-input i');
    const ratingInput = document.getElementById('ratingInput');

    if (starInputs.length && ratingInput) {
        starInputs.forEach(star => {
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                starInputs.forEach(s => {
                    s.classList.toggle('fas', parseInt(s.dataset.rating) <= rating);
                    s.classList.toggle('far', parseInt(s.dataset.rating) > rating);
                });
            });

            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                ratingInput.value = rating;
                starInputs.forEach(s => {
                    s.classList.toggle('fas', parseInt(s.dataset.rating) <= rating);
                    s.classList.toggle('far', parseInt(s.dataset.rating) > rating);
                });
            });
        });

        document.querySelector('.star-rating-input')?.addEventListener('mouseleave', function() {
            const current = parseInt(ratingInput.value) || 0;
            starInputs.forEach(s => {
                s.classList.toggle('fas', parseInt(s.dataset.rating) <= current);
                s.classList.toggle('far', parseInt(s.dataset.rating) > current);
            });
        });
    }
});
</script>
@endpush
@endsection
