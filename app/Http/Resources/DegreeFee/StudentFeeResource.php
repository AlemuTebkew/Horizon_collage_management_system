<?php

namespace App\Http\Resources\DegreeFee;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentFeeResource extends JsonResource
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
            'id'=>$this->id,
            'name'=>$this->full_name,
            'student_id'=>$this->student_id,
            'semesters'=>FeeSemesterResource::collection($this->semester_payments)
        ];
    }
}
