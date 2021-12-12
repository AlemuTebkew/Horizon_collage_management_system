<?php

namespace App\Http\Resources\DegreeFee;

use Illuminate\Http\Resources\Json\JsonResource;

class FeeSemesterResource extends JsonResource
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

            'semester_no'=>$this->number,
            'pad'=>$this->pivot->receipt_no,
            'months'=> MonthResource::collection($this->months)
        ];
    }
}
