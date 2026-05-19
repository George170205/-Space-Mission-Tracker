<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LaunchController;
use App\Http\Controllers\RocketController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Lanzamientos
Route::get('/launches', [LaunchController::class, 'index'])->name('launches.index');
Route::get('/launches/{id}', [LaunchController::class, 'show'])->name('launches.show');

// Cohetes
Route::get('/rockets', [RocketController::class, 'index'])->name('rockets.index');

// Favoritos
Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
Route::post('/favorites', [FavoriteController::class, 'store'])->name('favorites.store');
Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
Route::post('/favorites/{id}/notes', [FavoriteController::class, 'updateNotes'])->name('favorites.notes');
