<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\NotificationController;


Route::get('/', function () {
    return view('welcome');
});

// stats
// Redirect to the 'dashboard' method instead of 'index'
Route::get('/home', [HomeController::class, 'dashboard'])->middleware('auth')->name('home');




Route::get('/', [HomeController::class, 'carousel']);


Route::get('/csstest', function () {
    return view('csstest');
});


//nez vai vajag
Route::get('/testings', [HomeController::class, 'uploadpage'])->middleware(['auth', 'admin'])->name('uploadpage');


//See Books
Route::get('/bookpage', [HomeController::class, 'bookpage'])
    ->name('bookpage')
    ->middleware(['auth']);

Route::get('/get-genres', function () {
    $genres = DB::table('products')->distinct()->pluck('category');
    return response()->json($genres);
});

Route::get('/assets/{filename}', function ($filename) {
    $path = public_path('assets/' . $filename);
    if (!File::exists($path)) {
        abort(404);
    }
    return response()->file($path);
});

//Book Manage Routes
Route::post('/uploadbook', [HomeController::class, 'store'])->middleware(['auth', 'admin']);
Route::get('/uploadpage', [HomeController::class, 'show'])->middleware(['auth', 'admin'])->name('uploadpage');
// Show edit form
Route::get('/edit/{id}', [HomeController::class, 'edit'])->middleware(['auth', 'admin'])->name('edit');
// Handle edit request
Route::post('/update/{id}', [HomeController::class, 'update'])->middleware(['auth', 'admin'])->name('update');


//User Manage Routes
Route::get('/managepage', [UserController::class, 'index'])->name('user.manage')->middleware(['auth', 'admin']);
Route::put('/users/{user}', [UserController::class, 'updateUserType'])->name('users.updateUserType');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');



// File Routes
Route::get('/view/{id}', [HomeController::class, 'view'])->name('view')->middleware(['auth']);

Route::get('/download/{file}', [HomeController::class, 'download'])->name('download');
Route::delete('/delete/{id}', [HomeController::class, 'destroy'])->name('delete');
Route::get('/redirect-back', [HomeController::class, 'redirectAfterBack'])->name('redirect.back');



//Favorites Routes
Route::get('/favorites', [FavoritesController::class, 'favorites'])->name('favorites');
Route::post('/favorites/{id}', [FavoritesController::class, 'add'])->name('favorites.add');
Route::delete('/favorites/{id}', [FavoritesController::class, 'delete'])->name('favorites.delete');



//Notification Routes
Route::post('/managepage', [HomeController::class, 'sendNotification'])->name('admin.send.notification');
Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');



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



require __DIR__ . '/auth.php';
