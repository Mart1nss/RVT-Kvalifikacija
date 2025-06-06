<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\ReadLaterController;
use App\Http\Controllers\ReadBookController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class, 'dashboard'])->middleware('auth')->name('home');


Route::get('/myprogress', [App\Http\Controllers\ProgressController::class, 'index'])->middleware('auth')->name('myprogress');

Route::get('/', [HomeController::class, 'carousel']);

Route::get('/get-genres', function () {
    $genres = App\Models\Category::pluck('name');
    return response()->json($genres);
});

Route::get('/library', function () {
    return view('books.library');
})->middleware('auth')->name('library');

Route::middleware(['auth'])->group(function () {
    Route::get('/view/{id}', [BookController::class, 'view'])->name('view');
    Route::get('/download/{file}', [BookController::class, 'download'])->name('download');
    Route::get('/book-thumbnail/{file}', [BookController::class, 'servePdf'])->name('book.thumbnail');
    Route::get('/book-thumbnails/{filename}', [BookController::class, 'serveThumbnail'])->name('book.thumbnail.image');
});

// Book Management Routes 
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/book-manage', function () {
        return view('books.book-manage');
    })->name('book-manage');
    Route::get('/admin/books/create', [BookController::class, 'create'])->name('books.create'); // New route for upload form
    Route::post('/uploadbook', [BookController::class, 'store'])->name('uploadbook');
});

// User Management Routes
Route::get('/user-management', function () {
    return view('admin.users.user-management');
})->name('user.management.livewire')->middleware(['auth', 'admin']);

// User Details/Edit Route
Route::get('/user/{userId}', function ($userId) {
    return view('admin.users.user-show', ['userId' => $userId]);
})->name('user.show')->middleware(['auth', 'admin']);

//My Collection Routes
Route::get('/my-collection', [FavoritesController::class, 'favorites'])->name('my-collection');
Route::post('/my-collection/{id}', [FavoritesController::class, 'add'])->name('my-collection.add');
Route::delete('/my-collection/{id}', [FavoritesController::class, 'delete'])->name('my-collection.delete');

//Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', function () {
        return view('notifications');
    })->name('notifications.index')->middleware(['auth', 'admin']);
});

// Notes Routes
Route::post('/notes', [NoteController::class, 'store']);
Route::get('/notes/{productId}', [NoteController::class, 'show']);
Route::put('/notes/{productId}', [NoteController::class, 'store']);
Route::get('/viewnotes', [NoteController::class, 'index'])->name('viewnotes')->middleware('auth');
Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy')->middleware('auth');

//Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::view('notes', 'notes')->name('notes');
});

// Bookmark Routes
Route::middleware('auth')->group(function () {
    Route::post('/bookmarks', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::get('/bookmarks/{productId}', [BookmarkController::class, 'show'])->name('bookmarks.show');
    Route::delete('/bookmarks/{productId}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
});

// Category Management Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/categories', function () {
        return view('admin.categories.categories');
    })->name('categories.index');
});

// Support Tickets Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.update-status');
    Route::post('/tickets/{ticket}/respond', [TicketController::class, 'addResponse'])->name('tickets.respond');
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assignTicket'])->middleware('admin')->name('tickets.assign');
});

// Preferences Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/preferences', [PreferenceController::class, 'show'])->name('preferences.show');
    Route::post('/preferences', [PreferenceController::class, 'store'])->name('preferences.store');
    Route::post('/preferences/skip', [PreferenceController::class, 'skip'])->name('skip.preferences');
    Route::get('/preferences/edit', [PreferenceController::class, 'edit'])->name('preferences.edit');
});

// Audit Logs Routes
Route::get('/audit-logs', function () {
    return view('audit-logs-page');
})->name('audit.logs')->middleware(['auth', 'admin']);

// Read Later Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/readlater/{id}', [ReadLaterController::class, 'add'])->name('readlater.add');
    Route::delete('/readlater/{id}', [ReadLaterController::class, 'delete'])->name('readlater.delete');
});

// Forum Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/forums', function () {
        return view('forums.index');
    })->name('forums.index');

    Route::get('/forums/create', function () {
        return view('forums.create');
    })->name('forums.create');

    Route::get('/forums/{forum}', function (App\Models\Forum $forum) {
        return view('forums.view', ['forum' => $forum]);
    })->name('forums.view');
});

// Read Book Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/read-books/{productId}', [ReadBookController::class, 'status'])->name('read-books.status');
    Route::post('/read-books/{productId}', [ReadBookController::class, 'toggle'])->name('read-books.toggle');
});

require __DIR__ . '/auth.php';
