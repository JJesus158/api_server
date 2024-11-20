<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;
use GuzzleHttp\Psr7\Request;
use Illuminate\Log;

class BoardPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->type == 'A') {
            return true;
        }
        return null;
    }


    public function view(User $user, Board $board): bool
    {

        return $user!=null || $board->id == 1;
    }


}
