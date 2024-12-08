<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


    public function toArray(Request $request): array
    {

        $photoBase64 = null;

        if ($this->photo_filename && Storage::disk('public')->exists('photos/' . $this->photo_filename)) {
            $photoContents = Storage::disk('public')->get('photos/' . $this->photo_filename);
            $photoBase64 = 'data:image/jpeg;base64,' . base64_encode($photoContents);
        }



        return [
            'id' => $this->id,
            'name' => $this->name,
            'password'=> $this->password,
            'email' => $this->email,
            'type' => $this->type,
            'nickname'=> $this->nickname,
            'blocked' => $this->blocked,
            'brain_coins_balance'=>$this->brain_coins_balance,
            'photo' => $photoBase64,
        ];
    }
}
