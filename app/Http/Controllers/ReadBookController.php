<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ReadBook;
use Illuminate\Support\Facades\Auth;

class ReadBookController extends Controller
{
    /**
     * Get the read status of a book for the authenticated user
     * 
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($productId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        $product = Product::find($productId);
        
        if (!$product) {
            return response()->json(['error' => 'Book not found'], 404);
        }
        
        $isRead = $user->hasRead($productId);
        
        return response()->json([
            'is_read' => $isRead
        ]);
    }
    
    /**
     * Toggle the read status of a book for the authenticated user
     * 
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle($productId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        $product = Product::find($productId);
        
        if (!$product) {
            return response()->json(['error' => 'Book not found'], 404);
        }
        
        $readBook = ReadBook::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();
            
        if ($readBook) {
            // If already marked as read, remove it
            $readBook->delete();
            $message = 'Book marked as unread.';
            $isRead = false;
        } else {
            // Mark as read
            ReadBook::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
            $message = 'Book marked as read.';
            $isRead = true;
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'is_read' => $isRead
        ]);
    }
}
