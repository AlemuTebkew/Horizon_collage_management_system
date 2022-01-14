<?php

namespace App\Http\Resources\Head;

use Illuminate\Http\Resources\Json\JsonResource;

class TvetSectionResource extends JsonResource
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
            'tvet_department'=>$this->tvet_department,
            // 'academic_year'=>new AcademicYearResource($this->academic_year),
            'level'=>$this->level,
            'program'=>$this->program
        ];
    }
}
