<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\BoardController;
use App\Http\Controllers\api\GameController;
use App\Http\Controllers\api\TransactionController;
use App\Http\Controllers\api\UserController;
use App\Models\Game;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;





Route::get('/boards', [BoardController::class, 'index']);
//Route::get('/boards/{board}', [BoardController::class, 'show']);

Route::get('/boards/1', [BoardController::class, 'show']);


Route::middleware(['auth:sanctum','verified'])->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);

    Route::get('/users/me', [UserController::class , 'showMe']);

    Route::patch('/users/me', [UserController::class , 'updateMe']);
    Route::delete('/users/{user}', [UserController::class , 'deleteMe']);

    Route::get('/boards/{board}', [BoardController::class, 'show'])->can('view', 'board');
    Route::get('/games', [GameController::class, 'index']);
    Route::post('/games', [GameController::class, 'store']);


    Route::get('/games', [GameController::class, 'index']);
    Route::post('/games', [GameController::class, 'store']);
    Route::get('/games/{game}', [GameController::class, 'view'])->can('view', 'game');
    Route::put('/games/{game}', [GameController::class, 'update'])->can('update', 'game');

    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
});

Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
Route::post('/auth/register', [UserController::class, 'storeMe']);

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
})->middleware(['auth', 'signed'])->name('verification.verify');
