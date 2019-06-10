<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'date'    => $this->date,
            'amount'  => $this->amount,
            'flat_id' => $this->flat_id,
            'comment' => $this->comment,
        ];
    }
}
