<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'type',
        'nickname',
        'blocked',
        'photo_filename',
        'brain_coins_balance',
        'custom',
        'deleted_at',
    ];

    protected $casts = [
        'blocked' => 'boolean',
        'custom' => 'json',
        'email_verified_at' => 'datetime',
    ];

    public function gamesCreated()
    {
        return $this->hasMany(Game::class, 'created_user_id');
    }

    public function gamesWon()
    {
        return $this->hasMany(Game::class, 'winner_user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function multiplayerGamesPlayed()
    {
        return $this->hasMany(MultiplayerGamePlayed::class);
    }
}
