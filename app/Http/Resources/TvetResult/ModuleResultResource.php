<?php

namespace App\Http\Resources\TvetResult;

use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResultResource extends JsonResource
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
            'code'=>$this->code,
            'title'=>$this->title,
            'training_hour'=>$this->training_hour,
            'department'=>$this->department ? $this->department->makeHidden('created_at','department_head_id','updated_at'):null ,
            'level'=>$this->level ? $this->level->level_no:null,
            'from_20'=> $this->pivot->from_20 ?? null,
            'from_30'=> $this->pivot->from_30 ?? null,
            'from_50'=> $this->pivot->from_50 ?? null,
            'total_mark'=> $this->pivot->total_mark ?? null,
            'is_changed'=>0,
        ];;
    }
}
