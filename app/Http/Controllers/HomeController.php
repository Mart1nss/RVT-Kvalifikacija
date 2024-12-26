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
        $product = new Product;

        $product->title = $request->title;
        $product->author = $request->author;
        $product->category_id = $request->category_id;
        
        // Get category name for backward compatibility
        if ($request->category_id) {
            $category = Category::find($request->category_id);
        }

        $file = $request->file;
        if($file)
        {
            $filename = md5($file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move('assets', $filename);
            $product->file = $filename;
        }

        $product->save();

        return redirect()->back()->with('message', 'Book Added Successfully');
    }

    public function show(Request $request)
    {
        $query = $request->get('query');

        $data = product::query();

        if ($query) {
            $data->where('title', 'like', '%' . $query . '%');
        }

        $data = product::all();
        return view('product', compact('data'));
    }

    public function bookpage(Request $request)
    {
        $query = $request->get('query');

        $data = product::query();

        if ($query) {
            $data->where('title', 'like', '%' . $query . '%');
        }

        $data = product::all();
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
        
        // Update category name for backward compatibility
        if ($request->category_id) {
            $category = Category::find($request->category_id);
        }
        
        $product->save();
        
        return redirect()->back()->with('success', 'Book updated successfully');
    }

    public function carousel(Request $request)
    {
        $data = product::all();

        return view('welcome', compact('data'));
    }

    public function download(Request $request, $file)
    {
        return response()->download(public_path('assets/' . $file));
    }

    public function view($id)
    {
        $data = Product::find($id);
        $product = Product::findOrFail($id);
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



}