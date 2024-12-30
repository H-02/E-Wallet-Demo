<?php
namespace App\Http\Resources;

use App\EWallet\Helper\CommonHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Customize the output data format here
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'amount' => CommonHelper::number(abs($this->amount)),
            'transaction_time' => CommonHelper::datetime($this->transaction_time),
            'action_by' => $this->created_by,
            'is_user_updated' => $this->is_user_updated,
        ];
    }
}
