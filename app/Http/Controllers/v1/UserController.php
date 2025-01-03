<?php
namespace App\Http\Controllers\v1;

use Exception;
use App\Models\User;
use App\Models\Transaction;
use App\EWallet\Helper\CommonHelper;
use App\Constants\WalletConstants;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserBalanceResource;

class UserController extends ApiController
{
    public function getWalletBalance(UserRequest $request)
    {
        try {
            $user = User::find($request->user_id);

            if (!$user) {
                return $this->response(401, [], "User not found");
            }

            return $this->response(200, ['user' => new UserBalanceResource($user)], "Wallet balance retrieved successfully");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->response(500, [], "An error occurred while fetching wallet balance");
        }
    }

    private function processTransaction($transactionType, $amount, $request)
    {
        try {
            $user = User::find($request->user_id);

            if (!$user) {
                return $this->response(401, [], "User not found");
            }

            if ($transactionType === WalletConstants::TransactionType['WITHDRAW'] && $user->wallet_balance < $amount) {
                return $this->response(400, [], "Insufficient balance");
            }

            Transaction::create([
                'user_id' => $user->id,
                'type' => $transactionType,
                'amount' => $transactionType === WalletConstants::TransactionType['WITHDRAW'] ? -1 * $amount : $amount,
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]);

            return $this->response(
                200,
                [],
                $transactionType === WalletConstants::TransactionType['DEPOSIT'] ? "Funds Deposited successfully" : "Funds Withdrawn successfully"
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->response(500, [], "An error occurred while processing the transaction");
        }
    }

    public function depositFunds(UserRequest $request)
    {
        $amount = $request->validated()['amount'];
        return $this->processTransaction(WalletConstants::TransactionType['DEPOSIT'], $amount, $request);
    }

    public function withdrawFunds(UserRequest $request)
    {
        $amount = $request->amount;
        return $this->processTransaction(WalletConstants::TransactionType['WITHDRAW'], $amount, $request);
    }

    public function getTransactionHistory(UserRequest $request)
    {
        try {
            $user = User::find($request->user_id);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $from_date = $request->from_date ?? CommonHelper::current_date();
            $to_date = $request->to_date ?? CommonHelper::current_date();

            $query = Transaction::where('user_id', $request->user_id);

            if ($request->type) {
                $query->where('type', $request->type);
            }

            $query->whereDate('transaction_time', '>=', $from_date)
                ->whereDate('transaction_time', '<=', $to_date);

            $transactions = $query->orderBy('transaction_time', 'desc')
                                ->paginate($request->limit ?? 10);

            return $this->response(
                200,
                ['transactions' => TransactionResource::collection($transactions)],
                "Transaction History fetched Successfully.",
                $this->getMetaData($transactions)
            );

        } catch (\Exception $e) {
            Log::error("Error fetching transaction history: " . $e->getMessage());

            return $this->response(500, [], "An error occurred while fetching transaction history. Please try again later.");
        }
    }
}