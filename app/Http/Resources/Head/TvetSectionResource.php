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
            'academic_year_id'=>$this->academic_year_id,
            'tvet_department'=>$this->tvet_department ?? null,
            // 'academic_year'=>new AcademicYearResource($this->academic_year),
            'level'=>$this->level ?? null,
            'program'=>$this->program
        ];
    }
}
