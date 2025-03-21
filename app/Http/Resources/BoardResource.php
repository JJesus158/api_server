<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'board_rows'=>$this->board_rows,
            'board_cols'=>$this->board_cols,
            'numberOfCards'=>($this->board_rows * $this->board_cols)
        ];
    }
}
