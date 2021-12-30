<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddedSemesterResource extends JsonResource
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
            'sex'=>$this->sex,
            'department'=>$this->degree_department->name ??null,
            'department_duration'=>$this->when($this->degree_department->programs,function(){
               return $this->degree_department->programs
               ->where('id',$this->program_id)->first()->pivot->no_of_year ?? null;
            })

            ,
            'program'=>$this->program ? $this->program->makeVisible('id','name'):null,
            'current_year_number'=>$this->current_year_no,
            'semesters'=> new SemesterResource($this->semesters()->where('number',$this->current_semester_no)->first())
        ];
    }
}
