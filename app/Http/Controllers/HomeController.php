<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\ReadBook; // Added for top genres
use App\Models\Forum; // Added for forum count
use Illuminate\Support\Facades\DB; // Added for DB facade

class HomeController extends Controller
{
    /**
     * Initial entry point for the application.
     * Redirects authenticated users to their dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::id()) {
            return $this->dashboard();
        }
    }

    /**
     * Handles redirection after user actions based on user type.
     * Redirects users to appropriate pages based on their role:
     * - Regular users go to library
     * - Admins go to upload page
     * - Others are redirected back
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectAfterBack()
    {
        $usertype = Auth()->user()->usertype;

        if ($usertype === 'user') {
            return redirect()->route('library');
        } else if ($usertype === 'admin') {
            return redirect()->route('uploadpage');
        } else {
            return redirect()->back();
        }
    }

    /**
     * Displays the post creation page.
     * Only accessible by admin users.
     *
     * @return \Illuminate\View\View
     */
    public function post()
    {
        return view('post');
    }

    /**
     * Displays the user's dashboard with personalized content.
     * Shows:
     * - Recent books (last 10)
     * - Books from user's preferred categories
     * For admins, also shows:
     * - Total book count
     * - Total user count
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // For all users: get the 10 most recent books
        $recentBooks = Product::orderBy('created_at', 'desc')->take(10)->get();

        $preferredBooks = [];
        $user = Auth::user();
        
        // Get the user's preferred categories and ensure they are public
        // For admin users, show all their preferred categories
        if ($user->isAdmin()) {
            $userPreferences = $user->preferredCategories()->get();
        } else {
            // For regular users, only show public categories
            $userPreferences = $user->preferredCategories()
                ->where('is_public', true)
                ->get();
        }

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
            $forumCount = Forum::count(); // Added forum count

            // Calculate Top Read Genres
            $topGenres = DB::table('read_books')
                ->join('products', 'read_books.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('count(DISTINCT read_books.user_id) as user_read_count')) // Count distinct users per genre
                ->groupBy('categories.name')
                ->orderBy('user_read_count', 'desc')
                ->take(3) // Top 3 genres
                ->get();

            return view('admin.adminhome', compact('bookCount', 'userCount', 'categoryCount', 'forumCount', 'recentBooks', 'preferredBooks', 'topGenres'));
        } else {
            // For regular users, potentially different logic or no topGenres
            return view('dashboard', compact('recentBooks', 'preferredBooks'));
        }
    }

    /**
     * Displays the welcome page carousel with books.
     * Shows books from public categories only.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function carousel(Request $request)
    {
        if (Auth::user() && Auth::user()->usertype === 'admin') {
            // For admin, load all books with their categories
            $data = Product::with('category')->get();
        } else {
            // For regular users, only load books from public categories
            $data = Product::whereHas('category', function ($query) {
                $query->where('is_public', true);
            })->with('category')->get();
        }

        return view('welcome', compact('data'));
    }
}
