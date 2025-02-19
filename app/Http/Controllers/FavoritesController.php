<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    public function favorites(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'favorites');

        // Load only the data needed for the active tab
        $favorites = $tab === 'favorites' ? $user->favorites()->with('product')->get() : collect();
        $readLater = $tab === 'readlater' ? $user->readLater()->with('product')->get() : collect();

        return view('my-collection', compact('favorites', 'readLater', 'tab'));
    }

    public function add($id)
    {
        $user = Auth::user();

        $exists = Favorite::where('user_id', $user->id)
            ->where('product_id', $id)
            ->exists();

        if ($exists) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Book is already added to favorites!'], 400);
            }
            return redirect()->back()->with('error', 'Book is already added to favorites!');
        }

        $favorite = new Favorite();
        $favorite->user_id = $user->id;
        $favorite->product_id = $id;
        $favorite->save();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Book added to favorites!']);
        }
        return redirect()->back()->with('success', 'Book added to favorites!');
    }

    public function delete($id)
    {
        $favorite = Favorite::where('user_id', Auth::user()->id)
            ->where('product_id', $id)
            ->firstOrFail();

        $favorite->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Book removed from favorites!']);
        }
        return redirect()->back()->with('success', 'Book removed from favorites!');
    }
}


