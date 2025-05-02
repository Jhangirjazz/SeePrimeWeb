<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::middleware(['check_user'])->group(function () {

Route::get('/welcome', [PageController::class, 'welcome']);

Route::get('/shows', [PageController::class, 'shows']);

Route::get('/movies', [PageController::class, 'movies']);

Route::get('/webseries', [PageController::class, 'webseries']);

Route::get('/new', [PageController::class, 'new']);

Route::get('/mylist', [PageController::class, 'mylist']);

// Route::get('/video/{id}', [PageController::class, 'playVideo'])->name('video.play');
// Route::get('/play/{id}', [PageController::class, 'playVideo'])->name('play.video');
    Route::get('/play/{id}', [PageController::class, 'playVideo'])->name('play.video');



});


Route::get('/', [PageController::class, 'login']);
Route::post('/', [PageController::class, 'handleLogin']);
Route::get('/register', [PageController::class, 'register']);
Route::get('/logout', [PageController::class, 'logout'])->name('logout');
