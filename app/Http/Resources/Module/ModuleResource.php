<?php

namespace App\Http\Resources\Module;

use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
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
            'code'=>$this->code,
            'title'=>$this->title,
            'training_hour'=>$this->training_hour,
            'department'=>$this->department ? $this->department->makeHidden('created_at','department_head_id','updated_at'):null ,
            'level'=>$this->level ? $this->level->level_no:null,
        ];
    }
}
