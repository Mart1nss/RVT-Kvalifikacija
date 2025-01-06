<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Review;
use App\Models\Product;
use App\Models\Notification;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{

    public function index()
    {
        if (Auth::id()) {
            $usertype = Auth()->user()->usertype;

            if ($usertype == 'user') {
                return view('dashboard');
            } else if ($usertype == 'admin') {
                return $this->dashboard();
            } else {
                return redirect()->back();
            }
        }
    }

    public function redirectAfterBack()
    {
        $usertype = Auth()->user()->usertype;

        if ($usertype === 'user') {
            return redirect()->route('bookpage');
        } else if ($usertype === 'admin') {
            return redirect()->route('uploadpage');
        } else {
            return redirect()->back();
        }
    }

    public function post()
    {
        return view('post');
    }

    public function uploadpage()
    {
        return view('product');
    }


    public function dashboard()
    {
        $bookCount = Product::count();
        $userCount = User::count();
        $recentBooks = Product::orderBy('created_at', 'desc')->take(5)->get();
        
        return view('admin.adminhome', compact('bookCount', 'userCount', 'recentBooks'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'file' => 'required|mimes:pdf|max:10240' // 10240 KB = 10 MB
        ], [
            'file.max' => 'The file size must not exceed 10MB.',
            'file.mimes' => 'The file must be a PDF document.',
            'file.required' => 'Please select a PDF file.'
        ]);

        $product = new Product;
        $product->title = $request->title;
        $product->author = $request->author;
        $product->category_id = $request->category_id;
        $product->is_public = $request->has('is_public');
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move('assets', $filename);
            $product->file = $filename;
        }

        $product->save();

        return redirect()->back()->with('success', 'Book uploaded successfully!');
    }

    public function show(Request $request)
    {
        $query = $request->get('query');
        $visibility = $request->get('visibility', 'all'); // Default to 'all'
        
        $data = Product::query();

        if ($query) {
            $data->where('title', 'like', '%' . $query . '%');
        }

        // Apply visibility filter
        if ($visibility === 'public') {
            $data->where('is_public', true);
        } elseif ($visibility === 'private') {
            $data->where('is_public', false);
        }

        $data = $data->get();
        $categories = Category::all();
        return view('product', compact('data', 'categories', 'visibility'));
    }

    public function bookpage(Request $request)
    {
        $query = $request->get('query');
        $data = Product::query();

        if ($query) {
            $data->where('title', 'like', '%' . $query . '%');
        }

        // Only show public books in the library
        $data = $data->where('is_public', true)->get();
        return view('allBooks', compact('data'));
    }


    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $product->title = $request->title;
        $product->author = $request->author;
        $product->category_id = $request->category_id;
        $product->is_public = $request->has('is_public');
        
        // Update category name for backward compatibility
        if ($request->category_id) {
            $category = Category::find($request->category_id);
        }
        
        $product->save();
        
        return redirect()->back()->with('success', 'Book updated successfully');
    }

    public function carousel(Request $request)
    {
        // Only show public books to non-admin users
        if (Auth::user() && Auth::user()->usertype === 'admin') {
            $data = Product::all();
        } else {
            $data = Product::where('is_public', true)->get();
        }

        return view('welcome', compact('data'));
    }

    public function download(Request $request, $file)
    {
        return response()->download(public_path('assets/' . $file));
    }

    public function view($id)
    {
        $product = Product::findOrFail($id);

        // Check if book is private and user is not admin
        if (!$product->is_public && (!Auth::check() || Auth::user()->usertype !== 'admin')) {
            return redirect()->route('bookpage')->with('error', 'You do not have permission to view this book.');
        }

        $data = $product;  // For backward compatibility
        $reviews = $product->reviews()->latest()->get();

        return view('viewproduct', compact('product', 'reviews', 'data'));
    }

    public function destroy($id)
    {
        $data = Product::find($id);
        if ($data) {

            // Delete the file from the public/assets directory
            $data->reviews()->delete();
            $filePath = public_path('assets/' . $data->file);
            if (file_exists($filePath)) {
                unlink($filePath);
            }


            $data->delete();

            return redirect()->back()->with('success', 'Book deleted successfully!');
        } else {
            return redirect()->back()->withErrors(['error' => 'nav labi']);
        }
    }

    public function toggleVisibility(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->is_public = !$product->is_public;
        $product->save();
        
        return response()->json([
            'success' => true,
            'is_public' => $product->is_public
        ]);
    }



}