<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentLevelResource extends JsonResource
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
            'department'=>$this->department ? $this->department->name:null,
            'program'=>$this->program ? $this->program->name:null,
            'current_level_no'=>$this->current_level_no,
            'levels'=>LevelResource::collection($this->levels)
        ];
    }
}
