<?php

use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [WebController::class, 'index'])->name('dashboard');
    Route::get('counter', [WebController::class, 'counter'])->name('counter');

    Route::prefix('admin')->group(function () {
        Route::get('/', [WebController::class, 'hospitalSettings'])->name('admin.hospital-settings');
        Route::get('/receptions', [WebController::class, 'hospitalSettings'])->name('admin.receptions');
        Route::get('/services', [WebController::class, 'hospitalSettings'])->name('admin.services');
        Route::get('/panels', [WebController::class, 'hospitalSettings'])->name('admin.panels');
    });
});

require __DIR__.'/settings.php';
