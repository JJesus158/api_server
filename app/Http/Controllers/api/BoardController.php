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

    public function show($id)
    {
        $board = Board::find($id);

        return $board? new BoardResource($board): response()->json(['error' => 'Board not Found'], 404);
    }

}
