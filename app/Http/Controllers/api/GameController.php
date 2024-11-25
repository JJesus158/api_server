<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateGameRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->type == 'A') {
            return GameResource::collection(Game::all());
        }

       return GameResource::collection($user->gamesCreated);
    }

    public function store(StoreUpdateGameRequest $request){
        $game = Game::create($request->validated());
        return new GameResource($game);
    }
}
