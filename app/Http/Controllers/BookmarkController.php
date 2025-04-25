<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'page_number' => 'required|integer',
        ]);

        $bookmark = Bookmark::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $validated['product_id']
            ],
            [
                'page_number' => $validated['page_number']
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Bookmark saved successfully',
            'bookmark' => $bookmark
        ]);
    }

    public function show($productId)
    {
        $bookmark = Bookmark::where('product_id', $productId)
            ->where('user_id', Auth::id())
            ->first(['page_number']);

        return response()->json($bookmark);
    }

    public function destroy($productId)
    {
        $deleted = Bookmark::where('product_id', $productId)
            ->where('user_id', Auth::id())
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Bookmark removed successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Bookmark not found or already removed'
            ], 404);
        }
    }
}
