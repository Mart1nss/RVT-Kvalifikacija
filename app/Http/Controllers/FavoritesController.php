<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Iecienīto grāmatu kontrolieris
 * 
 * Šis kontrolieris apstrādā lietotāja iecienīto grāmatu kolekcijas pārvaldību,
 * ieskaitot grāmatu pievienošanu un noņemšanu no kolekcijas.
 */
class FavoritesController extends Controller
{
    /**
     * Parāda lietotāja iecienīto grāmatu kolekcijas skatu
     * 
     * @param Request
     * @return \Illuminate\View\View
     */
    public function favorites(Request $request)
    {
        $tab = $request->get('tab', 'favorites');
        return view('my-collection', compact('tab'));
    }

    /**
     * Pievieno grāmatu lietotāja iecienīto sarakstam
     * 
     * Pārbauda, vai grāmata jau nav pievienota iecienītajiem, un ja nav,
     * izveido jaunu ierakstu Favorite tabulā. Atgriež atbilstošu paziņojumu.
     * 
     * @param int
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Noņem grāmatu no lietotāja iecienītajiem
     * 
     * Atrod un dzēš atbilstošo iecienīto grāmatu ierakstu.
     * Atgriež atbildi ar paziņojumu par veiksmīgu noņemšanu.
     * 
     * @param int
     * @return \Illuminate\Http\Response
     */
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