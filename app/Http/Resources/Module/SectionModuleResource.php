<?php

namespace App\Http\Resources\Module;

use Illuminate\Http\Resources\Json\JsonResource;

class SectionModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'id'=>$this->id,
            'code'=>$this->code,
            'title'=>$this->title,
            'training_hour'=>$this->training_hour,
            // 'cp'=>$this->cp,
           // 'department'=>$this->department ? $this->department:null ,
            //'year_no'=>$this->year_no,
            'instructor'=>$this->teacher->first() ? $this->teacher->first()->full_name:null,

        ];
    }
}
