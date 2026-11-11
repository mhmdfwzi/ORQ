{{-- Footer --}}
<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="row">
                {{-- About Section --}}
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-section">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Medicamo" class="footer-logo mb-3">
                        <p>Medicamo - شركة رائدة في إنتاج المستحضرات الصيدلانية والعلاجية. نلتزم بتقديم منتجات عالية الجودة لصحتك وعافيتك.</p>
                        <div class="social-links mt-3">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="https://wa.me/201070725224" class="social-link"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                
                {{-- Quick Links --}}
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-section">
                        <h4>روابط سريعة</h4>
                        <ul class="footer-links">
                            <li><a href="{{ route('home') }}">الرئيسية</a></li>
                            <li><a href="{{ route('products.index') }}">المنتجات</a></li>
                            <li><a href="{{ route('about') }}">من نحن</a></li>
                            <li><a href="{{ route('contact') }}">اتصل بنا</a></li>
                        </ul>
                    </div>
                </div>
                
                {{-- Customer Service --}}
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-section">
                        <h4>خدمة العملاء</h4>
                        <ul class="footer-links">
                            <li><a href="{{ route('profile.orders') }}">تتبع الطلب</a></li>
                            <li><a href="{{ route('privacy') }}">سياسة الخصوصية</a></li>
                            <li><a href="{{ route('terms') }}">الشروط والأحكام</a></li>
                            <li><a href="{{ route('contact') }}">الأسئلة الشائعة</a></li>
                        </ul>
                    </div>
                </div>
                
                {{-- Contact Info --}}
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-section">
                        <h4>تواصل معنا</h4>
                        <ul class="contact-info">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>القاهرة، مصر</span>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <span dir="ltr">+20 107 072 5224</span>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <span>info@medicamo.com</span>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span>السبت - الخميس: 9ص - 9م</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Copyright --}}
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} Medicamo. جميع الحقوق محفوظة.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <img src="{{ asset('assets/images/payment-methods.png') }}" alt="Payment Methods" class="payment-methods" onerror="this.style.display='none'">
                </div>
            </div>
        </div>
    </div>
</footer>
