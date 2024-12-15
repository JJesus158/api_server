<?php

namespace App\Providers;

use App\Models\Board;
use App\Policies\BoardPolicy;
use App\Policies\GamePolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Gate::policy(Board::class, BoardPolicy::class);

    }
}
