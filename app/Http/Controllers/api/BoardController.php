<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BoardResource;
use App\Models\Board;
use Illuminate\Http\Request;
use function Laravel\Prompts\error;

class BoardController extends Controller
{
    public function index()
    {
        return BoardResource::collection(Board::get());
    }

    public function show(Board $board)
    {

        return new BoardResource($board);
    }

    public function showGuest()
    {
        $board = Board::findOrFail(1);
        return new BoardResource($board);
    }



}
