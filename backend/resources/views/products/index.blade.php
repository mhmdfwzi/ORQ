@extends('layouts.app')

@php
$pageTitle = __('Products');
$pageDescription = app()->getLocale() == 'ar' ? 'تصفح جميع منتجات ميديكامو الصيدلانية والعلاجية' : 'Browse all Medicamo pharmaceutical and therapeutic products.';
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="page-header">
    <div class="container">
        <h1 data-aos="fade-up">{{ __('Products') }}</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li>/</li>
            <li>{{ __('Products') }}</li>
        </ul>
    </div>
</section>

<!-- ==================== Products Filter ==================== -->
<section class="section">
    <div class="container">
        {{-- Filter section hidden as per request
        <div class="products-filter" data-aos="fade-up">
            <div class="filter-group">
                <label for="categoryFilter">Category:</label>
                <select id="categoryFilter" class="filter-select">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->slug }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="priceFilter">Price:</label>
                <select id="priceFilter" class="filter-select">
                    <option value="all">All Prices</option>
                    <option value="0-25">Under $25</option>
                    <option value="25-50">$25 - $50</option>
                    <option value="50-100">$50 - $100</option>
                    <option value="100+">Over $100</option>
                </select>
            </div>

            <div class="search-box">
                <input type="text" placeholder="Search for a product...">
                <button type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        --}}

        <!-- ==================== Mobile Sorting (No Reload) ==================== -->
        <div class="mobile-sort-container d-md-none mb-4" data-aos="fade-up">
            <div class="d-flex align-items-center justify-content-between bg-white p-2 rounded shadow-sm border">
                <span class="text-muted fw-bold ms-2"><i class="fas fa-sort-amount-down"></i> {{ __('Sort By') }}:</span>
                <select id="mobileSortSelect" class="form-select border-0 shadow-none w-auto" style="min-width: 150px; background-color: #f8f9fa;">
                    <option value="category" selected>{{ __('Category') }}</option>
                    <option value="default">{{ __('Default') }}</option>
                    <option value="price_asc">{{ __('Price: Low to High') }}</option>
                    <option value="price_desc">{{ __('Price: High to Low') }}</option>
                    <option value="name_asc">{{ __('Name: A to Z') }}</option>
                </select>
            </div>
        </div>

        <!-- ==================== Products Grid ==================== -->
        <div class="products-grid" id="productsGridContainer">
            @foreach($products as $index => $product)
            <x-product-card :product="$product" :delay="(($index % 6) + 1) * 50" />
            @endforeach
        </div>

        <!-- Pagination -->
        <nav class="pagination-nav" data-aos="fade-up">
            {{ $products->links() }}
        </nav>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('mobileSortSelect');
    const gridContainer = document.getElementById('productsGridContainer');
    
    if (sortSelect && gridContainer) {
        // Store original order
        const originalCards = Array.from(gridContainer.children);
        
        function sortProducts() {
            const sortType = sortSelect.value;
            let cards = Array.from(gridContainer.children);
            
            if (sortType === 'default') {
                // Restore original order
                originalCards.forEach(card => gridContainer.appendChild(card));
                return;
            }
            
            cards.sort((a, b) => {
                const priceA = parseFloat(a.dataset.price) || 0;
                const priceB = parseFloat(b.dataset.price) || 0;
                const nameA = a.dataset.name.toLowerCase();
                const nameB = b.dataset.name.toLowerCase();
                const catA = a.dataset.category.toLowerCase();
                const catB = b.dataset.category.toLowerCase();
                
                switch (sortType) {
                    case 'category':
                        // Sort by category first, then by name
                        const catCompare = catA.localeCompare(catB);
                        return catCompare === 0 ? nameA.localeCompare(nameB) : catCompare;
                    case 'price_asc':
                        return priceA - priceB;
                    case 'price_desc':
                        return priceB - priceA;
                    case 'name_asc':
                        return nameA.localeCompare(nameB);
                    default:
                        return 0;
                }
            });
            
            // Re-append in sorted order
            cards.forEach(card => gridContainer.appendChild(card));
        }

        sortSelect.addEventListener('change', sortProducts);
        
        // Trigger default sort on load if not default
        if (sortSelect.value !== 'default') {
            sortProducts();
        }
    }
});
</script>
@endpush
@endsection
