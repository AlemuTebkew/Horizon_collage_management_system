<?php

namespace App\Http\Resources\Head;

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
            'degree_department'=>new DegreeDepartmentResource($this->degree_department),
            'academic_year'=>new AcademicYearResource($this->academic_year),
            'semester'=>new SemesterResource($this->semester),
            'program'=>new ProgramResource($this->programs)

        ];
    }
}
