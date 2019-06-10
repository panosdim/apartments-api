<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LesseesResource extends JsonResource
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
            'id'          => $this->id,
            'name'        => $this->name,
            'address'     => $this->address,
            'postal_code' => $this->postal_code,
            'from'        => $this->from,
            'until'       => $this->until,
            'flat_id'     => $this->flat_id,
        ];
    }
}
