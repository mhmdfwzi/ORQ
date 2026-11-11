@extends('layouts.app')

@php
$pageTitle = __('Blog');
$pageDescription = app()->getLocale() == 'ar' ? 'اقرأ أحدث المقالات والنصائح الصحية والأخبار الصيدلانية' : 'Read our latest articles, health tips, and pharmaceutical news.';
@endphp

@section('content')
<!-- ==================== Page Header ==================== -->
<section class="blog-header">
    <div class="container">
        <h1 data-aos="fade-up">
            <i class="fas fa-newspaper"></i>
            {{ __('Our Blog') }}
        </h1>
        <p data-aos="fade-up" data-aos-delay="100">{{ __('Health Tips, News & Articles') }}</p>
    </div>
</section>

<!-- ==================== Featured Posts (Mobile Swiper) ==================== -->
@if($featuredPosts->count() > 0)
<section class="featured-section">
    <div class="container">
        <div class="section-label" data-aos="fade-up">
            <i class="fas fa-star"></i> {{ __('Featured Articles') }}
        </div>
        
        <div class="featured-swiper" data-aos="fade-up">
            @foreach($featuredPosts as $post)
            <a href="{{ route('blog.show', $post->slug) }}" class="featured-card">
                <div class="featured-card-image">
                    <img src="{{ $post->image_url }}" alt="{{ app()->getLocale() == 'ar' ? ($post->title_ar ?? $post->title) : $post->title }}" loading="lazy">
                    <div class="featured-badge">
                        <i class="fas fa-fire"></i> {{ __('Featured') }}
                    </div>
                </div>
                <div class="featured-card-body">
                    <div class="post-meta-inline">
                        <span><i class="far fa-calendar-alt"></i> {{ $post->published_at->format('M d') }}</span>
                        <span><i class="far fa-clock"></i> {{ $post->reading_time }} {{ __('min read') }}</span>
                    </div>
                    <h3>{{ app()->getLocale() == 'ar' ? ($post->title_ar ?? $post->title) : $post->title }}</h3>
                    @php
                        $excerpt = app()->getLocale() == 'ar' ? ($post->excerpt_ar ?? $post->excerpt_text) : $post->excerpt_text;
                    @endphp
                    <p>{{ Str::limit($excerpt, 80) }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ==================== All Posts ==================== -->
<section class="blog-list-section">
    <div class="container">
        <div class="section-label" data-aos="fade-up">
            <i class="fas fa-list"></i> {{ __('All Articles') }}
        </div>
        
        @if($posts->count() > 0)
        <div class="blog-list">
            @foreach($posts as $index => $post)
            <article class="blog-list-item" data-aos="fade-up" data-aos-delay="{{ min(($index + 1) * 50, 200) }}">
                <a href="{{ route('blog.show', $post->slug) }}" class="blog-item-link">
                    <div class="blog-item-image">
                        <img src="{{ $post->image_url }}" alt="{{ app()->getLocale() == 'ar' ? ($post->title_ar ?? $post->title) : $post->title }}" loading="lazy">
                        @if($post->is_featured)
                        <span class="item-badge"><i class="fas fa-star"></i></span>
                        @endif
                    </div>
                    <div class="blog-item-content">
                        <div class="post-meta-small">
                            <span>{{ $post->published_at->format('M d, Y') }}</span>
                            <span class="dot">•</span>
                            <span>{{ $post->reading_time }} {{ __('min read') }}</span>
                        </div>
                        <h3>{{ app()->getLocale() == 'ar' ? ($post->title_ar ?? $post->title) : $post->title }}</h3>
                        @php
                            $postExcerpt = app()->getLocale() == 'ar' ? ($post->excerpt_ar ?? $post->excerpt_text) : $post->excerpt_text;
                        @endphp
                        <p>{{ Str::limit($postExcerpt, 100) }}</p>
                        <div class="blog-item-footer">
                            <div class="author-small">
                                <i class="fas fa-user-circle"></i>
                                {{ $post->author->name ?? 'Admin' }}
                            </div>
                            <div class="views-small">
                                <i class="far fa-eye"></i>
                                {{ $post->views_count }}
                            </div>
                        </div>
                    </div>
                </a>
            </article>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="blog-pagination" data-aos="fade-up">
            {{ $posts->links() }}
        </div>
        @else
        <div class="empty-blog" data-aos="fade-up">
            <i class="fas fa-newspaper"></i>
            <h3>No Articles Yet</h3>
            <p>Check back soon for health tips and updates!</p>
            <a href="{{ route('home') }}" class="btn-back-home">
                <i class="fas fa-home"></i> Back to Home
            </a>
        </div>
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
/* ===================================
   Blog Mobile-First Styles
   =================================== */

/* Header */
.blog-header {
    background: linear-gradient(135deg, var(--primary-blue) 0%, #1e3a8a 100%);
    padding: 2rem 0;
    text-align: center;
    color: #fff;
}

.blog-header h1 {
    color: #fff;
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.blog-header h1 i {
    color: var(--secondary-green);
}

.blog-header p {
    opacity: 0.9;
    font-size: 0.95rem;
    margin: 0;
}

/* Section Label */
.section-label {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-green));
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

/* Featured Section */
.featured-section {
    padding: 1.5rem 0;
    background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
}

.featured-swiper {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 1rem;
    margin: 0 -1rem;
    padding-left: 1rem;
    padding-right: 1rem;
}

.featured-swiper::-webkit-scrollbar {
    display: none;
}

.featured-card {
    flex: 0 0 85%;
    scroll-snap-align: start;
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    text-decoration: none;
    transition: transform 0.3s ease;
}

.featured-card:active {
    transform: scale(0.98);
}

.featured-card-image {
    position: relative;
    height: 180px;
    overflow: hidden;
}

.featured-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.featured-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.featured-card-body {
    padding: 1rem;
}

.post-meta-inline {
    display: flex;
    gap: 1rem;
    font-size: 0.8rem;
    color: var(--medium-gray);
    margin-bottom: 0.5rem;
}

.post-meta-inline i {
    margin-right: 0.25rem;
    color: var(--secondary-green);
}

.featured-card-body h3 {
    font-size: 1.1rem;
    color: var(--primary-blue);
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.featured-card-body p {
    font-size: 0.9rem;
    color: var(--dark-gray);
    line-height: 1.5;
    margin: 0;
}

/* Blog List Section */
.blog-list-section {
    padding: 1.5rem 0 3rem;
}

.blog-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.blog-list-item {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.blog-item-link {
    display: flex;
    text-decoration: none;
    transition: all 0.3s ease;
}

.blog-item-link:active {
    background: #f8fafc;
}

.blog-item-image {
    flex: 0 0 120px;
    height: 120px;
    position: relative;
    overflow: hidden;
}

.blog-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: var(--accent-gold);
    color: #fff;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
}

.blog-item-content {
    flex: 1;
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
}

.post-meta-small {
    font-size: 0.7rem;
    color: var(--medium-gray);
    margin-bottom: 0.35rem;
}

.post-meta-small .dot {
    margin: 0 0.25rem;
}

.blog-item-content h3 {
    font-size: 0.95rem;
    color: var(--primary-blue);
    margin-bottom: 0.35rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blog-item-content p {
    font-size: 0.8rem;
    color: var(--dark-gray);
    line-height: 1.4;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
}

.blog-item-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid #f0f0f0;
    font-size: 0.7rem;
    color: var(--medium-gray);
}

.author-small, .views-small {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.author-small i, .views-small i {
    color: var(--primary-blue);
}

/* Pagination */
.blog-pagination {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

.blog-pagination nav {
    display: flex;
    gap: 0.5rem;
}

/* Empty State */
.empty-blog {
    text-align: center;
    padding: 3rem 1.5rem;
    background: #f8fafc;
    border-radius: 16px;
}

.empty-blog i {
    font-size: 3rem;
    color: var(--medium-gray);
    margin-bottom: 1rem;
}

.empty-blog h3 {
    color: var(--primary-blue);
    margin-bottom: 0.5rem;
}

.empty-blog p {
    color: var(--medium-gray);
    margin-bottom: 1.5rem;
}

.btn-back-home {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--primary-blue);
    color: #fff;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    text-decoration: none;
}

/* ===================================
   Tablet & Desktop Styles
   =================================== */
@media (min-width: 768px) {
    .blog-header {
        padding: 3rem 0;
    }
    
    .blog-header h1 {
        font-size: 2.5rem;
    }
    
    .featured-swiper {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        overflow: visible;
        margin: 0;
        padding: 0;
    }
    
    .featured-card {
        flex: none;
    }
    
    .featured-card-image {
        height: 200px;
    }
    
    .blog-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    
    .blog-item-link {
        flex-direction: column;
    }
    
    .blog-item-image {
        flex: none;
        height: 180px;
    }
    
    .blog-item-content {
        padding: 1.25rem;
    }
    
    .blog-item-content h3 {
        font-size: 1.1rem;
    }
}

@media (min-width: 1024px) {
    .featured-section {
        padding: 3rem 0;
    }
    
    .blog-list-section {
        padding: 3rem 0 4rem;
    }
    
    .blog-list {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .featured-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    }
    
    .blog-list-item:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }
}
</style>
@endpush
