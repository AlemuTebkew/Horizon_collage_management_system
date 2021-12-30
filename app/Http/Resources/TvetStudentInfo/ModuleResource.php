<?php

namespace App\Http\Resources\TvetStudentInfo;

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

            'id'=>$this->id,
            'code'=>$this->code,
            'title'=>$this->title,
            'training_hour'=>$this->training_hour,
            'level'=>$this->level ? $this->level->level_no:null,
        ];
    }
}
