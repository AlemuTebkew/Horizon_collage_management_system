<?php

namespace App\Http\Resources\TvetStudentInfo;

use App\Models\Level;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentCocResource extends JsonResource
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
            'year'=>$this->academic_year->year ?? null,
            'registration_date'=>$this->pivot->application_date,
            'exam_date'=>$this->exam_week,
            'occupation'=>Level::find($this->pivot->level_id)->occupation_name ?? null,
            'level'=>Level::find($this->pivot->level_id)->level_no ?? null,
            'result'=>$this->pivot->result ?? null,
        ];
    }
}
