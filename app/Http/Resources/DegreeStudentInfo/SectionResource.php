<?php

namespace App\Http\Resources\DegreeStudentInfo;

use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
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
            'name'=>$this->name,
            'year_no'=>$this->year_no,
            'academic_year'=>$this->academic_year->year,
            'semester'=>$this->semester->number,
        ];
    }
}
