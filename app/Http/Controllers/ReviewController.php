<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }



    public function destroy(Product $product, Review $review)
    {
        // Ensure the user is the owner of the review
        if ($review->user_id != Auth::id()) {
            return back()->with('error', 'You are not authorized to delete this review.');
        }

        $review->delete();

        return back()->with('success', 'Review deleted successfully!');
    }
}
