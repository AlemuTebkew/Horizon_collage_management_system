<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DegreeDepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
   // public static $wrap = 'degree_departments';

    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'short_name'=>$this->short_name,
            'department_head'=>$this->manager ? $this->manager->full_name:null,
            'head_id'=>$this->manager ? $this->manager->id:null,
            'programs'=>ProgramResource::collection($this->programs)
        ];
    }
}
