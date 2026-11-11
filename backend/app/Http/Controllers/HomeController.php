<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->root()
            ->withCount('products')
            ->orderBy('sort_order')
            ->take(8)
            ->get();
        
        $featuredProducts = Product::published()
            ->featured()
            ->with('category')
            ->take(8)
            ->get();
        
        // Get products with active offers for homepage
        $offerProducts = Product::published()
            ->activeOffers()
            ->with('category')
            ->orderByDesc('is_offer_featured')
            ->orderByRaw('((price - sale_price) / price * 100) DESC')
            ->take(6)
            ->get();
        
        $testimonials = Review::forHomepage()
            ->with(['user', 'product'])
            ->latest()
            ->take(6)
            ->get();
        
        return view('pages.home', compact('categories', 'featuredProducts', 'offerProducts', 'testimonials'));
    }

    public function about()
    {
        $teamMembers = \App\Models\TeamMember::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
            
        return view('pages.about', compact('teamMembers'));
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $subjectLabels = [
            'order' => 'Order Inquiry',
            'product' => 'Product Inquiry',
            'complaint' => 'Complaint',
            'suggestion' => 'Suggestion',
            'other' => 'Other',
        ];

        $subjectLabel = $subjectLabels[$request->subject] ?? $request->subject;

        $body = "New Contact Form Submission\n";
        $body .= "========================\n\n";
        $body .= "Name: {$request->name}\n";
        $body .= "Email: {$request->email}\n";
        $body .= "Phone: " . ($request->phone ?? 'N/A') . "\n";
        $body .= "Subject: {$subjectLabel}\n";
        $body .= "========================\n\n";
        $body .= "Message:\n{$request->message}\n";
        $body .= "\n========================\n";
        $body .= "Sent from Medicamo Website at " . now()->format('Y-m-d H:i') . "\n";

        try {
            $adminEmail = \App\Models\Setting::getValue('admin_email', 'medicamodrug@gmail.com');
            
            \Illuminate\Support\Facades\Mail::raw($body, function ($mail) use ($request, $subjectLabel, $adminEmail) {
                $mail->to($adminEmail)
                     ->replyTo($request->email, $request->name)
                     ->subject("Contact Form: {$subjectLabel} - {$request->name}");
            });

            return redirect()->route('contact')->with('success', __('Thank you for your message! We will get back to you soon.'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Contact form email failed: ' . $e->getMessage());
            return redirect()->route('contact')->with('success', __('Thank you for your message! We will get back to you soon.'));
        }
    }
}
