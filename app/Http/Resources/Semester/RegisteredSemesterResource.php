<?php

namespace App\Http\Resources\Semester;

use Illuminate\Http\Resources\Json\JsonResource;

class RegisteredSemesterResource extends JsonResource
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
        'start_date'=>$this->start_date,
        'end_date'=>$this->end_date,
        'status'=>$this->status,
        'year'=>$this->academic_year->year,
        'year_no'=>$request->year_no,
        'semester_no'=>$this->number,
    ];
    }
}
