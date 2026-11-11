<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'status' => 'pending', // Requires admin approval
        ]);

        return back()->with('success', 'Thank you for your review! It will be visible after approval.');
    }
}
