<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogService;

class BookController extends Controller
{
  /**
   * Display the book management page.
   * This page allows admins to manage and upload books.
   *
   * @return \Illuminate\View\View
   */
  public function uploadpage()
  {
    return view('book-manage');
  }

  /**
   * Store a newly uploaded book in the database.
   * Handles file upload, validation, and creates a new book record.
   * Requirements:
   * - Title and author (max 255 chars)
   * - Valid category ID
   * - PDF file (max 10MB)
   *
   * @param Request $request
   * @return \Illuminate\Http\RedirectResponse
   */
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

  /**
   * Display the book management interface with filtering and sorting options.
   * Features:
   * - Search by title
   * - Filter by visibility (public/private)
   * - Filter by genres
   * - Multiple sorting options
   * - Pagination
   * - AJAX support for dynamic updates
   *
   * @param Request $request
   * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
   */
  public function show(Request $request)
  {
    $query = $request->get('query');
    $visibility = $request->get('visibility', 'all');
    $genres = $request->get('genres') ? explode(',', $request->get('genres')) : [];
    $sort = $request->get('sort', 'newest');

    $data = Product::query()->withAvg('reviews', 'review_score');

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
        $data->orderBy('reviews_avg_review_score', 'asc');
        break;
      case 'rating_desc':
        $data->orderBy('reviews_avg_review_score', 'desc');
        break;
      default: // 'newest'
        $data->orderBy('created_at', 'desc');
    }

    $data = $data->paginate(15)->withQueryString();
    $categories = Category::all();

    $data->each(function ($book) {
      $book->rating = $book->reviews_avg_review_score ?? 0;
    });

    if ($request->ajax()) {
      return response()->json([
        'html' => view('components.book-grid', compact('data'))->render(),
        'pagination' => view('vendor.pagination.tailwind', ['paginator' => $data])->render()
      ]);
    }

    return view('book-manage', compact('data', 'categories', 'visibility', 'sort'));
  }

  /**
   * Display the public library view with books.
   * Shows only public books with filtering and sorting options.
   * Features:
   * - Search by title
   * - Filter by genres
   * - Multiple sorting options
   * - Pagination
   *
   * @param Request $request
   * @return \Illuminate\View\View
   */
  public function library(Request $request)
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

  /**
   * Retrieve book information for editing.
   * Returns JSON response with book details.
   *
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function edit($id)
  {
    $product = Product::findOrFail($id);
    return response()->json($product);
  }

  /**
   * Update book information.
   * Tracks and logs all changes made to the book.
   * Handles updates for:
   * - Title
   * - Author
   * - Category
   * - Visibility status
   *
   * @param Request $request
   * @param int $id
   * @return \Illuminate\Http\RedirectResponse
   */
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

  /**
   * Display detailed view of a specific book.
   * Shows book information and reviews.
   * Handles visibility permissions:
   * - Public books visible to all
   * - Private books only visible to admins
   *
   * @param int $id
   * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
   */
  public function view($id)
  {
    $product = Product::findOrFail($id);

    // Check if book is private and user is not admin
    if (!$product->is_public && (!Auth::check() || Auth::user()->usertype !== 'admin')) {
      return redirect()->route('library')->with('error', 'You do not have permission to view this book.');
    }

    // Track last read book for authenticated users
    if (Auth::check()) {
      Auth::user()->update([
        'last_read_book_id' => $product->id
      ]);
    }

    $data = $product;
    $reviews = $product->reviews()->latest()->get();

    return view('viewproduct', compact('product', 'reviews', 'data'));
  }

  /**
   * Delete a book and its associated files.
   * Performs:
   * - Deletion of associated reviews
   * - Removal of PDF file from storage
   * - Deletion of database record
   * - Logging of deletion
   *
   * @param int $id
   * @return \Illuminate\Http\RedirectResponse
   */
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

  /**
   * Download a book's PDF file.
   *
   * @param Request $request
   * @param string $file
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function download(Request $request, $file)
  {
    return response()->download(public_path('assets/' . $file));
  }

  /**
   * Toggle a book's visibility status between public and private.
   * Returns JSON response with updated status.
   *
   * @param Request $request
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
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

  /**
   * Handle AJAX requests for book data.
   * Returns JSON with rendered book grid and pagination.
   * Features:
   * - Search by title
   * - Filter by genres
   * - Multiple sorting options
   * - Only returns public books
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
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
