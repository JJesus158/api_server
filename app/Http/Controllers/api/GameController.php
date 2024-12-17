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
            return GameResource::collection(Game::orderBy('created_at', 'desc')->paginate($itensPerPage));
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
    public function personalScoreboard(Request $request)
    {
        $user = $request->user();

        // Best times and minimum turns for each board size (Single-Player)
        $singlePlayerStats = $user->gamesCreated()
            ->where('type', 'S')
            ->get()
            ->groupBy(fn($game) => $game->board->board_rows . 'x' . $game->board->board_cols)
            ->map(function ($games, $boardSize) {
                $topBestTimes = $games->sortBy('total_time')->take(10)->map(fn($game) => [
                    'game_id' => $game->id,
                    'time' => $game->total_time,
                ]);
                $topMinTurns = $games->sortBy('total_turns_winner')->take(10)->map(fn($game) => [
                    'game_id' => $game->id,
                    'turns' => $game->total_turns_winner,
                ]);
                return [
                    'board_size' => $boardSize,
                    'best_times' => $topBestTimes->values(),
                    'min_turns' => $topMinTurns->values(),
                ];
            });

        // Victories and losses (Multiplayer)
        $multiplayerStats = [
            'total_victories' => $user->gamesCreated()->where('type', 'M')->where('winner', $user->id)->count(),
            'total_losses' => $user->gamesCreated()->where('type', 'M')->where('winner', '!=', $user->id)->count(),
        ];

        return response()->json([
            'single_player' => $singlePlayerStats->values(),
            'multiplayer' => $multiplayerStats,
        ]);
    }

    public function globalScoreboard()
    {
        // Best times and minimum turns for each board size (Single-Player)
        $singlePlayerStats = Game::where('type', 'S')
            ->get()
            ->groupBy(fn($game) => $game->board->board_rows . 'x' . $game->board->board_cols)
            ->map(function ($games, $boardSize) {
                $topBestTimes = $games->whereNotNull('total_time')->where('status', 'E')->sortBy('total_time')->take(10)->map(fn($game) => [
                    'nickname' => $game->createdUser->nickname ?? "N/A",
                    'time' => $game->total_time,
                ]);
                $topMinTurns = $games
                    ->whereNotNull('total_turns_winner')
                    ->where('status', 'E')
                    ->sortBy('total_turns_winner')
                    ->take(10)->map(fn($game) => [
                    'nickname' => $game->createdUser->nickname ?? "N/A",
                    'turns' => $game->total_turns_winner,
                ]);
                return [
                    'board_size' => $boardSize,
                    'best_times' => $topBestTimes->values(),
                    'min_turns' => $topMinTurns->values(),
                ];
            });

        // Top 5 players with the most victories (Multiplayer)
        $multiplayerStats = Game::where('type', 'M')
            ->whereNotNull('winner_user_id')
            ->get()
            ->groupBy('winner')
            ->map(fn($games, $playerId) => [
                'nickname' => $games->first()->createdUser->nickname?? "N/A",
                'victories' => $games->count(),
                'first_victory_date' => $games->min('created_at'),
            ])
            ->sortByDesc('victories')
            ->sortBy('first_victory_date') // Tie-breaker
            ->take(5)
            ->values();

        return response()->json([
            'single_player' => $singlePlayerStats->values(),
            'multiplayer' => $multiplayerStats,
        ]);
    }



}
