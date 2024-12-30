<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Constants\WalletConstants;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends BaseRequest
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
        if ($this->route()->getName() === "user.deposit" || $this->route()->getName() === "user.withdraw") {
            return [
                'amount' => 'required|numeric|gt:0'
            ];
        } else if ($this->route()->getName() === "user.transactions") {
            return [
                'from_date' => ['nullable', 'date', 'before_or_equal:to_date'],
                'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
                'type' => ['nullable', Rule::in(WalletConstants::TransactionType)],
                'limit' => ['nullable', 'integer', 'gt:0']
            ];
        }

        return [];
    }
}
