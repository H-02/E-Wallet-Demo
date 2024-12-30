<?php

namespace App\Http\Controllers\web;

use Exception;
use App\Models\User;
use App\Models\Transaction;
use App\EWallet\Helper\CommonHelper;
use App\Constants\WalletConstants;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Show the user dashboard.
     *
     * @return \Illuminate\View\View
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showDashboard()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        return view('user.dashboard', [
            'wallet_balance' => number_format($user->wallet_balance, 2),
        ]);
    }

    /**
     * Get the authenticated user's wallet balance (for AJAX/API calls).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWalletBalance()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            return response()->json([
                'status' => 'success',
                'wallet_balance' => number_format($user->wallet_balance, 2)
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'An error occurred while fetching wallet balance'], 500);
        }
    }

    /**
     * Deposit funds into the user's wallet.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function depositFunds(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            // Log the transaction
            Transaction::create([
                'user_id' => $user->id,
                'type' => WalletConstants::TransactionType['DEPOSIT'],
                'amount' => $request->amount,
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Funds deposited successfully',
                'wallet_balance' => number_format($user->wallet_balance, 2)
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'An error occurred while depositing funds'], 500);
        }
    }

    /**
     * Withdraw funds from the user's wallet.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdrawFunds(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            if ($user->wallet_balance < $request->amount) {
                return response()->json(['status' => 'error', 'message' => 'Insufficient balance'], 400);
            }

            // Log the transaction
            Transaction::create([
                'user_id' => $user->id,
                'type' => WalletConstants::TransactionType['WITHDRAW'],
                'amount' => -1 * $request->amount,
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Funds withdrawn successfully',
                'wallet_balance' => number_format($user->wallet_balance, 2)
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'An error occurred while withdrawing funds'], 500);
        }
    }

    /**
     * Fetch the user's transaction history (for AJAX/API calls).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactionHistory(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            $from_date = $request->from_date ?? CommonHelper::current_date();
            $to_date = $request->to_date ?? CommonHelper::current_date();

            $query = Transaction::where('user_id', $user->id);

            if ($request->type) {
                $query->where('type', $request->type);
            }

            $query->whereDate('transaction_time', '>=', $from_date)
                ->whereDate('transaction_time', '<=', $to_date);
            $transactions = $query->orderBy('transaction_time', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'transactions' => $transactions
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'An error occurred while fetching transaction history'], 500);
        }
    }
}
