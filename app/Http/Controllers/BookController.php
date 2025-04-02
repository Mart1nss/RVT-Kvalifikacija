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
use Spatie\PdfToImage\Pdf;
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
   * Requirements:
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
      $filename = Str::slug($request->title) . '_' . time() . '.' . $file->getClientOriginalExtension();
      $file->storeAs('books', $filename);
      $product->file = $filename;

      // Save the product first to get an ID
      $product->save();

      // Generate and save thumbnail with the actual title and author
      $this->generateThumbnail($filename, $product->title, $product->author);
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
   * Generate a thumbnail for a PDF file
   *
   * @param string $filename
   * @param string $title
   * @param string $author
   * @return bool
   */
  private function generateThumbnail($filename, $title = null, $author = null)
  {
    try {
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

      // Check if the PDF file exists
      $pdfPath = storage_path('app/books/' . $filename);
      if (!file_exists($pdfPath)) {
        return $this->generateEnhancedThumbnail($thumbnailPath, $title, $author);
      }

      // Try using Ghostscript
      $gsExecutable = 'C:\\Program Files\\gs\\gs10.05.0\\bin\\gswin64c.exe';

      if (file_exists($gsExecutable)) {
        $command = '"' . $gsExecutable . '" -sDEVICE=jpeg -dNOPAUSE -dBATCH -dSAFER '
          . '-dFirstPage=1 -dLastPage=1 -r150 '
          . '-dTextAlphaBits=4 -dGraphicsAlphaBits=4 '
          . '-o "' . $thumbnailPath . '" '
          . '"' . $pdfPath . '"';

        exec($command, $output, $return_var);

        if ($return_var === 0 && file_exists($thumbnailPath)) {
          // Optimize the thumbnail size if needed
          if (extension_loaded('imagick')) {
            $this->resizeThumbnail($thumbnailPath);
          }
          return true;
        }
      }

      // Try using direct Imagick if available
      if (extension_loaded('imagick')) {
        try {
          $imagick = new \Imagick();
          $imagick->setResolution(150, 150);
          $imagick->readImage($pdfPath . '[0]');
          $imagick->setImageFormat('jpg');
          $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
          $imagick->setImageCompressionQuality(85);
          $imagick->resizeImage(400, 0, \Imagick::FILTER_LANCZOS, 1);
          $imagick->writeImage($thumbnailPath);
          $imagick->clear();
          $imagick->destroy();

          return true;
        } catch (\Exception $e) {
          // Fall through to next method if this fails
        }
      }

      // If all PDF methods failed, use enhanced placeholder
      return $this->generateEnhancedThumbnail($thumbnailPath, $title, $author);
    } catch (\Exception $e) {
      // Log error but don't fail the upload
      \Log::error('Failed to generate thumbnail: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Generate an enhanced thumbnail with book title and author
   * 
   * @param string $thumbnailPath
   * @param string $title
   * @param string $author
   * @return bool
   */
  private function generateEnhancedThumbnail($thumbnailPath, $title, $author)
  {
    try {
      // Create image with standard book dimensions
      $width = 400;
      $height = 566;
      $img = imagecreatetruecolor($width, $height);

      // Define colors
      $bgColor = imagecolorallocate($img, 35, 45, 75);       // Deep blue
      $accentColor = imagecolorallocate($img, 80, 120, 180); // Bright blue accent
      $textColor = imagecolorallocate($img, 240, 240, 240);  // White text
      $overlayColor = imagecolorallocate($img, 25, 35, 65);  // Darker overlay
      $highlightColor = imagecolorallocate($img, 255, 140, 0); // Orange highlight

      // Fill background
      imagefill($img, 0, 0, $bgColor);

      // Create gradient-like background
      for ($i = 0; $i < $height; $i += 30) {
        $shade = min(180, 80 + $i / 4);
        $patternColor = imagecolorallocate($img, 30, 40, $shade);
        imagefilledrectangle($img, 0, $i, $width, $i + 15, $patternColor);
      }

      // Create central text area
      $centerX1 = $width / 4;
      $centerY1 = $height / 3 - 30;
      $centerX2 = $width * 3 / 4;
      $centerY2 = $height * 2 / 3 + 30;

      imagefilledrectangle($img, $centerX1, $centerY1, $centerX2, $centerY2, $overlayColor);
      imagerectangle($img, $centerX1, $centerY1, $centerX2, $centerY2, $accentColor);
      imagerectangle($img, $centerX1 + 3, $centerY1 + 3, $centerX2 - 3, $centerY2 - 3, $highlightColor);

      // Draw PDF badge
      $badgeSize = 60;
      $badgeY = $height / 3 - 15;
      imagefilledellipse($img, $width / 2, $badgeY, $badgeSize, $badgeSize, $highlightColor);

      // Add PDF text to badge
      $pdfText = 'PDF';
      $pdfFont = 5;
      $pdfTextWidth = imagefontwidth($pdfFont) * strlen($pdfText);
      $pdfX = ($width - $pdfTextWidth) / 2;
      $pdfY = $badgeY - 10;
      imagestring($img, $pdfFont, $pdfX, $pdfY, $pdfText, $textColor);

      // Add book title
      $titleFont = 4;
      $bookTitle = $title ?? 'PDF Document';

      // Shorten title if needed
      if (strlen($bookTitle) > 25) {
        $bookTitle = substr($bookTitle, 0, 22) . '...';
      }

      $titleWidth = imagefontwidth($titleFont) * strlen($bookTitle);
      $titleX = ($width - $titleWidth) / 2;
      $titleY = $height / 2 - 10;

      // Title with shadow
      imagestring($img, $titleFont, $titleX + 1, $titleY + 1, $bookTitle, imagecolorallocate($img, 20, 20, 20));
      imagestring($img, $titleFont, $titleX, $titleY, $bookTitle, $highlightColor);

      // Add author if available
      if ($author) {
        $authorFont = 2;

        // Shorten author if needed
        if (strlen($author) > 30) {
          $author = substr($author, 0, 27) . '...';
        }

        $authorWidth = imagefontwidth($authorFont) * strlen($author);
        $authorX = ($width - $authorWidth) / 2;
        $authorY = $titleY + 30;

        imagestring($img, $authorFont, $authorX, $authorY, $author, $textColor);
      }

      // Save the image
      imagejpeg($img, $thumbnailPath, 90);
      imagedestroy($img);

      return true;
    } catch (\Exception $e) {
      \Log::error('Failed to generate enhanced thumbnail: ' . $e->getMessage());
      return false;
    }
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

    try {
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
    } catch (\Exception $e) {
      \Log::error('Failed to resize thumbnail: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Generate a placeholder thumbnail using GD library
   * 
   * @param string $thumbnailPath
   * @param string $bookTitle
   * @param string $bookAuthor
   * @return bool
   */
  private function generatePlaceholderThumbnail($thumbnailPath, $bookTitle = null, $bookAuthor = null)
  {
    try {
      // Create a more appealing placeholder image
      $width = 400;
      $height = 566; // Standard book aspect ratio

      // Create the base image
      $img = imagecreatetruecolor($width, $height);

      // Define colors
      $bgColor = imagecolorallocate($img, 50, 60, 70); // Dark blue-gray
      $accentColor = imagecolorallocate($img, 100, 120, 140); // Lighter blue-gray
      $textColor = imagecolorallocate($img, 230, 230, 230); // Light gray
      $overlayColor = imagecolorallocate($img, 30, 40, 50); // Darker blue for overlay

      // Fill the background
      imagefill($img, 0, 0, $bgColor);

      // Draw a nice pattern
      for ($i = 0; $i < $height; $i += 20) {
        // Draw horizontal lines with a pattern
        imageline($img, 0, $i, $width, $i, $accentColor);
      }

      // Create a central area for the text
      imagefilledrectangle(
        $img,
        $width / 4,
        $height / 3,
        $width * 3 / 4,
        $height * 2 / 3,
        $overlayColor
      );

      // Add border around the central area
      imagerectangle(
        $img,
        $width / 4,
        $height / 3,
        $width * 3 / 4,
        $height * 2 / 3,
        $accentColor
      );

      // Use provided title and author or placeholders
      $title = $bookTitle ?? 'PDF Document';
      $author = $bookAuthor ?? '';

      // Shorten title if too long
      $displayTitle = (strlen($title) > 20) ? substr($title, 0, 17) . '...' : $title;

      // Add PDF icon or text
      $pdfText = 'PDF DOCUMENT';
      $titleFont = 4; // Larger built-in font
      $authorFont = 2; // Smaller built-in font
      $pdfFont = 5; // Largest built-in font

      // Calculate text dimensions
      $pdfTextWidth = imagefontwidth($pdfFont) * strlen($pdfText);
      $titleWidth = imagefontwidth($titleFont) * strlen($displayTitle);
      $authorWidth = $author ? imagefontwidth($authorFont) * strlen($author) : 0;

      // Center the text
      $pdfX = ($width - $pdfTextWidth) / 2;
      $pdfY = $height / 3 + 15;

      $titleX = ($width - $titleWidth) / 2;
      $titleY = $pdfY + 30;

      $authorX = ($width - $authorWidth) / 2;
      $authorY = $titleY + 25;

      // Draw the text
      imagestring($img, $pdfFont, $pdfX, $pdfY, $pdfText, $textColor);
      imagestring($img, $titleFont, $titleX, $titleY, $displayTitle, $textColor);

      if ($author) {
        imagestring($img, $authorFont, $authorX, $authorY, $author, $textColor);
      }

      // Add a subtle pattern to the bottom
      for ($i = 0; $i < $width; $i += 10) {
        imageline($img, $i, $height - 20, $i + 5, $height, $accentColor);
      }

      // Add a small border
      imagerectangle($img, 0, 0, $width - 1, $height - 1, $accentColor);

      // Save the placeholder with good quality
      imagejpeg($img, $thumbnailPath, 90);
      imagedestroy($img);

      return true;
    } catch (\Exception $e) {
      // Log error but don't fail the upload
      \Log::error('Failed to generate placeholder thumbnail: ' . $e->getMessage());
      return false;
    }
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
   * Add a new endpoint to serve thumbnail images
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
