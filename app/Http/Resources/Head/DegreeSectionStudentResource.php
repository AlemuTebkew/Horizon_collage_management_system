<?php

namespace App\Http\Resources\Head;

use Illuminate\Http\Resources\Json\JsonResource;

class DegreeSectionStudentResource extends JsonResource
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
            'student_id'=>$this->student_id,
            'full_name'=>$this->full_name,
            'sex'=>$this->sex
        ];
    }
}
