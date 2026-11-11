{{-- Header --}}
<header class="site-header">
    <div class="container">
        <nav class="navbar navbar-expand-lg">
            {{-- Logo --}}
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Medicamo" class="logo">
            </a>
            
            {{-- Mobile Menu Button --}}
            <button class="mobile-menu-btn d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>
            
            {{-- Navigation --}}
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home"></i> الرئيسية
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            <i class="fas fa-box"></i> المنتجات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
                            <i class="fas fa-info-circle"></i> من نحن
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                            <i class="fas fa-envelope"></i> اتصل بنا
                        </a>
                    </li>
                </ul>
                
                {{-- Icons --}}
                <div class="nav-icons">
                    {{-- Search --}}
                    <button class="nav-icon-btn" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fas fa-search"></i>
                    </button>
                    
                    {{-- User --}}
                    @auth
                        <div class="dropdown">
                            <button class="nav-icon-btn dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="fas fa-user"></i> حسابي</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.orders') }}"><i class="fas fa-shopping-bag"></i> طلباتي</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="nav-icon-btn">
                            <i class="fas fa-user"></i>
                        </a>
                    @endauth
                    
                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="nav-icon-btn cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count" id="cartCount">{{ $cartCount ?? 0 }}</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>

{{-- Search Modal --}}
<div class="modal fade" id="searchModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-4">
                <form action="{{ route('products.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-lg" placeholder="ابحث عن منتج..." autofocus>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
