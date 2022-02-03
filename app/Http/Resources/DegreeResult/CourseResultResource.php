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
            // 'grade_point2'=>$this->cp*6,
           // 'department'=>$this->department ? $this->department:null ,

            'year_no'=>$this->year_no,
            'from_5'=>$this->pivot ? $this->pivot->from_5:null,
            'from_5s'=>$this->pivot ? $this->pivot->from_5s:null,
            'from_25'=>$this->pivot ? $this->pivot->from_25:null,
            'from_25s'=>$this->pivot ? $this->pivot->from_25s:null,
            'from_40'=>$this->pivot ? $this->pivot->from_40:null,
            'total_mark'=>$this->pivot ? $this->pivot->total_mark:null,
            'letter_grade'=>$this->pivot ? $this->pivot->letter_grade:null,
            'is_changed'=>0,
        ];
    }
}
