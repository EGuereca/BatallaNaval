<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GameController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de juegos (migradas desde api.php)
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');
    Route::post('/games', [GameController::class, 'createGame'])->name('games.create');
    Route::post('/games/{game}/join', [GameController::class, 'joinGame'])->name('games.join');
    Route::post('/games/{game}/move', [GameController::class, 'makeMove'])->name('games.move');
    Route::get('/games/{game}/opponent-board', [GameController::class, 'getOpponentBoard'])->name('games.opponent-board');
    Route::get('/games/{game}/board/{playerNumber}', [GameController::class, 'getPlayerBoard'])->name('games.player-board');
});

require __DIR__.'/auth.php';
