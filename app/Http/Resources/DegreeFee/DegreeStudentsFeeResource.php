<?php

namespace App\Http\Resources\DegreeFee;

use App\Models\DegreeStudent;
use Illuminate\Http\Resources\Json\JsonResource;

class DegreeStudentsFeeResource extends JsonResource
{

  //  public $collects = DegreeStudent::class;

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
            'months'=>MonthResource::collection($this->month_payments)
        ];
    }
}
