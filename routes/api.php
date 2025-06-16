<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Rutas para GameController
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/games', [GameController::class, 'createGame']);
    Route::post('/games/{gameId}/join', [GameController::class, 'joinGame']);
    Route::get('/games', [GameController::class, 'listGames']);
    Route::get('/games/{gameId}', [GameController::class, 'showGame']);
    Route::post('/games/{gameId}/move', [GameController::class, 'makeMove']);
    Route::get('/games/{gameId}/opponent-board', [GameController::class, 'getOpponentBoard']);
});
