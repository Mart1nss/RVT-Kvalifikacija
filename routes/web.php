<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ReadLaterController;

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

// Redirect to the 'dashboard' method instead of 'index'
Route::get('/home', [HomeController::class, 'dashboard'])->middleware('auth')->name('home');

Route::get('/testpage', function () {
    return view('testpage');
});

Route::get('/myprogress', function () {
    return view('myprogress');
});

Route::get('/', [HomeController::class, 'carousel']);

//nez vai vajag
Route::get('/testings', [HomeController::class, 'uploadpage'])->middleware(['auth', 'admin'])->name('uploadpage');

//See Books
Route::get('/library', [BookController::class, 'library'])->name('library');

Route::get('/get-genres', function () {
    $genres = App\Models\Category::pluck('name');
    return response()->json($genres);
});

//Book Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/view/{id}', [BookController::class, 'view'])->name('view');
    Route::get('/download/{file}', [BookController::class, 'download'])->name('download');
    Route::get('/book-thumbnail/{file}', [BookController::class, 'servePdf'])->name('book.thumbnail');
    Route::get('/ajax/books', [BookController::class, 'ajaxBooks'])->name('ajax.books');
});

//Admin Book Management Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/book-manage', [BookController::class, 'show'])->name('book-manage');
    Route::get('/uploadpage', [BookController::class, 'uploadpage'])->name('uploadpage');
    Route::post('/uploadbook', [BookController::class, 'store']);
    Route::get('/edit/{id}', [BookController::class, 'edit'])->name('edit');
    Route::match(['post', 'put'], '/update/{id}', [BookController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [BookController::class, 'destroy'])->name('delete');
    Route::post('/toggle-visibility/{id}', [BookController::class, 'toggleVisibility'])->name('toggle.visibility');
});

//User Manage Routes
Route::get('/managepage', [UserController::class, 'index'])->name('user.manage')->middleware(['auth', 'admin']);
Route::put('/users/{user}', [UserController::class, 'updateUserType'])->name('users.updateUserType');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// File Routes
Route::get('/redirect-back', [HomeController::class, 'redirectAfterBack'])->name('redirect.back');

//My Collection Routes
Route::get('/my-collection', [FavoritesController::class, 'favorites'])->name('my-collection');
Route::post('/my-collection/{id}', [FavoritesController::class, 'add'])->name('my-collection.add');
Route::delete('/my-collection/{id}', [FavoritesController::class, 'delete'])->name('my-collection.delete');

//Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index')->middleware(['auth', 'admin']);

    Route::post('/notifications/send', [NotificationController::class, 'sendNotification'])
        ->name('admin.send.notification');

    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markRead');
    Route::post('/notifications/{id}/delete', [NotificationController::class, 'deleteNotification'])
        ->name('notifications.delete');
    Route::post('/notifications/delete-all', [NotificationController::class, 'deleteAllNotifications'])
        ->name('notifications.deleteAll');
    Route::get('/notifications/count', [NotificationController::class, 'getCount'])
        ->name('notifications.count');
    Route::post('/notifications/sent/{id}/delete', [NotificationController::class, 'deleteSentNotification'])
        ->name('notifications.sent.delete')->middleware('admin');
});

// Notes Routes
Route::post('/notes', [NoteController::class, 'store']);
Route::get('/notes/{productId}', [NoteController::class, 'show']);
Route::put('/notes/{productId}', [NoteController::class, 'store']);
Route::get('/viewnotes', [NoteController::class, 'index'])->name('viewnotes')->middleware('auth');

// Reviews Routes
Route::resource('products.reviews', ReviewController::class)->only(['store', 'destroy']);

// Peec Logina
Route::get('/home', [HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('post', [HomeController::class, 'post'])->middleware(['auth', 'admin']);

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
    Route::get('/categories', function() {
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

Route::middleware(['auth'])->group(function () {
    Route::get('/preferences', [PreferenceController::class, 'show'])->name('preferences.show');
    Route::post('/preferences', [PreferenceController::class, 'store'])->name('preferences.store');
    Route::post('/preferences/skip', [PreferenceController::class, 'skip'])->name('skip.preferences');
    Route::get('/preferences/edit', [PreferenceController::class, 'edit'])->name('preferences.edit');
});

Route::get('/audit-logs', [AuditLogController::class, 'index'])->middleware(['auth', 'admin']);

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

require __DIR__ . '/auth.php';
