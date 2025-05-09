<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

// Public Routes
Route::get('/', [PageController::class, 'login']);
Route::post('/', [PageController::class, 'handleLogin']);
Route::get('/register', [PageController::class, 'register']);
Route::get('/logout', [PageController::class, 'logout'])->name('logout');
Route::get('/welcome', [PageController::class, 'welcome']);
Route::get('/play/{id}/{partId?}', [PageController::class, 'playVideo'])->name('play.video');

// Protected Routes (need login)
Route::middleware(['check_user'])->group(function () {
    Route::get('/shows', [PageController::class, 'shows']);
    Route::get('/movies', [PageController::class, 'movies']);
    Route::get('/webseries', [PageController::class, 'webseries']);
    Route::get('/new', [PageController::class, 'new']);
    Route::get('/mylist', [PageController::class, 'mylist']);
    Route::get('/search', [App\Http\Controllers\PageController::class, 'search'])->name('search');
});

?>