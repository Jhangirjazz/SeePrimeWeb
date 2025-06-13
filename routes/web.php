<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WatchHistoryController;
use App\Http\Middleware\VerifyCsrfToken;

// Auth Routes
Route::get('/', [AuthController::class, 'login']); // Login form
Route::post('/', [AuthController::class, 'handleLogin']); // Form submit
Route::get('/register', [AuthController::class, 'register']); // Register form
Route::get('/logout', [AuthController::class, 'logout'])->name('logout'); // Logout

Route::get('/welcome', [PageController::class, 'welcome']);
Route::get('/filter', [PageController::class, 'multiFilter'])->name('filter.multi');
Route::get('/category/{id}', [PageController::class, 'filterByCategory'])->name('filter.by.category');
Route::get('/genre/{id}', [PageController::class, 'filterByGenre'])->name('filter.by.genre');
Route::get('/play/{id}/{partId?}', [PageController::class, 'playVideo'])->name('play.video');

Route::post('/save-progress', [WatchHistoryController::class, 'save']);

// Protected Routes (need login)
Route::middleware(['check_user'])->group(function () {
    

    // routes/api.php
    // Route::post('/save-progress', [AuthController::class, 'save']);
    Route::get('/documentaries', [PageController::class, 'documentaries'])->name('documentaries');
    Route::get('/shows', [PageController::class, 'shows']);
    Route::get('/movies', [PageController::class, 'movies']);
    Route::get('/webseries', [PageController::class, 'webseries']);
    Route::get('/new', [PageController::class, 'new']);
    Route::get('/mylist', [PageController::class, 'mylist']);
    Route::post('/mylist/add', [AuthController::class, 'addToMyList'])->name('mylist.add');
    Route::post('/mylist/remove', [AuthController::class, 'removeFromMyList'])->name('mylist.remove');
    Route::get('/search', [App\Http\Controllers\PageController::class, 'search'])->name('search');
 
    // ✅ Profile screen for Prime Members
 Route::get('/profiles', [PageController::class, 'showProfiles'])->name('profiles');
});

?>