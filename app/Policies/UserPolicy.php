<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use function Symfony\Component\Translation\t;

class UserPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function viewMe(User $user, Game $game): bool
    {
        return ($user->id === $game->created_user_id);
    }

    /**
     * Determine whether the user can create models.
     */


    public function createMe(User $user): bool
    {
        return $user->type !== 'A';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateMe(User $user, Game $game): bool
    {
        return  $game->created_user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteMe(User $user): bool
    {

        return  $user->type === 'P';
    }

    public function viewAny(User $user): bool
    {
        return $user->type === 'A';
    }

    public function updateStatus(User $user): bool
    {
        return $user->type === 'A';
    }

    public function delete(User $user)
    {
        return $user->type === 'A';
    }

    public function store(User $user)
    {
        return $user->type === 'A';
    }


}
