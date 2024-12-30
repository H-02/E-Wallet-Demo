<?php
namespace App\Http\Controllers\v1;

use Exception;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\AdminRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserBalanceResource;
use App\Constants\WalletConstants;

class AdminController extends ApiController
{
    public function getAllUsers(AdminRequest $request)
    {
        $users = User::filter($request->all())->paginate($request->limit ?? 10);
        return $this->response(
            200,
            ['users' => UserBalanceResource::collection($users)],
            "Users fetched Successfully.",
            $this->getMetaData($users)
        );
    }

    public function getUserDetails(AdminRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $transactions = $user->transactions()->orderBy('transaction_time', 'desc')->get();

        return $this->response(
            200,
            ['transactions' => TransactionResource::collection($transactions)],
            "User transactions fetched Successfully.",
        );
    }

    public function depositToUser(AdminRequest $request, $id)
    {
        return $this->processTransaction(WalletConstants::TransactionType['DEPOSIT'], $request, $id);
    }

    public function withdrawFromUser(Request $request, $id)
    {
        return $this->processTransaction(WalletConstants::TransactionType['WITHDRAW'], $request, $id);
    }

    /**
     * Process transaction for deposit or withdrawal.
     *
     * @param string $transactionType
     * @param float $amount
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    private function processTransaction($transactionType, $request, $userId)
    {
        try {
            $amount = $request->amount;
            $user = User::findOrFail($userId);

            // Check for sufficient balance in case of withdrawal
            if ($transactionType === WalletConstants::TransactionType['WITHDRAW'] && $user->wallet_balance < $amount) {
                return $this->response(400, [], "Insufficient balance");
            }

            // Log the transaction
            Transaction::create([
                'user_id' => $user->id,
                'type' => $transactionType,
                'amount' => $transactionType === WalletConstants::TransactionType['WITHDRAW'] ? -$amount : $amount,
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id
            ]);

            return $this->response(
                200,
                [],
                $transactionType === WalletConstants::TransactionType['DEPOSIT'] ? "Funds Deposited successfully for " : "Funds Withdrawn successfully for " . $user->name
            );
        } catch (Exception $e) {
            Log::error("Transaction Error: " . $e->getMessage());
            return $this->response(500, [], "An error occurred while processing the transaction");
        }
    }
}