<?php

use App\Http\Controllers\api\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('login', function () {
    return view('welcome');
});

