<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use function Symfony\Component\Translation\t;

class GamePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function before(User $user): bool|null
    {
        if ($user->type == 'A') {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Game $game): bool
    {
        return $user->id === $game->created_user_id;
    }

    /**
     * Determine whether the user can create models.
     */


    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Game $game): bool
    {
        return  $game->created_user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Game $game): bool
    {
        //
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Game $game): bool
    {
        //
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Game $game): bool
    {
        //
        return true;
    }


}
