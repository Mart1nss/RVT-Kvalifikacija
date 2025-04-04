<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

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
        $recentBooks = Product::orderBy('created_at', 'desc')->take(10)->get();

        $userPreferences = Auth::user()->preferredCategories()->get();
        $preferredBooks = [];

        foreach ($userPreferences as $category) {
            $books = Product::where('category_id', $category->id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
            if ($books->count() > 0) {
                $preferredBooks[$category->name] = $books;
            }
        }

        if (Auth::user()->usertype == 'admin') {
            $bookCount = Product::count();
            $userCount = User::count();
            return view('admin.adminhome', compact('bookCount', 'userCount', 'recentBooks', 'preferredBooks'));
        } else {
            return view('dashboard', compact('recentBooks', 'preferredBooks'));
        }
    }

    /**
     * Displays the welcome page carousel with books.
     * Shows all books for admin users, but only public books for regular users.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function carousel(Request $request)
    {
        if (Auth::user() && Auth::user()->usertype === 'admin') {
            $data = Product::all();
        } else {
            $data = Product::where('is_public', 1)->get();
        }

        return view('welcome', compact('data'));
    }
}