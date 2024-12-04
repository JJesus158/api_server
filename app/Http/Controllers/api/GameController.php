<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateGameRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\Transaction;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $itensPerPage = $request->input('itensPerPage', 10);

        if ($user->type == 'A') {
            return GameResource::collection(Game::get()::orderBy('created_at', 'desc')->paginate($itensPerPage));
        }

       return GameResource::collection($user->gamesCreated()->orderBy('created_at', 'desc')->paginate($itensPerPage));
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
        $user = $request->user();
        $game = new Game();

        $game->fill($request->all());
        $game->created_user_id = $request->user() ? $request->user()->id : null;
        if($game->board->id > 1) {
            $user->brain_coins_balance -=1;
            $user->transactions()->create([
                'type' => 'I',
                'transaction_datetime' => now(),
                'game_id' => $game->id,
                'brain_coins' => -1
            ]);

            $user->save();
        }
        $game->save();
        return new GameResource($game);
    }
}
