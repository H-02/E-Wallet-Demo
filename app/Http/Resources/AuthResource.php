<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "profile"  =>  [
                        "id"    =>  $this->id,
                        "name"  =>  $this->name,
                        "email" =>  $this->email
                    ],
            "auth" =>  ["token"  =>  $this?->user_token]
        ];
    }
}
