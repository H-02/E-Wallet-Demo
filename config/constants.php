<?php

namespace App\Constants;


class WalletConstants
{
    const TransactionType = [
        "DEPOSIT" => "DEPOSIT",
        "WITHDRAW" => "WITHDRAW"
    ];

    const UserSortBy = [
        "created" => "id",
        "modified" => "updated_at",
    ];

    const UserSearchBy = [
        "name" => "name",
        "email" => "email"
    ];


}
