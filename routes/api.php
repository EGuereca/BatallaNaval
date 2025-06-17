<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rutas públicas de autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Ruta para obtener información del usuario
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    // Rutas del juego
    Route::prefix('games')->group(function () {
        Route::post('/', [GameController::class, 'createGame']);
        Route::post('/{gameId}/join', [GameController::class, 'joinGame']);
        Route::get('/', [GameController::class, 'listGames']);
        Route::get('/{gameId}', [GameController::class, 'showGame']);
        Route::post('/{gameId}/move', [GameController::class, 'makeMove']);
        Route::get('/{gameId}/opponent-board', [GameController::class, 'getOpponentBoard']);
        Route::get('/{gameId}/board/{playerNumber}', [GameController::class, 'getPlayerBoard']);
    });
});
