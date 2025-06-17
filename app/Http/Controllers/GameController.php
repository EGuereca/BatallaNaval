<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Board;
use App\Models\Move;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\NullableType;

class GameController extends Controller
{
    public function createGame(Request $request){
        $game = Game::create([
            'player1_id' => Auth::id(),
            'player2_id' => Auth::id(), // Initially set to the creator, will be updated when a second player joins
            'status' => 'waiting',
            'current_turn_id' => Auth::id(),
        ]);

        $boats = 15;
        $grid = array_fill(0, 8, array_fill(0, 8, 0)); // Initialize a 8x8 grid with null values

        $filledGrid = $this->fillBoard($grid, $boats); // Fill the board with boats

        // Initialize the board with an empty grid
        Board::create([
            'game_id' => $game->id,
            'player_id' => Auth::id(),
            'grid' => json_encode($filledGrid), // Store the grid as a JSON string
        ]);

        return response()->json(['game_id' => $game->id, 'grid' => $filledGrid], 201);

    }
    private function fillBoard(&$grid, $boats) {
        while ($boats > 0) {
            $x = rand(0, 7);
            $y = rand(0, 7);

            // Check if the cell is empty
            if ($grid[$x][$y] == 0) {
                $grid[$x][$y] = 1; // Place a boat
                $boats--;
            }
        }
        return $grid;
    }
    public function joinGame(Request $request, $gameId){
        $game = Game::findOrFail($gameId);
        if ($game->status !== 'waiting') {
            return response()->json(['error' => 'Game is not available for joining'], 400);
        }

        $game->player2_id = Auth::id();
        $game->status = 'playing';
        $game->save();

        // Create a new board for the second player
        $boats = 15;
        $grid = array_fill(0, 8, array_fill(0, 8, 0)); // Initialize a 8x8 grid with null values
        $filledGrid = $this->fillBoard($grid, $boats); // Fill the board with boats
        Board::create([
            'game_id' => $game->id,
            'player_id' => Auth::id(),
            'grid' => json_encode($filledGrid), // Store the grid as a JSON string
        ]);

        return response()->json(['message' => 'Joined game successfully', 'game_id' => $game->id, 'grid' => $filledGrid], 200);
    }
    public function listGames(Request $request){
        $games = Game::where('status', 'waiting')->get();
        $gamesWithNames = $games->map(function ($game) {
            $player1 = User::findOrFail($game->player1_id);
            $player2 = User::findOrFail($game->player2_id);
            if ($game->player2_id == $game->player1_id) {
                $player2 = null; // Player 2 is not set yet
            }
            return [
                'id' => $game->id,
                'name' => $game->name ?: $this->randomGameName(), // Use a random name if not set
                'player1' => $player1 ? $player1->name : 'Player 1',
                'player2' => $player2 ? $player2->name : null,
                'status' => $game->status,
                'created_at' => $game->created_at,
            ];
        });
        if ($gamesWithNames->isEmpty()) {
            return response()->json(['message' => 'No available games'], 404);
        }
        return response()->json(['games' => $gamesWithNames, 'count' => $gamesWithNames->count(),], 200);
    }
    private function randomGameName() {
        $adjectives = ['Brave', 'Clever', 'Swift', 'Mighty', 'Fierce'];
        $nouns = ['Warrior', 'Hunter', 'Guardian', 'Champion', 'Defender'];
        return $adjectives[array_rand($adjectives)] . ' ' . $nouns[array_rand($nouns)];
    }
    public function showGame(Request $request, $gameId){
        $game = Game::with('board')->findOrFail($gameId);
        return response()->json($game, 200);
    }
    public function makeMove(Request $request, $gameId){
        $game = Game::findOrFail($gameId);
        if ($game->status !== 'playing') {
            return response()->json(['error' => 'Game is not in progress'], 400);
        }
        if ($game->current_turn_id !== Auth::id()) {
            return response()->json(['error' => 'No es tu turno'], 403);
        }
        $this->validate($request, [
            'x' => 'required|integer|min:0|max:7',
            'y' => 'required|integer|min:0|max:7',
        ]);

        // Get the board for the current player
        $board = Board::where('game_id', $gameId)
            ->where('player_id', Auth::id())
            ->firstOrFail();
        $grid = json_decode($board->grid, true);

        if ($grid[$request->x][$request->y] == 1) {
            // Hit
            $grid[$request->x][$request->y] = 2; // Mark as hit
            $status = 'hit';
        } else {
            // Miss
            $grid[$request->x][$request->y] = 3; // Mark as miss
            $status = 'miss';
        }

        // Save the updated board
        $board->grid = json_encode($grid);
        $board->save();

        // Record the move
        Move::create([
            'game_id' => $gameId,
            'player_id' => Auth::id(),
            'x' => $request->x,
            'y' => $request->y,
            'hit' => ($status === 'hit') ? true : false,
        ]);

        // Switch turn
        $game->current_turn_id = ($game->current_turn_id == $game->player1_id) ? $game->player2_id : $game->player1_id;
        $game->save();

        $this->checkStatus($gameId, $game->current_turn_id); // Check if the game is finished
        // Check if the game is finished
        
        return response()->json(['message' => 'Move made successfully', 'status' => $status, 'grid' => $grid], 200);
    }
    private function checkStatus($gameId, $currentTurnId) {
        $game = Game::findOrFail($gameId);
        $board = Board::where('game_id', $gameId)
            ->where('player_id', $currentTurnId)
            ->firstOrFail();
        $grid = json_decode($board->grid, true);

        // Check if all boats are hit
        $allHit = true;
        foreach ($grid as $row) {
            foreach ($row as $cell) {
                if ($cell == 1) { // Still a boat present
                    $allHit = false;
                    break 2; // Break out of both loops
                }
            }
        }

        if ($allHit) {
            $game->status = 'finished';
            $game->winner_id = ($game->current_turn_id == $game->player1_id) ? $game->player2_id : $game->player1_id;
            $game->save();
        }
        
    }
    public function getOpponentBoard($gameId)
    {
        $game = Game::findOrFail($gameId);
        $userId = Auth::id();
        $opponentBoard = $game->boards()->where('player_id', '!=', $userId)->first();
        $grid = json_decode($opponentBoard->grid, true);

        // Solo mostrar celdas disparadas (2=hit, 3=miss), el resto como null
        $visibleGrid = [];
        foreach ($grid as $i => $row) {
            foreach ($row as $j => $cell) {
                $visibleGrid[$i][$j] = ($cell == 2 || $cell == 3) ? $cell : null;
            }
        }

        return response()->json(['grid' => $visibleGrid]);
    }
}
