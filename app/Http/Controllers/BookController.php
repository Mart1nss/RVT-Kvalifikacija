<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

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
   *
   * @param Request $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(Request $request)
  {
    // Check if book already exists
    $existingBook = Product::where('title', $request->title)
                             ->where('author', $request->author)
                             ->first();

    if ($existingBook) {
      return redirect()->back()->with('error', 'This book has already been uploaded.')->withInput();
    }

    $request->validate([
      'title' => 'required|string|max:100',
      'author' => 'required|string|max:50',
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

    if ($request->hasFile('file')) {
      $file = $request->file('file');
      $filename = Str::slug($request->title) . '_' . time() . '.' . $file->getClientOriginalExtension();
      $file->storeAs('books', $filename);
      $product->file = $filename;

      // Save the product first to get an ID
      $product->save();

      // Generate thumbnail
      $this->generateThumbnail($filename);
    } else {
      $product->save();
    }

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
   * Generate a thumbnail for a PDF file using Ghostscript
   *
   * @param string $filename
   * @return bool
   */
  private function generateThumbnail($filename)
  {
    // Create the thumbnails directory if it doesn't exist
    $thumbnailDir = public_path('book-thumbnails');
    if (!file_exists($thumbnailDir)) {
      mkdir($thumbnailDir, 0755, true);
    }

    // Generate the thumbnail filename (change .pdf to .jpg)
    $thumbnailFilename = str_replace('.pdf', '.jpg', $filename);
    $thumbnailPath = $thumbnailDir . '/' . $thumbnailFilename;

    // Check if thumbnail already exists
    if (file_exists($thumbnailPath)) {
      return true;
    }

    // Get PDF file path
    $pdfPath = storage_path('app/books/' . $filename);
    if (!file_exists($pdfPath)) {
      return false;
    }

    // get gs exe
    $gsExecutable = 'C:\\Program Files\\gs\\gs10.05.1\\bin\\gswin64c.exe';

    if (file_exists($gsExecutable)) {
      $command = '"' . $gsExecutable . '" -sDEVICE=jpeg -dNOPAUSE -dBATCH -dSAFER '
        . '-dFirstPage=1 -dLastPage=1 -r150 '
        . '-dTextAlphaBits=4 -dGraphicsAlphaBits=4 '
        . '-o "' . $thumbnailPath . '" '
        . '"' . $pdfPath . '"';

      exec($command, $output, $return_var);

      if ($return_var === 0 && file_exists($thumbnailPath)) {
        // Optimize the thumbnail size if needed
        $this->resizeThumbnail($thumbnailPath);
        return true;
      }
    }

    return false;
  }

  /**
   * Resize a thumbnail image to standard dimensions
   *
   * @param string $thumbnailPath
   * @return bool
   */
  private function resizeThumbnail($thumbnailPath)
  {
    if (!extension_loaded('imagick')) {
      return false;
    }

    $maxWidth = 400;
    $maxHeight = 566;

    $imagick = new \Imagick($thumbnailPath);
    $imagick->setImageFormat('jpg');
    $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
    $imagick->setImageCompressionQuality(85);

    $width = $imagick->getImageWidth();
    $height = $imagick->getImageHeight();

    if ($width > $maxWidth || $height > $maxHeight) {
      // Resize while maintaining aspect ratio
      $ratioWidth = $maxWidth / $width;
      $ratioHeight = $maxHeight / $height;
      $ratio = min($ratioWidth, $ratioHeight);

      $newWidth = $width * $ratio;
      $newHeight = $height * $ratio;

      $imagick->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1);
    }

    $imagick->writeImage($thumbnailPath);
    $imagick->clear();
    $imagick->destroy();

    return true;
  }

  /**
   * Display detailed view of a specific book.
   * Shows book information and reviews.
   * Handles visibility permissions:
   * - Books visible if category is public
   * - Books in private categories only visible to admins
   *
   * @param int $id
   * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
   */
  public function view($id)
  {
    $product = Product::findOrFail($id);
    $category = $product->category;

    // Check if category is private and user is not admin
    if (!$category->is_public && (!Auth::check() || Auth::user()->usertype !== 'admin')) {
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
   * Download a book's PDF file.
   *
   * @param Request $request
   * @param string $file
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function download(Request $request, $file)
  {
    $path = storage_path('app/books/' . $file);
    if (!file_exists($path)) {
      return redirect()->back()->with('error', 'File not found.');
    }
    return response()->download($path);
  }

  /**
   * Serve a PDF file for thumbnail generation.
   *
   * @param string $file
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function servePdf($file)
  {
    $path = storage_path('app/books/' . $file);
    if (!file_exists($path)) {
      abort(404);
    }
    return response()->file($path, ['Content-Type' => 'application/pdf']);
  }

  /**
   * Serve thumbnail images
   *
   * @param string $filename
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function serveThumbnail($filename)
  {
    $path = public_path('book-thumbnails/' . $filename);

    // If thumbnail doesn't exist, try to generate it
    if (!file_exists($path)) {
      $pdfFilename = str_replace('.jpg', '.pdf', $filename);
      $this->generateThumbnail($pdfFilename);
    }

    // Check again after possible generation
    if (!file_exists($path)) {
      abort(404);
    }

    return response()->file($path, ['Content-Type' => 'image/jpeg']);
  }
}
