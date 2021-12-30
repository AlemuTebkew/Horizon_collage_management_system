<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeSection extends Model
{
    use HasFactory;

    public $fillable=['name','year_no','semester_no','degree_department_id','academic_year_id','semester_id','program_id'];
    public function degree_department(){
        return $this->belongsTo(DegreeDepartment::class);
    }
    public function academic_year(){
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(){
        return $this->belongsTo(Semester::class);
    }

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function degree_students(){
        return $this->belongsToMany(DegreeStudent::class,'degree_student_section');
    }
}
