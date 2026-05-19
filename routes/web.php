<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LaunchController;
use App\Http\Controllers\RocketController;
use Illuminate\Support\Facades\Route;

/* ---------- Públicas ---------- */

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Lanzamientos
Route::get('/launches', [LaunchController::class, 'index'])->name('launches.index');
Route::get('/launches/{id}', [LaunchController::class, 'show'])->name('launches.show');

// Cohetes
Route::get('/rockets', [RocketController::class, 'index'])->name('rockets.index');

/* ---------- Autenticación ---------- */

Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/* ---------- Favoritos (solo autenticados) ---------- */

Route::middleware('auth')->group(function () {
    Route::get('/favorites',             [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites',            [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{id}',     [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::post('/favorites/{id}/notes', [FavoriteController::class, 'updateNotes'])->name('favorites.notes');
});
