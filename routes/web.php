<?php

use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPortfolioController;
use Illuminate\Support\Facades\Route;

// Home page
Route::get('/', [PortfolioController::class, 'home'])
    ->name('home');

// Public portfolio page
Route::get('/p/{slug}', [PublicPortfolioController::class, 'show'])
    ->name('portfolios.public');

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard
    Route::get('/dashboard', [PortfolioController::class, 'index'])
        ->name('dashboard');

    // Portfolio management
    Route::get('/portfolios/manage', [PortfolioController::class, 'manage'])
        ->name('portfolios.manage');

    // Create and save portfolio
    Route::get('/portfolios/create', [PortfolioController::class, 'create'])
        ->name('portfolios.create');

    Route::post('/portfolios', [PortfolioController::class, 'store'])
        ->name('portfolios.store');

    // Edit portfolio
    Route::get('/portfolios/{portfolio}/edit', [PortfolioController::class, 'edit'])
        ->name('portfolios.edit');

    Route::put('/portfolios/{portfolio}', [PortfolioController::class, 'update'])
        ->name('portfolios.update');

    // Delete portfolio
    Route::delete('/portfolios/{portfolio}', [PortfolioController::class, 'destroy'])
        ->name('portfolios.destroy');

    // Template selection
    Route::get('/portfolios/{portfolio}/templates', [PortfolioController::class, 'templates'])
        ->name('portfolios.templates.select');

    Route::patch('/portfolios/{portfolio}/template', [PortfolioController::class, 'chooseTemplate'])
        ->name('portfolios.template.choose');

    // Portfolio preview
    Route::get('/portfolios/{portfolio}/preview', [PortfolioController::class, 'preview'])
        ->name('portfolios.preview');

    // Publish portfolio
    Route::post('/portfolios/{portfolio}/publish', [PortfolioController::class, 'publish'])
        ->name('portfolios.publish');

    // Unpublish portfolio
    Route::post('/portfolios/{portfolio}/unpublish', [PortfolioController::class, 'unpublish'])
        ->name('portfolios.unpublish');
});

require __DIR__.'/auth.php';
