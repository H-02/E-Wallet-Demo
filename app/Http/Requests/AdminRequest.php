<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Constants\WalletConstants;
use App\Rules\SortQueryValidation;
use App\Rules\SearchQueryValidation;
use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends BaseRequest
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
        if ($this->route()->getName() == 'admin.users') {
            return [
                'sort_by' => [new SortQueryValidation(WalletConstants::UserSortBy)],
                'search' => [new SearchQueryValidation(WalletConstants::UserSearchBy)]
            ];
        } else if ($this->route()->getName() == 'admin.user.details')
        {
            $this->merge([
                "id" => $this->route("id"),
            ]);

            return [
                "id" => [
                    "required",
                    "integer",
                    "gt:0",
                    "exists:users,id,deleted_at,NULL"
                ]
            ];
        } else if ($this->route()->getName() == 'admin.user.deposit' || $this->route()->getName() == 'admin.user.withdraw') {
            $this->merge([
                "id" => $this->route("id"),
            ]);

            return [
                "id" => [
                    "required",
                    "integer",
                    "gt:0",
                    "exists:users,id,deleted_at,NULL",
                    function ($attribute, $value, $fail) {
                        $targetUser = User::find($value);

                        if ($targetUser && $targetUser->role === 'ADMIN' && $targetUser->id !== $this->user()->id) {
                            $fail('You cannot perform transactions for another user with the role of ADMIN.');
                        }
                    },
                ],
                "amount" => 'required|numeric|gt:0',
            ];
        }

        return [
            //
        ];
    }
}
