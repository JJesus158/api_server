<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateGameRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\GlobalScoreboardResource;
use App\Http\Resources\PersonalScoreboardResource;
use App\Models\Game;
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

    public function store(StoreUpdateGameRequest $request)
    {
        $user = $request->user();
        $game = new Game();

        $game->fill($request->all());
        $game->created_user_id = $user->id;

        // Subtract brain coins for certain games
        if ($game->board->id > 1) {
            $user->brain_coins_balance -= 1;
            $user->transactions()->create([
                'type' => 'I',
                'transaction_datetime' => now(),
                'game_id' => $game->id,
                'brain_coins' => -1,
            ]);

            $user->save();
        }

        $game->save();
        return new GameResource($game);
    }

    public function personalScoreboard(Request $request)
    {
        $user = $request->user();

        // Prepare single-player scoreboard data
        $singlePlayerStats = $user->gamesCreated()
            ->where('type', 'S')
            ->where('status', 'E')
            ->whereNotNull('total_time')
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

        // Prepare multiplayer scoreboard data
        $totalMultiplayerGames = $user->gamesCreated()->where('type', 'M')->where('status', 'E')->count();
        $totalVictories = $user->gamesCreated()->where('type', 'M')->where('winner_user_id', $user->id)->count();
        $totalLosses = $totalMultiplayerGames - $totalVictories;

        $multiplayerStats = [
            'total_victories' => $totalVictories,
            'total_losses' => $totalLosses,
        ];

        // Return the data via the resource
        return new PersonalScoreboardResource([
            'single_player' => $singlePlayerStats->values(),
            'multiplayer' => $multiplayerStats,
        ]);
    }



    public function globalPlayerScoreboard()
            {
                // Get top 10 best times and top 10 minimum turns for each board size (Single-Player)
                $singlePlayerStats = Game::with(['board', 'createdUser']) // Eager load board and createdUser
                ->where('type', 'S')
                    ->where('status', 'E') // Filter games that are completed
                    ->whereNotNull('total_time') // Filter out games without total_time
                    ->orderBy('total_time') // Sort by best times (ascending)
                    ->get()
                    ->groupBy(fn($game) => $game->board->board_rows . 'x' . $game->board->board_cols)
                    ->map(function ($games, $boardSize) {
                        // Best times (Top 10)
                        $topBestTimes = $games->take(10)->map(fn($game) => [
                            'nickname' => $game->createdUser->nickname ?? "Anonymous",
                            'time' => $game->total_time,
                        ]);


                        $topMinTurns = $games->whereNotNull('total_turns_winner') // Only games with total_turns_winner
                        ->sortBy('total_turns_winner') // Sort by turns (ascending)
                        ->take(10)
                            ->map(fn($game) => [
                                'nickname' => $game->createdUser->nickname ?? "Anonymous",
                                'turns' => $game->total_turns_winner,
                            ]);

                        return [
                            'board_size' => $boardSize,
                            'best_times' => $topBestTimes,
                            'min_turns' => $topMinTurns,
                        ];
                    });

                $multiplayerStats = Game::where('type', 'M')
                    ->whereNotNull('winner_user_id')
                    ->where('status', 'E')
                    ->whereHas('winner', fn($query) => $query->where('blocked', '!=', '1'))
                    ->get()
                    ->groupBy('winner_user_id')
                    ->map(fn($games, $playerId) => [
                        'nickname' => $games->first()->winner->nickname ?? "Anonymous",
                        'total_victories' => $games->count() ??0,
                    ])
                    ->sortByDesc('total_victories')
                    ->take(5)
                    ->values();


                return new GlobalScoreboardResource([
                    'single_player' => $singlePlayerStats->values(),
                    'multiplayer' => $multiplayerStats->values(),
                ]);
    }
}
