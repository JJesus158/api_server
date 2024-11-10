<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_cols',
        'board_rows',
        'custom',
    ];

    protected $casts = [
        'custom' => 'json',
    ];

    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
