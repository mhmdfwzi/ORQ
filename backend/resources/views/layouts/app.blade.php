<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $pageDescription ?? 'Medicamo - Pharmaceutical Production Company. Feel Better Naturally.' }}">
    <meta name="keywords" content="{{ $pageKeywords ?? 'pharmaceuticals, medicine, skincare, Medicamo, healthcare products' }}">
    <title>{{ isset($pageTitle) ? $pageTitle . ' - Medicamo' : 'Medicamo - Pharmaceutical Production Company' }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Bootstrap CSS (LTR) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/ltr.css') }}">
    @endif

    @stack('styles')
</head>

<body>

    <!-- ==================== Header ==================== -->
    <header class="main-header" id="header">
        <div class="container">
            <nav class="navbar">
                <a href="{{ route('home') }}" class="navbar-brand">
                    <img src="{{ asset('images/logo.png') }}" alt="Medicamo Logo" style="height: 50px;">
                </a>

                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle Menu">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>

                <ul class="navbar-nav" id="navbarNav">
                    <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">{{ __('Home') }}</a></li>
                    
                    <!-- Products with Mega Menu -->
                    <li class="nav-item has-mega-menu">
                        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            {{ __('Products') }} <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="mega-menu">
                            <div class="mega-menu-container">
                                @php
                                    $menuCategories = \App\Models\Category::active()->with(['products' => function($q) {
                                        $q->published()->take(4);
                                    }])->orderBy('sort_order')->take(4)->get();
                                @endphp
                                
                                @foreach($menuCategories as $category)
                                <div class="mega-menu-column">
                                    <h4>
                                        <i class="{{ $category->icon ?? 'fas fa-pills' }}"></i>
                                        {{ app()->getLocale() == 'ar' ? ($category->name_ar ?? $category->name) : $category->name }}
                                    </h4>
                                    <ul>
                                        @foreach($category->products as $product)
                                        <li><a href="{{ route('products.show', $product->slug) }}">{{ app()->getLocale() == 'ar' ? ($product->name_ar ?? $product->name) : $product->name }}</a></li>
                                        @endforeach
                                    </ul>
                                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="view-all">
                                        {{ __('View All') }} {{ app()->getLocale() == 'ar' ? ($category->name_ar ?? $category->name) : $category->name }} →
                                    </a>
                                </div>
                                @endforeach
                                
                                <!-- Featured Product -->
                                @php
                                    $featuredProduct = \App\Models\Product::published()->featured()->inRandomOrder()->first();
                                @endphp
                                @if($featuredProduct)
                                <div class="mega-menu-featured">
                                    <span class="badge bg-success mb-2">{{ __('Featured') }}</span>
                                    @if($featuredProduct->images && count($featuredProduct->images) > 0)
                                    <img src="{{ asset('storage/' . $featuredProduct->images[0]) }}" alt="{{ $featuredProduct->name }}">
                                    @endif
                                    <h5>{{ app()->getLocale() == 'ar' ? ($featuredProduct->name_ar ?? $featuredProduct->name) : $featuredProduct->name }}</h5>
                                    <div class="price">{{ number_format($featuredProduct->current_price, 2) }} EGP</div>
                                    <a href="{{ route('products.show', $featuredProduct->slug) }}" class="btn btn-sm btn-primary mt-2">{{ __('View Product') }}</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </li>
                    
                    <li><a href="{{ route('blog.index') }}" class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">{{ __('Blog') }}</a></li>
                    <li><a href="{{ route('jobs.index') }}" class="nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}">{{ __('Careers') }}</a></li>
                    <li><a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">{{ __('About Us') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">{{ __('Contact') }}</a></li>
                </ul>

                <div class="header-icons">
                    @auth
                        <a href="{{ route('profile.index') }}" class="header-icon-link user-profile-link desktop-only">
                            <i class="fas fa-user-circle"></i>
                            <span class="user-name">{{ auth()->user()->name }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="header-icon-link desktop-only">
                            <i class="fas fa-user-circle"></i>
                        </a>
                    @endauth
                    <!-- Language Switcher Toggle (Desktop Only) -->
                    <div class="lang-toggle desktop-only">
                        <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
                        <a href="{{ route('lang.switch', 'ar') }}" class="lang-btn {{ app()->getLocale() == 'ar' ? 'active' : '' }}">AR</a>
                    </div>
                    <div class="cart-icon">
                        <a href="{{ route('cart.index') }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-badge">{{ $cartCount ?? 0 }}</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Enhanced Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <button class="mobile-menu-close" id="mobileMenuClose">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- User Profile Section -->
        <div class="mobile-menu-user">
            @auth
                <div class="mobile-user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="mobile-user-info">
                    <span class="mobile-user-greeting">{{ __('Welcome back!') }}</span>
                    <span class="mobile-user-name">{{ auth()->user()->name }}</span>
                </div>
            @else
                <div class="mobile-user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="mobile-user-info">
                    <span class="mobile-user-greeting">{{ __('Hello, Guest!') }}</span>
                    <a href="{{ route('login') }}" class="mobile-user-login">{{ __('Login / Register') }}</a>
                </div>
            @endauth
        </div>

        <!-- Main Navigation -->
        <nav class="mobile-menu-nav">
            <a href="{{ route('home') }}" class="mobile-menu-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>{{ __('Home') }}</span>
            </a>
            <a href="{{ route('products.index') }}" class="mobile-menu-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-bag"></i>
                <span>{{ __('Products') }}</span>
            </a>
            <a href="{{ route('blog.index') }}" class="mobile-menu-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i>
                <span>{{ __('Blog') }}</span>
            </a>
            <a href="{{ route('cart.index') }}" class="mobile-menu-link {{ request()->routeIs('cart.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span>{{ __('Cart') }}</span>
                @if(($cartCount ?? 0) > 0)
                    <span class="mobile-menu-badge">{{ $cartCount }}</span>
                @endif
            </a>
            @auth
                <a href="{{ route('wishlist.index') }}" class="mobile-menu-link">
                    <i class="fas fa-heart"></i>
                    <span>{{ __('Wishlist') }}</span>
                </a>
                <a href="{{ route('profile.orders') }}" class="mobile-menu-link">
                    <i class="fas fa-box"></i>
                    <span>{{ __('My Orders') }}</span>
                </a>
                <a href="{{ route('profile.index') }}" class="mobile-menu-link">
                    <i class="fas fa-user-cog"></i>
                    <span>{{ __('My Profile') }}</span>
                </a>
            @endauth
        </nav>

        <!-- Divider -->
        <div class="mobile-menu-divider"></div>

        <!-- Secondary Links -->
        <nav class="mobile-menu-secondary">
            <a href="{{ route('about') }}" class="mobile-menu-link">
                <i class="fas fa-info-circle"></i>
                <span>{{ __('About Us') }}</span>
            </a>
            <a href="{{ route('contact') }}" class="mobile-menu-link">
                <i class="fas fa-phone-alt"></i>
                <span>{{ __('Contact Us') }}</span>
            </a>
            <a href="{{ route('privacy') }}" class="mobile-menu-link">
                <i class="fas fa-shield-alt"></i>
                <span>{{ __('Privacy Policy') }}</span>
            </a>
        </nav>

        <!-- Divider -->
        <div class="mobile-menu-divider"></div>

        <!-- Language Switcher in Mobile Menu -->
        <div class="mobile-menu-lang">
            <span class="mobile-lang-label"><i class="fas fa-globe"></i> {{ __('Language') }}</span>
            <div class="mobile-lang-toggle">
                <a href="{{ route('lang.switch', 'en') }}" class="mobile-lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}">English</a>
                <a href="{{ route('lang.switch', 'ar') }}" class="mobile-lang-btn {{ app()->getLocale() == 'ar' ? 'active' : '' }}">العربية</a>
            </div>
        </div>

        <!-- Logout Button (if logged in) -->
        @auth
        <div class="mobile-menu-footer">
            <form action="{{ route('logout') }}" method="POST" style="width: 100%;">
                @csrf
                <button type="submit" class="mobile-logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('Logout') }}</span>
                </button>
            </form>
        </div>
        @endauth
    </div>

    <!-- Main Content -->
    @yield('content')

    <!-- ==================== Footer ==================== -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <!-- About Section -->
                <div class="footer-section" data-aos="fade-up">
                    <h3>{{ $settings['site_name'] ?? 'Medicamo' }}</h3>
                    <p>{{ $settings['site_description'] ?? 'Your trusted pharmaceutical production company. We combine nature\'s power with scientific research to deliver effective health solutions.' }}</p>
                    <div class="social-links">
                        @if(!empty($settings['facebook_url']))
                        <a href="{{ $settings['facebook_url'] }}" target="_blank" rel="noopener" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        @endif
                        @if(!empty($settings['instagram_url']))
                        <a href="{{ $settings['instagram_url'] }}" target="_blank" rel="noopener" class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                        @endif
                        @if(!empty($settings['twitter_url']))
                        <a href="{{ $settings['twitter_url'] }}" target="_blank" rel="noopener" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        @endif
                        @if(!empty($settings['whatsapp_number']))
                        <a href="https://wa.me/{{ $settings['whatsapp_number'] }}" target="_blank" rel="noopener" class="social-icon">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-section" data-aos="fade-up" data-aos-delay="100">
                    <h3>{{ __('Quick Links') }}</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                        <li><a href="{{ route('products.index') }}">{{ __('Products') }}</a></li>
                        <li><a href="{{ route('blog.index') }}">{{ __('Blog') }}</a></li>
                        <li><a href="{{ route('jobs.index') }}">{{ __('Careers') }}</a></li>
                        <li><a href="{{ route('about') }}">{{ __('About Us') }}</a></li>
                        <li><a href="{{ route('contact') }}">{{ __('Contact') }}</a></li>
                    </ul>
                </div>

                <!-- My Account -->
                <div class="footer-section" data-aos="fade-up" data-aos-delay="150">
                    <h3>{{ __('My Account') }}</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        <li><a href="{{ route('register') }}">{{ __('Register') }}</a></li>
                        <li><a href="{{ route('profile.index') }}">{{ __('My Profile') }}</a></li>
                        <li><a href="{{ route('cart.index') }}">{{ __('Cart') }}</a></li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div class="footer-section" data-aos="fade-up" data-aos-delay="200">
                    <h3>{{ __('Customer Service') }}</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('contact') }}">{{ __('FAQ') }}</a></li>
                        <li><a href="{{ route('privacy') }}">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ route('terms') }}">{{ __('Terms & Conditions') }}</a></li>
                        <li><a href="{{ route('contact') }}">{{ __('Returns & Refunds') }}</a></li>
                        <li><a href="{{ route('contact') }}">{{ __('Shipping Info') }}</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-section" data-aos="fade-up" data-aos-delay="300">
                    <h3>{{ __('Contact Us') }}</h3>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $settings['address'] ?? 'New York, USA' }}</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:{{ $settings['contact_phone'] ?? '+11234567890' }}" style="color: inherit;"><span>{{ $settings['contact_phone'] ?? '+1 123 456 7890' }}</span></a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:{{ $settings['contact_email'] ?? 'info@medicamo.com' }}" style="color: inherit;"><span>{{ $settings['contact_email'] ?? 'info@medicamo.com' }}</span></a>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>{{ __('24/7 Customer Support') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Medicamo. {{ __('All Rights Reserved') }}.</p>
            </div>
        </div>
    </footer>

    <!-- Sticky Action Buttons -->
    <div class="sticky-actions">
        @if(!empty($settings['whatsapp_number']))
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp_number']) }}" 
           class="sticky-btn whatsapp-btn" 
           target="_blank" 
           title="Chat on WhatsApp">
            <i class="fab fa-whatsapp"></i>
            <span class="btn-tooltip">WhatsApp</span>
        </a>
        @endif
        @if(!empty($settings['contact_phone']))
        <a href="tel:{{ $settings['contact_phone'] }}" 
           class="sticky-btn phone-btn" 
           title="Call Us">
            <i class="fas fa-phone-alt"></i>
            <span class="btn-tooltip">Call Us</span>
        </a>
        @endif
        <button class="sticky-btn scroll-top-btn" id="backToTop" title="Back to Top">
            <i class="fas fa-chevron-up"></i>
            <span class="btn-tooltip">Top</span>
        </button>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AOS Animation JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/custom.js') }}"></script>

    @stack('scripts')
</body>

</html>
