<?php

use App\Http\Controllers\GamesController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/games-active', [GamesController::class, 'index'])->name('dashboard');
    Route::get('/game-finished', [GamesController::class, 'finished'])->name('games.finished');
    Route::post('/dashboard', [GamesController::class, 'store'])->name('games.store');

    Route::get('/create-games', [GamesController::class, 'create'])->name('games.create');
    Route::get('/game/{gameId}', [GamesController::class, 'show'])->name('games.show');
    Route::post('/game', [GamesController::class, 'play'])->name('games.play');
});

require __DIR__ . '/auth.php';
