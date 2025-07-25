<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WatchHistoryController;
use App\Http\Middleware\VerifyCsrfToken;

// Auth Routes
Route::get('/', [AuthController::class, 'login'])->name('login'); // Login form
Route::post('/', [AuthController::class, 'handleLogin']); // Form submit
Route::get('/register', [AuthController::class, 'register']);
Route::post('/register', [AuthController::class, 'handleRegister']); // Register form
Route::get('/logout', [AuthController::class, 'logout'])->name('logout'); // Logout

Route::get('/welcome', [PageController::class, 'welcome'])->name('welcome');
Route::get('/filter', [PageController::class, 'multiFilter'])->name('filter.multi');
Route::get('/category/{id}', [PageController::class, 'filterByCategory'])->name('filter.by.category');
Route::get('/genre/{id}', [PageController::class, 'filterByGenre'])->name('filter.by.genre');
Route::get('/play/{id}/{partId?}', [PageController::class, 'playVideo'])->name('play.video');
Route::post('/save-progress', [WatchHistoryController::class, 'save']);
Route::get('api/account-types',[AuthController::class,'getAccountType']);

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

    // ✅ Profile Management (for PRIME users)
    Route::get('/profiles',[AuthController::class,'manageProfiles'])->name('profiles.index');
    Route::post('/profiles',[AuthController::class,'storeProfile'])->name('profile.store');
    // Route::post('/profiles/delete/{id}',[AuthController::class,'deleteProfile'])->name('profiles.delete');
    Route::delete('/profiles/delete/{id}', [AuthController::class, 'deleteProfile'])->name('profiles.delete');
    Route::get('/profiles/{id}/edit', [AuthController::class, 'editProfile'])->name('profiles.edit');
    Route::post('/profiles/{id}/update', [AuthController::class, 'updateProfile'])->name('profiles.update');
    Route::get('/profile-photo/{id}', [AuthController::class, 'getProfilePhoto'])->name('profile.photo');
    Route::get('/profile-photo/{id}',[AuthController::class,'getProfiePhoto'])->name('profile.photo');
    Route::post('/profile-login', [AuthController::class, 'handleProfileLogin'])->name('profile.login');
    

    



    Route::get('/about', function(){
        return view('about');
    });
     Route::get('/blog', function(){
        return view('blog');
    });
    Route::get('/pricing', function(){
        return view('pricing');
    });
    Route::get('/faq', function(){
        return view('faq');
    });
    Route::get('/top_trending', function(){
        return view('top_trending');
    });
    Route::get('/recomend', function(){
        return view('recomend');
    });
    Route::get('/popular', function(){
        return view('popular');
    });
    Route::get('/contact', function(){
        return view('contact');
    });
    Route::get('/privacy', function(){
        return view('privacy');
    });
    Route::get('/terms', function(){
        return view('terms');
    });

    // ✅ Profile screen for Prime Members
});

?>