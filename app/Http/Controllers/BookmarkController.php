<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'page_number' => 'required|integer',
                'scroll_position' => 'required|integer'
            ]);

            $bookmark = Bookmark::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'product_id' => $validated['product_id']
                ],
                [
                    'page_number' => $validated['page_number'],
                    'scroll_position' => $validated['scroll_position']
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Bookmark saved successfully',
                'bookmark' => $bookmark
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving bookmark: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($productId)
    {
        try {
            $bookmark = Bookmark::where('product_id', $productId)
                ->where('user_id', Auth::id())
                ->first();

            return response()->json($bookmark);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching bookmark: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($productId)
    {
        try {
            Bookmark::where('product_id', $productId)
                ->where('user_id', Auth::id())
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bookmark removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing bookmark: ' . $e->getMessage()
            ], 500);
        }
    }
}
