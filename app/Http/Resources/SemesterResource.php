<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class SemesterResource extends JsonResource
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
            'year'=>$this->academic_year ? $this->academic_year->year:null,
            'academic_year_id'=>$this->academic_year ? $this->academic_year->id:null,
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'status'=>$this->pivot->status,
            'is_closed'=>$this->is_closed,
            'is_current'=>$this->is_current,
            'year_no'=>$this->pivot->year_no,
            'semester_no'=>$this->pivot->semester_no,
            'GPA'=>$this->pivot->semester_GPA??null,
            'legible'=> $this->pivot->legible, //check for student payment fee
            'is_allowed_now'=>  DB::table('dynamic_system_settings')->first()->degree_registrar_result_entry_time

        ];
      }
}
