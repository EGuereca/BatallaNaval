<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    protected $fillable = [
        'player1_id',
        'player2_id',
        'status',
        'winner_id',
        'current_turn_id',
    ];
}
