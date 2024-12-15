<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function before(User $user): bool
    {
        if($user->type === 'A' || $user->type === 'P')
            return true;


        return false;
    }

    public function view(User $user): bool
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->type === 'P';
    }
}
