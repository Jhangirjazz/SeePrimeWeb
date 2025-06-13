<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;   // or WatchController later
use App\Http\Controllers\WatchHistoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are automatically prefixed with /api and have the "api"
| middleware group (no sessions, no CSRF). Perfect for AJAX / Beacon calls.
// */
// Route::middleware(['web','oracle_user','check_user'])
//      ->post('/save-progress', [AuthController::class, 'save']);

// Route::middleware(['web','oracle_user','check_user'])
//      ->get('/continue-watching', [PageController::class, 'continueJson']);

Route::post('/save-progress', [WatchHistoryController::class, 'save'])
    ->middleware(['web']);