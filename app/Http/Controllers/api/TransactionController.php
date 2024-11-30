<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
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
}
