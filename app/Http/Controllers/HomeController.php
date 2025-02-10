<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\AuditLogService;

class HomeController extends Controller
{

    public function index()
    {
        if (Auth::id()) {
            return $this->dashboard();
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
        return view('book-manage');
    }


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



    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'file' => 'required|mimes:pdf|max:10240'
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

        AuditLogService::log(
            "Uploaded book",
            "book",
            "Uploaded new book",
            $product->id,
            $product->title
        );

        return redirect()->back()->with('success', 'Book uploaded successfully!');
    }

    public function show(Request $request)
    {
        $query = $request->get('query');
        $visibility = $request->get('visibility', 'all');
        $genres = $request->get('genres') ? explode(',', $request->get('genres')) : [];
        $sort = $request->get('sort', 'newest');

        $data = Product::query();

        if ($query) {
            $data->where('title', 'like', '%' . $query . '%');
        }

        if ($visibility === 'public') {
            $data->where('is_public', true);
        } elseif ($visibility === 'private') {
            $data->where('is_public', false);
        }

        if (!empty($genres)) {
            $data->whereHas('category', function ($q) use ($genres) {
                $q->whereIn('name', $genres);
            });
        }

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $data->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $data->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $data->orderBy('title', 'desc');
                break;
            case 'author_asc':
                $data->orderBy('author', 'asc');
                break;
            case 'author_desc':
                $data->orderBy('author', 'desc');
                break;
            case 'rating_asc':
                $data->withAvg('reviews', 'review_score')
                    ->orderBy('reviews_avg_review_score', 'asc');
                break;
            case 'rating_desc':
                $data->withAvg('reviews', 'review_score')
                    ->orderBy('reviews_avg_review_score', 'desc');
                break;
            default: // 'newest'
                $data->orderBy('created_at', 'desc');
        }

        $data = $data->paginate(15)->withQueryString();
        $categories = Category::all();
        return view('book-manage', compact('data', 'categories', 'visibility', 'sort'));
    }

    public function bookpage(Request $request)
    {
        $query = $request->get('query');
        $genres = $request->get('genres') ? explode(',', $request->get('genres')) : [];
        $sort = $request->get('sort', 'newest');

        $data = Product::query()
            ->where('is_public', true)
            ->withAvg('reviews', 'review_score')
            ->when($query, function ($q) use ($query) {
                return $q->where('title', 'like', '%' . $query . '%');
            })
            ->when(!empty($genres), function ($q) use ($genres) {
                return $q->whereHas('category', function ($sq) use ($genres) {
                    $sq->whereIn('name', $genres);
                });
            });

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $data->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $data->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $data->orderBy('title', 'desc');
                break;
            case 'author_asc':
                $data->orderBy('author', 'asc');
                break;
            case 'author_desc':
                $data->orderBy('author', 'desc');
                break;
            case 'rating_asc':
                $data->orderBy('reviews_avg_review_score', 'asc');
                break;
            case 'rating_desc':
                $data->orderBy('reviews_avg_review_score', 'desc');
                break;
            default: // 'newest'
                $data->orderBy('created_at', 'desc');
        }

        $data = $data->paginate(15)->withQueryString();

        $data->each(function ($book) {
            $book->rating = $book->reviews_avg_review_score ?? 0;
        });

        return view('library', compact('data', 'sort'));
    }



    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Store old values before update
        $changes = [];

        if ($product->title !== $request->title) {
            $changes[] = "title from '{$product->title}' to '{$request->title}'";
        }

        if ($product->author !== $request->author) {
            $changes[] = "author from '{$product->author}' to '{$request->author}'";
        }

        if ((int) $product->category_id !== (int) $request->category_id) {
            $oldCategory = Category::find($product->category_id);
            $newCategory = Category::find($request->category_id);
            if ($oldCategory && $newCategory) {
                $changes[] = "category from '{$oldCategory->name}' to '{$newCategory->name}'";
            }
        }

        $oldIsPublic = (bool) $product->is_public;
        $newIsPublic = (bool) $request->has('is_public');

        if ($oldIsPublic !== $newIsPublic) {
            $oldVisibility = $oldIsPublic ? 'public' : 'private';
            $newVisibility = $newIsPublic ? 'public' : 'private';
            $changes[] = "visibility from {$oldVisibility} to {$newVisibility}";
        }

        $product->title = $request->title;
        $product->author = $request->author;
        $product->category_id = $request->category_id;
        $product->is_public = $newIsPublic;

        $product->save();

        if (!empty($changes)) {
            $changeDescription = "Changed " . implode(', ', $changes);
            AuditLogService::log(
                "Updated book",
                "book",
                $changeDescription,
                $product->id,
                $product->title
            );
        }

        return redirect()->back()->with('success', 'Book updated successfully');
    }

    public function carousel(Request $request)
    {
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

        $data = $product;
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

            AuditLogService::log(
                "Deleted book",
                "book",
                "Deleted book",
                $id,
                $data->title
            );

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

    public function ajaxBooks(Request $request)
    {
        $query = $request->get('query');
        $genres = $request->get('genres') ? explode(',', $request->get('genres')) : [];
        $sort = $request->get('sort', 'newest');

        $data = Product::query()
            ->where('is_public', true)
            ->withAvg('reviews', 'review_score');

        if ($query) {
            $data->where('title', 'like', '%' . $query . '%');
        }

        if (!empty($genres)) {
            $data->whereHas('category', function ($q) use ($genres) {
                $q->whereIn('name', $genres);
            });
        }

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $data->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $data->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $data->orderBy('title', 'desc');
                break;
            case 'author_asc':
                $data->orderBy('author', 'asc');
                break;
            case 'author_desc':
                $data->orderBy('author', 'desc');
                break;
            case 'rating_asc':
                $data->orderBy('reviews_avg_review_score', 'asc');
                break;
            case 'rating_desc':
                $data->orderBy('reviews_avg_review_score', 'desc');
                break;
            default: // 'newest'
                $data->orderBy('created_at', 'desc');
        }

        $data = $data->paginate(15);

        $data->each(function ($book) {
            $book->rating = $book->reviews_avg_review_score ?? 0;
        });

        return response()->json([
            'html' => view('components.book-grid', compact('data'))->render(),
            'pagination' => view('vendor.pagination.tailwind', ['paginator' => $data])->render()
        ]);
    }

}