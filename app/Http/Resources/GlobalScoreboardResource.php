<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GlobalScoreboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'single_player' => $this->singlePlayerScoreboard(),
            'multiplayer' => $this->multiplayerScoreboard(),
        ];
    }

    /**
     * Format single-player scoreboard data.
     */
    private function singlePlayerScoreboard()
    {

        return $this->resource['single_player']->map(function ($boardStats) {
            return [
                'board_size' => $boardStats['board_size'],
                'best_times' => $boardStats['best_times'],
                'min_turns' => $boardStats['min_turns'],
            ];
        });
    }

    /**
     * Format multiplayer scoreboard data.
     */

    private function multiplayerScoreboard()
    {
        return $this->resource['multiplayer']->map(function ($playerStats) {
            return [
                'nickname' => $playerStats['nickname'],
                'total_victories' => $playerStats['total_victories']
            ];
        });
    }
}
