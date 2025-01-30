<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{

    public function favorites()
    {
        $user = Auth::user();
        $favorites = $user->favorites()->with('product')->get();
        $readLater = $user->readLater()->with('product')->get();

        return view('favorites', compact('favorites', 'readLater'));
    }
    public function add($id)
    {
        $user = Auth::user();

        $exists = Favorite::where('user_id', $user->id)
            ->where('product_id', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Book is already added to favorites!');
        }

        $favorite = new Favorite();
        $favorite->user_id = $user->id;
        $favorite->product_id = $id;
        $favorite->save();

        return redirect()->back()->with('success', 'Book added to favorites!');

    }

    public function delete($id)
    {
        $favorite = Favorite::where('user_id', Auth::user()->id)
            ->where('product_id', $id)
            ->firstOrFail();

        $favorite->delete();

        return redirect()->back()->with('success', 'Book removed from favorites!')
            ->withInput(['tab' => request('current_tab', 'favorites')]);
    }
}


