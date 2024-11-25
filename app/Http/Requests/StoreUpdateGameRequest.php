<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'type' => 'required|in:S,M',
        'created_user_id' => 'required',
        'winner_user_id' => 'nullable',
        'status' => 'required|in:PE,PL,E,I',
        'board_id' => 'required|exists:boards,id',
    ];
    }
}
