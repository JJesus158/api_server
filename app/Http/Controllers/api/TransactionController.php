<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameResource;
use App\Http\Resources\TransactionResource;
use App\Models\Game;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $itensPerPage = $request->input('itensPerPage', 10);

        if ($user->type == 'A') {
            return TransactionResource::collection(Transaction::orderBy('transaction_datetime', 'desc')->paginate($itensPerPage));
        }

        return TransactionResource::collection($user->transactions()->orderBy('transaction_datetime', 'desc')->paginate($itensPerPage));
    }

    public function store(Request $request){

        $user = $request->user();
        $transaction = new Transaction();

        $transaction->transaction_datetime = now();
        $transaction->user_id = $user->id;
        $transaction->type = 'P';
        $transaction->euros = $request->input('value');
        $transaction->brain_coins = $request->input('value') * 10;
        $transaction->payment_type = $request->input('type');
        $transaction->payment_reference = $request->input('reference');
        $transaction->save();

        $user->brain_coins_balance += $transaction->brain_coins;
        $user->save();

        return new TransactionResource($transaction);

    }
}
