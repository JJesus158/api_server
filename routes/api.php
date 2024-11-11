<?php

use App\Http\Controllers\api\BoardController;
use App\Http\Controllers\api\GameController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/users', [UserController::class, 'index']);

Route::get('/boards', [BoardController::class, 'index']);

Route::get('/games', [GameController::class, 'index']);
