<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\BoardController;
use App\Http\Controllers\api\GameController;
use App\Http\Controllers\api\TransactionController;
use App\Http\Controllers\api\UserController;
use App\Models\Game;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;






Route::get('/boards', [BoardController::class, 'index']);



Route::middleware(['auth:sanctum','verified'])->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);
    //users
    Route::get('/users/me', [UserController::class , 'showMe']);
    Route::patch('/users/me', [UserController::class , 'updateMe']);
    Route::delete('/users/me/{id}', [UserController::class , 'deleteMe'])->can('deleteMe', User::class);

    Route::get('/users', [UserController::class, 'index'])->can('viewAny', User::class);
    Route::patch('users/{id}', [UserController::class, 'updateStatus'])->can('updateStatus', User::class);
    Route::delete('users/{id}', [UserController::class, 'delete'])->can('delete', User::class);
    Route::post('/users', [UserController::class, 'storeAdminUser'])->can('store', User::class);


    Route::get('/boards/{board}', [BoardController::class, 'show'])->can('view', 'board');

    //Games
    Route::get('/games', [GameController::class, 'index']);
    Route::post('/games', [GameController::class, 'store'])->can('create', Game::class);

    Route::get('/games/{game}', [GameController::class, 'view'])->can('view', 'game');
    Route::put('/games/{game}', [GameController::class, 'update'])->can('update', 'game');

    Route::get('/transactions', [TransactionController::class, 'index'])->can('view', Transaction::class);
    Route::post('/transactions', [TransactionController::class, 'store'])->can('create', Transaction::class);

    //personal scoreboard
    Route::get('/scoreboard/personal', [GameController::class, 'personalScoreboard']);

});


Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
Route::post('/auth/register', [UserController::class, 'storeMe']);


//scoreboard multiplayer
Route::get('/scoreboard/global', [GameController::class, 'globalPlayerScoreboard']);



//Email verification
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['error' => 'Invalid verification link'], 403);
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return view('email-verification', [
        'message' => 'Your email has been successfully verified.',
    ]);
})->middleware(['signed'])->name('verification.verify');


