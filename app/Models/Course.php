<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    public $fillable=['code','title','type','cp','program_id','degree_department_id','semester_no','year_no',];
public $timestamps = false;

public function department(){
    return $this->belongsTo(DegreeDepartment::class,'degree_department_id');
}

public function program(){
    return $this->belongsTo(Program::class);
}

public function teachers(){
    return $this->belongsToMany(Teacher::class,'teacher_section_courses');
}
public function teacher(){
    return $this->belongsToMany(Teacher::class,'teacher_section_courses');

}

public function degree_students(){
    return $this->belongsToMany(DegreeStudent::class,'student_semester_courses')
    ->withPivot('semester_id','total_mark','grade_point',
    'from_5','from_5s','from_25s','from_25','from_40','letter_grade'
);
}
}
