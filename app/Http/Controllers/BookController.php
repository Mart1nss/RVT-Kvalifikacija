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
   * Parāda jaunas grāmatas augšupielādes formu.
   *
   * @return \Illuminate\View\View
   */
  public function create()
  {
    $categories = Category::orderBy('name')->get();
    return view('books.create', compact('categories'));
  }

  /**
   * Parāda grāmatu pārvaldības lapu.
   * Šī lapa ļauj administratoriem pārvaldīt un augšupielādēt grāmatas.
   *
   * @return \Illuminate\View\View
   */
  public function uploadpage()
  {
    return view('book-manage');
  }

  /**
   * Saglabā jaunu augšupielādētu grāmatu datubāzē.
   * Apstrādā faila augšupielādi, validāciju un izveido jaunu grāmatas ierakstu.
   *
   * @param Request $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(Request $request)
  {
    // Pārbauda, vai grāmata jau pastāv
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

      // Vispirms saglabā grāmatu, lai iegūtu ID
      $product->save();

      // Ģenerē sīktēlu
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

    return redirect()->route('book-manage')->with('success', 'Book uploaded successfully!');
  }

  /**
   * Ģenerē PDF faila sīktēlu, izmantojot Ghostscript
   *
   * @param string $filename
   * @return bool
   */
  private function generateThumbnail($filename)
  {
    // Izveido sīktēlu direktoriju, ja tā nepastāv
    $thumbnailDir = public_path('book-thumbnails');
    if (!file_exists($thumbnailDir)) {
      mkdir($thumbnailDir, 0755, true);
    }

    // Ģenerē sīktēla faila nosaukumu (nomaina .pdf uz .jpg)
    $thumbnailFilename = str_replace('.pdf', '.jpg', $filename);
    $thumbnailPath = $thumbnailDir . '/' . $thumbnailFilename;

    // Pārbauda, vai sīktēls jau pastāv
    if (file_exists($thumbnailPath)) {
      return true;
    }

    // Iegūst PDF faila ceļu
    $pdfPath = storage_path('app/books/' . $filename);
    if (!file_exists($pdfPath)) {
      return false;
    }

    // iegūst ghostscript 
    $gsExecutable = 'C:\\Program Files\\gs\\gs10.05.1\\bin\\gswin64c.exe';

    if (file_exists($gsExecutable)) {
      $command = '"' . $gsExecutable . '" -sDEVICE=jpeg -dNOPAUSE -dBATCH -dSAFER '
        . '-dFirstPage=1 -dLastPage=1 -r150 '
        . '-dTextAlphaBits=4 -dGraphicsAlphaBits=4 '
        . '-o "' . $thumbnailPath . '" '
        . '"' . $pdfPath . '"';

      exec($command, $output, $return_var);

      if ($return_var === 0 && file_exists($thumbnailPath)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Parāda konkrētas grāmatas detalizētu skatu.
   * Rāda grāmatas informāciju un atsauksmes.
   * Apstrādā redzamības atļaujas:
   * - Grāmatas ir redzamas, ja kategorija ir publiska
   * - Grāmatas privātās kategorijās ir redzamas tikai administratoriem
   *
   * @param int $id
   * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
   */
  public function view($id)
  {
    $product = Product::findOrFail($id);
    $category = $product->category;

    // Pārbauda, vai kategorija ir privāta un lietotājs nav administrators
    if (!$category->is_public && (!Auth::check() || Auth::user()->usertype !== 'admin')) {
      return redirect()->route('library')->with('error', 'You do not have permission to view this book.');
    }

    // Seko līdzi pēdējai lasītajai grāmatai autentificētiem lietotājiem
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
   * Lejupielādē grāmatas PDF failu.
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
   * Pasniedz PDF failu sīktēlu ģenerēšanai.
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
   * Pasniedz sīktēlu attēlus
   *
   * @param string $filename
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function serveThumbnail($filename)
  {
    $path = public_path('book-thumbnails/' . $filename);

    // Ja sīktēls nepastāv, mēģina to ģenerēt
    if (!file_exists($path)) {
      $pdfFilename = str_replace('.jpg', '.pdf', $filename);
      $this->generateThumbnail($pdfFilename);
    }

    if (!file_exists($path)) {
      abort(404);
    }

    return response()->file($path, ['Content-Type' => 'image/jpeg']);
  }
}
