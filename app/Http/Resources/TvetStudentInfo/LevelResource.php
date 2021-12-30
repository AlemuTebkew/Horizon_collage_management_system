<?php

namespace App\Http\Resources\TvetStudentInfo;

use App\Models\AcademicYear;
use Illuminate\Http\Resources\Json\JsonResource;

class LevelResource extends JsonResource
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
            'level_no'=>$this->level_no,
            'year'=>$this->pivot->academic_year_id ? AcademicYear::find($this->pivot->academic_year_id)->year:null,
            'status'=>$this->pivot->status,
        ];
    }
}
