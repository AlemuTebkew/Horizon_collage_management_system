<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Resources\Json\JsonResource;

class SectionCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return
        [
            'id'=>$this->id,
            'code'=>$this->code,
            'title'=>$this->title,
            'type'=>$this->type,
            'cp'=>$this->cp,
           // 'department'=>$this->department ? $this->department:null ,
            //'year_no'=>$this->year_no,
            'instructor'=>$this->teacher->first() ? $this->teacher->first()->full_name:null,

        ];
    }
}
