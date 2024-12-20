<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_user' => $this->createdUser,
            'winner' => $this->winner,
            'type' => $this->type,
            'status' => $this->status,
            'began_at' => $this->began_at,
            'ended_at' => $this->ended_at,
            'total_time' => $this->total_time === null ? 0 : $this->total_time,
            'board_size' => ($this->board->board_rows * $this->board->board_cols),
            'board_id'=>$this->board_id,
            'total_turns_winner' => $this->total_turns_winner ?? 0,
            'custom' => $this->custom
        ];
    }
}
