<?php

namespace App\Livewire\Books;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class BookUploadForm extends Component
{
  use WithFileUploads;

  public $title = '';
  public $author = '';
  public $category_id = '';
  public $is_public = false;
  public $file;

  public $categories = [];

  protected $rules = [
    'title' => 'required|string|max:255',
    'author' => 'required|string|max:255',
    'category_id' => 'required|exists:categories,id',
    'file' => 'required|mimes:pdf|max:10240'
  ];

  protected $messages = [
    'file.max' => 'The file size must not exceed 10MB.',
    'file.mimes' => 'The file must be a PDF document.',
    'file.required' => 'Please select a PDF file.'
  ];

  public function mount()
  {
    $this->categories = Category::all();
  }

  public function uploadBook()
  {
    $this->validate();

    $product = new Product();
    $product->title = $this->title;
    $product->author = $this->author;
    $product->category_id = $this->category_id;
    $product->is_public = $this->is_public;

    if ($this->file) {
      $filename = Str::slug($this->title) . '_' . time() . '.' . $this->file->getClientOriginalExtension();
      $this->file->storeAs('books', $filename);
      $product->file = $filename;
    }

    $product->save();

    // Log the upload
    app(\App\Services\AuditLogService::class)->log(
      "Uploaded book",
      "book",
      "Uploaded new book",
      $product->id,
      $product->title
    );

    // Reset form
    $this->reset(['title', 'author', 'category_id', 'is_public', 'file']);

    // Show success message
    $this->dispatch('alert', [
      'type' => 'success',
      'message' => 'Book uploaded successfully!'
    ]);

    // Refresh book list
    $this->dispatch('refreshBooks');
  }

  public function render()
  {
    return view('livewire.books.book-upload-form');
  }
}