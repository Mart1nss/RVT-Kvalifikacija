<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;

class ReviewController extends Controller
{

    public function store(Request $request, Product $product)
    {

        $request->validate([
            'review_score' => 'required|integer|between:1,5',
            'review_text' => 'required'
        ]);

        $review = Review::create([
            'review_score' => $request->review_score,
            'review_text' => $request->review_text,
            'user_id' => auth()->id(),
            'product_id' => $product->id
        ]);

        return back()->with('success', 'Review added successfully!');
    }


    public function edit(string $id)
    {
        
    }

    public function update(Request $request, string $id)
    {
    
    }



    public function destroy(Product $product, Review $review)
    {
        // Allow admins to delete any review, regular users can only delete their own
        if (!auth()->user()->isAdmin() && $review->user_id != Auth::id()) {
            return back()->with('error', 'You are not authorized to delete this review.');
        }

        $review->delete();

        return back()->with('success', 'Review deleted successfully!');
    }
}
