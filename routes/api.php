<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\BoardController;
use App\Http\Controllers\api\GameController;
use App\Http\Controllers\api\UserController;
use App\Models\Game;
use Illuminate\Support\Facades\Route;





Route::get('/boards', [BoardController::class, 'index']);
//Route::get('/boards/{board}', [BoardController::class, 'show']);

Route::get('/boards/1', [BoardController::class, 'showGuest']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);
    Route::get('/users/me', [UserController::class , 'showMe']);

    Route::get('/boards/{board}', [BoardController::class, 'show'])->can('view', 'board');


    Route::get('/games', [GameController::class, 'index']);
    Route::post('/games', [GameController::class, 'store']);
});
Route::post('/auth/login', [AuthController::class, 'login']);

