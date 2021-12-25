<?php

namespace App\Http\Resources\DegreeResult;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return  [
            'id'=>$this->id,
            'code'=>$this->code,
            'title'=>$this->title,
            'semester_no'=>$this->semester_no,
            'cp'=>$this->cp,
            'grade_point2'=>$this->cp*6,
           // 'department'=>$this->department ? $this->department:null ,
            'year_no'=>$this->year_no,
            'total_mark'=>$this->pivot ? $this->pivot->total_mark:null,
            'grade_point'=>$this->pivot ? $this->pivot->grade_point:null,
        ];
    }
}
