<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;
    protected $fillable = ['game_id', 'grid', 'player_id'];
    protected $casts = [
        'grid' => 'array', // Assuming grid is stored as a JSON array
    ];
}
