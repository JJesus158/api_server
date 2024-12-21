<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Game;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;


class StatisticsController extends Controller
{
    public function getGeneralStatistics()
    {
        return response()->json([
            'totalPlayers' => User::count(),
            'totalGames' => Game::count(),
            'gamesInProgress' => Game::where('status', 'PL')->count(),
            'gamesLastWeek' => Game::where('began_at', '>=', now()->subWeek())->count(),
            'gamesLastMonth' => Game::where('began_at', '>=', now()->subMonth())->count(),
            'gamesByBoard' => Game::select('board_id', DB::raw('COUNT(*) as games_count'))
                ->groupBy('board_id')
                ->get(),
            'multiplayerGames' => Game::where('type', 'M')->count(),
            'averageGameDuration' => Game::where('status', 'E')->avg('total_time'),
            'totalBrainCoins' => User::sum('brain_coins_balance'),
            'mostPopularBoard' => Game::select('board_id', DB::raw('COUNT(*) as games_count'))
                ->groupBy('board_id')
                ->orderByDesc('games_count')
                ->first(),
        ]);
    }

    public function getAdminStatistics()
    {
        if (!auth()->user() || auth()->user()->type !== 'A') {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        return response()->json([
            'brainCoinsBought' => Transaction::whereNotNull('payment_type')->sum('brain_coins'),
            'totalEarnings' => Transaction::where('type', 'P')->sum('euros'),
            'userStatus' => User::select('blocked', DB::raw('COUNT(*) as count'))
                ->groupBy('blocked')
                ->get(),
            'revenueByPaymentType' => Transaction::where('type', 'P')
                ->select('payment_type', DB::raw('SUM(euros) as total_revenue'))
                ->groupBy('payment_type')
                ->get()
                ->toArray(),
        ]);
    }

    public function getGamesByTime($timeframe = 'month')
    {
        $timeFormat = match ($timeframe) {
            'month' => '%Y-%m',
            'week' => '%Y-%u',
            'day' => '%Y-%m-%d',
            default => '%Y-%m',
        };

        $gamesByTime = Game::select(
            DB::raw("DATE_FORMAT(began_at, '{$timeFormat}') as period"),
            DB::raw('COUNT(*) as games_count')
        )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        return response()->json($gamesByTime);
    }
}
