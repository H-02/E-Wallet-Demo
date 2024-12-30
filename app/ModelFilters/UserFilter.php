<?php

namespace App\ModelFilters;

use App\Constants\WalletConstants;
use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;
use App\EWallet\Helper\CommonHelper;

class UserFilter extends ModelFilter
{
    public function sortBy($sort)
    {
        $sort = CommonHelper::sortBy($sort);

        return $this->orderBy(WalletConstants::UserSortBy[$sort['sortBy']], $sort['sortOrder']);
    }

    public function search($query)
    {
        $search = CommonHelper::searchBy($query);

        return $this->where(WalletConstants::UserSearchBy[$search['search_on']], 'ILIKE', '%' . strtolower($search['search']) . '%')->orderBy(DB::raw("POSITION('" . strtolower($search['search']) . "' IN LOWER(" . WalletConstants::UserSearchBy[$search['search_on']] . "))"));
    }

    public function isActive($isActive)
    {
        $this->where('is_active', $isActive);
    }
}
