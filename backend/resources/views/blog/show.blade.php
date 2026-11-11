@extends('layouts.app')

@php
$postTitle = app()->getLocale() == 'ar' ? ($post->title_ar ?? $post->title) : $post->title;
$postExcerpt = app()->getLocale() == 'ar' ? ($post->excerpt_ar ?? $post->excerpt_text) : $post->excerpt_text;
$pageTitle = app()->getLocale() == 'ar' ? ($post->meta_title_ar ?? $postTitle) : ($post->meta_title ?: $post->title);
$pageDescription = app()->getLocale() == 'ar' ? ($post->meta_description_ar ?? $postExcerpt) : ($post->meta_description ?: $post->excerpt_text);
@endphp

@section('content')
<!-- ==================== Article ==================== -->
<article class="blog-article-page">
    <!-- Back Button (Mobile Sticky) -->
    <div class="article-back-bar">
        <a href="{{ route('blog.index') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            <span>{{ __('Back to Blog') }}</span>
        </a>
        <div class="share-quick">
            <button class="share-quick-btn" onclick="shareArticle()">
                <i class="fas fa-share-alt"></i>
            </button>
        </div>
    </div>

    <!-- Featured Image -->
    @if($post->image)
    <div class="article-hero">
        <img src="{{ $post->image_url }}" alt="{{ $postTitle }}" loading="lazy">
        <div class="article-hero-overlay"></div>
    </div>
    @endif

    <!-- Article Header -->
    <header class="article-header-mobile">
        <div class="container">
            <div class="article-meta-bar">
                <span class="meta-date">
                    <i class="far fa-calendar-alt"></i>
                    {{ $post->published_at->format('M d, Y') }}
                </span>
                <span class="meta-time">
                    <i class="far fa-clock"></i>
                    {{ $post->reading_time }} {{ __('min read') }}
                </span>
                <span class="meta-views">
                    <i class="far fa-eye"></i>
                    {{ $post->views_count }}
                </span>
            </div>
            
            <h1>{{ $postTitle }}</h1>
            
            @if($post->excerpt)
            <p class="article-excerpt">{{ $postExcerpt }}</p>
            @endif
            
            <div class="author-bar">
                <div class="author-avatar-small">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="author-details">
                    <span class="author-name">{{ $post->author->name ?? __('Medicamo Team') }}</span>
                    <span class="author-role">{{ __('Health Expert') }}</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Article Content -->
    <div class="article-body-mobile">
        <div class="container">
            <div class="article-content">
                @php
                    $postContent = app()->getLocale() == 'ar' ? ($post->content_ar ?? $post->content) : $post->content;
                @endphp
                {!! $postContent !!}
            </div>
        </div>
    </div>

    <!-- Share Section -->
    <div class="share-section">
        <div class="container">
            <div class="share-box">
                <span class="share-label">{{ __('Share this article') }}</span>
                <div class="share-buttons-row">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                       target="_blank" class="share-icon facebook" aria-label="Share on Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" 
                       target="_blank" class="share-icon twitter" aria-label="Share on Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . request()->url()) }}" 
                       target="_blank" class="share-icon whatsapp" aria-label="Share on WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <button class="share-icon copy" onclick="copyLink()" aria-label="Copy Link">
                        <i class="fas fa-link"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Author Card -->
    <div class="author-card-section">
        <div class="container">
            <div class="author-card">
                <div class="author-card-avatar">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="author-card-info">
                    <h4>Written by</h4>
                    <h3>{{ $post->author->name ?? 'Medicamo Team' }}</h3>
                    <p>Our team of pharmaceutical experts shares health tips and industry insights to help you make informed decisions.</p>
                </div>
            </div>
        </div>
    </div>
</article>

<!-- ==================== Related Posts ==================== -->
@if($relatedPosts->count() > 0)
<section class="related-section">
    <div class="container">
        <div class="section-label">
            <i class="fas fa-book-reader"></i> Read More
        </div>
        
        <div class="related-scroll">
            @foreach($relatedPosts as $related)
            <a href="{{ route('blog.show', $related->slug) }}" class="related-card">
                <div class="related-card-image">
                    <img src="{{ $related->image_url }}" alt="{{ $related->title }}" loading="lazy">
                </div>
                <div class="related-card-body">
                    <span class="related-date">{{ $related->published_at->format('M d') }}</span>
                    <h4>{{ Str::limit($related->title, 50) }}</h4>
                </div>
            </a>
            @endforeach
        </div>
        
        <div class="all-posts-link">
            <a href="{{ route('blog.index') }}" class="btn-all-posts">
                <i class="fas fa-list"></i> View All Articles
            </a>
        </div>
    </div>
</section>
@endif
@endsection

@push('styles')
<style>
/* ===================================
   Article Page - Mobile First
   =================================== */

.blog-article-page {
    background: #fff;
}

/* Back Bar */
.article-back-bar {
    position: sticky;
    top: 0;
    z-index: 100;
    background: #fff;
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.back-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-blue);
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
}

.back-btn i {
    font-size: 1rem;
}

.share-quick-btn {
    background: var(--light-gray);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: var(--primary-blue);
    cursor: pointer;
    transition: all 0.3s ease;
}

.share-quick-btn:active {
    background: var(--primary-blue);
    color: #fff;
}

/* Hero Image */
.article-hero {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.article-hero img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.article-hero-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 80px;
    background: linear-gradient(to top, #fff, transparent);
}

/* Header */
.article-header-mobile {
    padding: 0 0 1.5rem;
    margin-top: -30px;
    position: relative;
    z-index: 10;
}

.article-meta-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.8rem;
    color: var(--medium-gray);
}

.article-meta-bar span {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.article-meta-bar i {
    color: var(--secondary-green);
}

.article-header-mobile h1 {
    font-size: 1.5rem;
    color: var(--primary-blue);
    line-height: 1.35;
    margin-bottom: 0.75rem;
}

.article-excerpt {
    font-size: 1rem;
    color: var(--dark-gray);
    line-height: 1.6;
    margin-bottom: 1.25rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid var(--secondary-green);
}

.author-bar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.author-avatar-small {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-green));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.author-avatar-small i {
    color: #fff;
    font-size: 1.25rem;
}

.author-details {
    display: flex;
    flex-direction: column;
}

.author-name {
    font-weight: 600;
    color: var(--primary-blue);
    font-size: 0.95rem;
}

.author-role {
    font-size: 0.8rem;
    color: var(--medium-gray);
}

/* Article Body */
.article-body-mobile {
    padding: 0 0 2rem;
}

.article-content {
    font-size: 1.05rem;
    line-height: 1.8;
    color: var(--dark-gray);
}

.article-content h2 {
    font-size: 1.35rem;
    color: var(--primary-blue);
    margin: 2rem 0 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--secondary-green);
}

.article-content h3 {
    font-size: 1.15rem;
    color: var(--primary-blue);
    margin: 1.5rem 0 0.75rem;
}

.article-content p {
    margin-bottom: 1.25rem;
}

.article-content img {
    width: 100%;
    border-radius: 12px;
    margin: 1.5rem 0;
}

.article-content ul, .article-content ol {
    margin: 1.25rem 0;
    padding-right: 1.25rem;
}

.article-content li {
    margin-bottom: 0.5rem;
}

.article-content blockquote {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border-right: 4px solid var(--primary-blue);
    padding: 1.25rem;
    margin: 1.5rem 0;
    border-radius: 12px;
    font-style: italic;
    color: var(--primary-blue);
}

.article-content a {
    color: var(--secondary-green);
    text-decoration: underline;
}

/* Share Section */
.share-section {
    padding: 1.5rem 0;
    background: #f8fafc;
}

.share-box {
    text-align: center;
}

.share-label {
    display: block;
    font-weight: 600;
    color: var(--primary-blue);
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.share-buttons-row {
    display: flex;
    justify-content: center;
    gap: 0.75rem;
}

.share-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    border: none;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.share-icon:active {
    transform: scale(0.95);
}

.share-icon.facebook { background: #3b5998; }
.share-icon.twitter { background: #1da1f2; }
.share-icon.whatsapp { background: #25D366; }
.share-icon.copy { background: var(--primary-blue); }

/* Author Card */
.author-card-section {
    padding: 2rem 0;
}

.author-card {
    background: linear-gradient(135deg, var(--primary-blue), #1e3a8a);
    border-radius: 16px;
    padding: 1.5rem;
    color: #fff;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.author-card-avatar {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.author-card-avatar i {
    font-size: 1.75rem;
}

.author-card-info h4 {
    font-size: 0.8rem;
    opacity: 0.8;
    margin-bottom: 0.25rem;
    font-weight: 400;
}

.author-card-info h3 {
    font-size: 1.15rem;
    margin-bottom: 0.5rem;
}

.author-card-info p {
    font-size: 0.85rem;
    opacity: 0.9;
    line-height: 1.5;
    margin: 0;
}

/* Related Section */
.related-section {
    padding: 2rem 0 3rem;
    background: #f8fafc;
}

.related-scroll {
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

.related-scroll::-webkit-scrollbar {
    display: none;
}

.related-card {
    flex: 0 0 200px;
    scroll-snap-align: start;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    text-decoration: none;
}

.related-card:active {
    transform: scale(0.98);
}

.related-card-image {
    height: 120px;
    overflow: hidden;
}

.related-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-card-body {
    padding: 0.75rem;
}

.related-date {
    font-size: 0.7rem;
    color: var(--medium-gray);
    display: block;
    margin-bottom: 0.35rem;
}

.related-card-body h4 {
    font-size: 0.9rem;
    color: var(--primary-blue);
    line-height: 1.35;
    margin: 0;
}

.all-posts-link {
    text-align: center;
    margin-top: 1.5rem;
}

.btn-all-posts {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--primary-blue);
    color: #fff;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
}

/* Section Label (reused) */
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

/* ===================================
   Tablet & Desktop
   =================================== */
@media (min-width: 768px) {
    .article-hero {
        height: 350px;
    }
    
    .article-header-mobile {
        padding: 0 0 2rem;
        margin-top: -50px;
    }
    
    .article-header-mobile h1 {
        font-size: 2rem;
    }
    
    .article-content {
        max-width: 750px;
        margin: 0 auto;
    }
    
    .share-icon {
        width: 55px;
        height: 55px;
    }
    
    .share-icon:hover {
        transform: scale(1.1);
    }
    
    .author-card {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .related-scroll {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        overflow: visible;
        margin: 0;
        padding: 0;
    }
    
    .related-card {
        flex: none;
    }
    
    .related-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
}

@media (min-width: 1024px) {
    .article-hero {
        height: 400px;
        max-width: 800px;
        margin: 2rem auto 0;
        border-radius: 16px;
    }
    
    .article-header-mobile h1 {
        font-size: 2.5rem;
    }
    
    .article-back-bar {
        position: static;
        max-width: 750px;
        margin: 0 auto;
        box-shadow: none;
        padding: 1.5rem 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
function shareArticle() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $post->title }}',
            url: window.location.href
        });
    } else {
        document.querySelector('.share-section').scrollIntoView({ behavior: 'smooth' });
    }
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        const btn = document.querySelector('.share-icon.copy');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.style.background = 'var(--secondary-green)';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = 'var(--primary-blue)';
        }, 2000);
    });
}
</script>
@endpush
