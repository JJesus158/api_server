<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultiplayerGamePlayed extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'player_won',
        'pairs_discovered',
        'custom',
    ];

    protected $casts = [
        'player_won' => 'boolean',
        'custom' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
