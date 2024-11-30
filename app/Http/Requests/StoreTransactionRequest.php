<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:B,P,I',
            'transaction_datetime' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'game_id' => 'nullable|exists:games,id', // Only applicable for type 'I'
            'euros' => 'nullable|numeric|min:0', // Only for type 'P'
            'payment_type' => 'nullable|in:MBWAY,IBAN,MB,VISA', // Only for type 'P'
            'payment_reference' => 'nullable|string|max:255', // Only for type 'P'
            'brain_coins' => 'required|integer', // Positive or negative
            'custom' => 'nullable|json', // Must be a valid JSON string
        ];
    }
}
