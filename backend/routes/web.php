<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PaymobController;


// Access Destinations API
Route::get('/api/destinations/{parent}/children', function (\App\Models\Destination $parent) {
    return $parent->children()->where('rank', '>=', '2')->orderBy('name')->get(['id', 'name', 'price']);
})->name('api.destinations.children');

// Paymob Routes
Route::get('/paymob/callback', [PaymobController::class, 'processedCallback'])->name('paymob.callback');
Route::post('/paymob/webhook', [PaymobController::class, 'transactionCallback'])->name('paymob.webhook');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');

// Language Switching
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Jobs / Careers
Route::get('/careers', [\App\Http\Controllers\JobController::class, 'index'])->name('jobs.index');
Route::get('/careers/{slug}', [\App\Http\Controllers\JobController::class, 'show'])->name('jobs.show');
Route::get('/careers/{slug}/apply', [\App\Http\Controllers\JobController::class, 'apply'])->name('jobs.apply');
Route::post('/careers/{slug}/apply', [\App\Http\Controllers\JobController::class, 'submitApplication'])->name('jobs.submit');
Route::get('/careers/{slug}/success', [\App\Http\Controllers\JobController::class, 'success'])->name('jobs.success');

// Forgot Password with OTP
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('password.send-otp');
Route::get('/forgot-password/verify', [ForgotPasswordController::class, 'showVerifyForm'])->name('password.verify.form');
Route::post('/forgot-password/verify', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify-otp');
Route::post('/forgot-password/resend', [ForgotPasswordController::class, 'resendOtp'])->name('password.resend-otp');
Route::get('/forgot-password/reset', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');

// Coupons
Route::post('/coupon/apply', [\App\Http\Controllers\CouponController::class, 'apply'])->name('coupon.apply');
Route::post('/coupon/remove', [\App\Http\Controllers\CouponController::class, 'remove'])->name('coupon.remove');

// OTP Verification (Logged in but not verified)
Route::middleware('auth')->group(function () {
    Route::get('/verify-phone', [AuthController::class, 'showOtpVerify'])->name('otp.verify');
    Route::post('/verify-phone', [AuthController::class, 'verifyOtp'])->name('otp.verify.submit');
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');
});

// Protected Routes (Auth + Phone Verified)
Route::middleware(['auth', 'verified.phone'])->group(function () {
    // Checkout Routes - Prevent Admin Access
    Route::middleware(['prevent.admin'])->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
        Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    });
    
    // Profile - Prevent Admin (redirect to dashboard)
    Route::middleware(['prevent.admin'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
        Route::get('/profile/orders/{order}', [ProfileController::class, 'orderDetails'])->name('profile.order');
        Route::get('/profile/addresses', [ProfileController::class, 'addresses'])->name('profile.addresses');
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    });
    
    // Order Tracking
    Route::get('/orders/{order}/track', [\App\Http\Controllers\OrderController::class, 'track'])->name('order.track');
    
    // Wishlist
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/remove/{product}', [\App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/move-to-cart/{product}', [\App\Http\Controllers\WishlistController::class, 'moveToCart'])->name('wishlist.moveToCart');
    
    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Social Login Routes
Route::get('/auth/{provider}', [\App\Http\Controllers\SocialAuthController::class, 'redirect'])
    ->name('social.redirect')
    ->whereIn('provider', ['google', 'facebook']);

Route::get('/auth/{provider}/callback', [\App\Http\Controllers\SocialAuthController::class, 'callback'])
    ->name('social.callback')
    ->whereIn('provider', ['google', 'facebook']);

// Complete Profile (for social users without phone)
Route::middleware('auth')->group(function () {
    Route::get('/complete-profile', [\App\Http\Controllers\SocialAuthController::class, 'showCompleteProfile'])
        ->name('social.complete-profile');
    Route::post('/complete-profile', [\App\Http\Controllers\SocialAuthController::class, 'savePhone']);
});
