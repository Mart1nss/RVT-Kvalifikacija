<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\ReadBook;
use App\Models\Forum;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Parāda vadības paneli
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Visiem lietotājiem rāda jaunākās 10 publiskās grāmatas
        $recentBooks = Product::whereHas('category', function ($query) {
                $query->where('is_public', true);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $preferredBooks = [];
        $user = Auth::user();
        
        // Iegūst lietotāja izvēlētās kategorijas, tikai publiskas kategorijas
        $userPreferences = $user->preferredCategories()
            ->where('is_public', true)
            ->get();

        foreach ($userPreferences as $category) {
            $books = Product::where('category_id', $category->id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
            if ($books->count() > 0) {
                $preferredBooks[$category->name] = $books;
            }
        }

        if ($user->usertype == 'admin') {
            $bookCount = Product::count();
            $userCount = User::count();
            $categoryCount = Category::count();
            $forumCount = Forum::count();

            // Aprēķina populārākos lasītos žanrus
            $topGenres = DB::table('read_books')
                ->join('products', 'read_books.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('count(DISTINCT read_books.user_id) as user_read_count'))
                ->groupBy('categories.name')
                ->orderBy('user_read_count', 'desc')
                ->take(3)
                ->get();

            return view('admin.adminhome', compact('bookCount', 'userCount', 'categoryCount', 'forumCount', 'recentBooks', 'preferredBooks', 'topGenres'));
        } else {
            return view('dashboard', compact('recentBooks', 'preferredBooks'));
        }
    }

    /**
     * Parāda sākuma lapas karuseli ar grāmatām.
     * Rāda grāmatas tikai no publiskajām kategorijām.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function carousel(Request $request)
    {
        $data = Product::whereHas('category', function ($query) {
            $query->where('is_public', true);
        })->with('category')->get();
        

        return view('welcome', compact('data'));
    }
}
