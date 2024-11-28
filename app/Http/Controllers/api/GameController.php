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

    public function view(Game $game)
    {
        return new GameResource($game);

    }

    public function update(StoreUpdateGameRequest $request, Game $game)
    {
        $game->fill($request->validated());
        $game->save();

        return new GameResource($game);
    }


    public function store(StoreUpdateGameRequest $request){
        $game = new Game();
        $game->fill($request->all());
        $game->created_user_id = $request->user() ? $request->user()->id : null;
        $game->save();
        return new GameResource($game);
    }
}
