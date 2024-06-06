<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditRequest;
use App\Http\Requests\DebitRequest;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Credit user's wallet.
     *
     * @return \Illuminate\Http\Response
     */
    public function credit(CreditRequest $request)
    {
        $user = User::find(Auth::id());
    
        try {
            DB::transaction(function () use ($user, $request) {
                $user->lockForUpdate();
                $user->increment('balance', $request->amount);
                $user->transactions()->create(['type' => 'credit', 'amount' => $request->amount, 'user_id' => $request->user_id]);
            });

            return response()->json(['status' => true, 'message' => 'Amount credited successfully', 'data' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Debit user's wallet.
     *
     * @return \Illuminate\Http\Response
     */
    public function debit(DebitRequest $request)
    {
        $user = User::find(Auth::id());

        try {
            DB::transaction(function () use ($user, $request) {
                $user->lockForUpdate();
                if ($user->balance >= $request->amount) {
                    $user->decrement('balance', $request->amount);
                    $user->transactions()->create(['type' => 'debit', 'amount' => $request->amount, 'user_id' => $request->user_id]);
                } else {
                    throw new \Exception('Insufficient balance');
                }
            });
            return response()->json(['status' => true, 'message' => 'Amount debited successfully', 'data' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get transactions for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTransactions()
    {
        $user = Auth::user();

        $transactions = Transaction::where('user_id', $user->id)->get();

        return response()->json(['status' => true, 'message' => 'Transactions fetched successfully', 'data' => $transactions]);
    }
}
