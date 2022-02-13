<?php

namespace App\Http\Resources;

use App\Models\AcademicYear;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class AddedLevelResource extends JsonResource
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
            'year'=>$this->pivot->academic_year_id ? AcademicYear::find($this->pivot->academic_year_id)->year:null,
            'status'=>$this->pivot->status,
            'level_no'=>$this->level_no,
            'legible'=> $this->pivot->legible, //check for student payment fee
            'is_allowed_now'=>  DB::table('dynamic_system_settings')->first()->tvet_registrar_result_entry_time
       
        ];
    }
}
