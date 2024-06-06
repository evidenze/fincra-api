<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminCreditRequest;
use App\Http\Requests\AdminDebitRequest;
use App\Models\User;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Credit user's wallet.
     *
     * @return \Illuminate\Http\Response
     */
    public function credit(AdminCreditRequest $request)
    {
        $user = User::where('wallet_id', $request->wallet_id)->first();

        try {

            DB::transaction(function () use ($user, $request) {
                $user->lockForUpdate();
                $user->balance += $request->amount;
                $user->save();

                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => $request->amount,
                ]);
            });

            return response()->json(['status' => true, 'message' => 'Amount credited successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Debit user's wallet.
     *
     * @return \Illuminate\Http\Response
     */
    public function debit(AdminDebitRequest $request)
    {
        $user = User::where('wallet_id', $request->wallet_id)->first();

        try {
            DB::transaction(function () use ($user, $request) {
                $user->lockForUpdate();

                if ($user->balance >= $request->amount) {
                    $user->decrement('balance', $request->amount);

                    $user->transactions()->create([
                        'type' => 'debit',
                        'amount' => $request->amount,
                        'user_id' => $user->id
                    ]);
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
     * Generate and return a weekly report of transactions.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing the weekly report.
     */
    public function weeklyReport()
    {
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $transactions = Transaction::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->paginate(20);

        $totalCredits = $transactions->where('type', 'credit')->sum('amount');
        $totalDebits = $transactions->where('type', 'debit')->sum('amount');

        return response()->json([
            'status' => true,
            'message' => 'Report fetched successfully',
            'data' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_credits' => $totalCredits,
                'total_debits' => $totalDebits,
                'transactions' => $transactions
            ]
        ]);
    }
}
