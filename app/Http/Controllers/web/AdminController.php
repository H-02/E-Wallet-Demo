<?php

namespace App\Http\Controllers\web;

use Exception;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Constants\WalletConstants;
use App\Http\Requests\AdminRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function showDashboard()
    {
        return view('admin.dashboard');
    }

    public function getAllUsers(AdminRequest $request)
    {
        $users = User::filter($request->all())->select('id', 'name', 'email', 'wallet_balance')->get();

        // Return JSON response for AJAX
        if ($request->ajax()) {
            return response()->json([
                'users' => $users
            ]);
        }

        return view('admin.dashboard', [
            'users' => $users
        ]);
    }

    public function getUserDetails($id)
    {
        $user = User::findOrFail($id);
        $transactions = $user->transactions()->orderBy('transaction_time', 'desc')->get();

        return response()->json([
            'user' => $user,
            'transactions' => $transactions
        ]);
    }

    public function depositToUser(AdminRequest $request)
    {
        return $this->processTransaction(WalletConstants::TransactionType['DEPOSIT'], $request);
    }

    /**
     * Process transaction for deposit or withdrawal.
     *
     * @param string $transactionType
     * @param float $amount
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    private function processTransaction($transactionType, Request $request)
    {
        try {
            $userId = $request->id;
            $amount = $request->amount;
            $user = User::findOrFail($userId);

            // Get the logged-in user's role
            $loggedInUser = Auth::user();

            if ($user->role === 'ADMIN' && $loggedInUser->id !== $user->id) {
                return response()->json(['status' => 'error', 'message' => 'Admins cannot perform transactions on other admin accounts.'], 400);
            }

            // Check for sufficient balance in case of withdrawal
            if ($transactionType === WalletConstants::TransactionType['WITHDRAW'] && $user->wallet_balance < $amount) {
                return response()->json(['status' => 'error', 'message' => 'Insufficient balance'], 400);
            }

            // Log the transaction
            Transaction::create([
                'user_id' => $user->id,
                'type' => $transactionType,
                'amount' => $transactionType === WalletConstants::TransactionType['WITHDRAW'] ? -$amount : $amount,
                'created_by' => $loggedInUser->id,
                'updated_by' => $loggedInUser->id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $transactionType === WalletConstants::TransactionType['DEPOSIT'] ? "Funds Deposited successfully for " . $user->name : "Funds Withdrawn successfully for " . $user->name
            ]);
        } catch (Exception $e) {
            Log::error("Transaction Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'An error occurred while processing the transaction'], 500);
        }
    }

    public function withdrawFromUser(Request $request)
    {
        return $this->processTransaction(WalletConstants::TransactionType['WITHDRAW'], $request);
    }
}
