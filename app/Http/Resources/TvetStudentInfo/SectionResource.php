<?php

namespace App\Http\Resources\TvetStudentInfo;

use App\Models\Level;
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
            'academic_year'=>$this->academic_year->year,
            'level'=>Level::find($this->level_id)->level_no ?? null,
        ];
    }
}
