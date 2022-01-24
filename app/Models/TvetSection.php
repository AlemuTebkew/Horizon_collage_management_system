<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvetSection extends Model
{
    use HasFactory;
    public $fillable=['name','tvet_department_id','academic_year_id',
    'program_id','level_id'];
    public function tvet_department(){
        return $this->belongsTo(TvetDepartment::class);
    }
    public function academic_year(){
        return $this->belongsTo(AcademicYear::class);
    }
    public function program(){
        return $this->belongsTo(Program::class);
    }
    public function level(){
        return $this->belongsTo(Level::class);
    }
    public function tvet_students(){
        return $this->belongsToMany(TvetStudent::class,'tvet_student_section');
    }

    public function teachers(){
        return $this->belongsToMany(Teacher::class,'teacher_section_modules')->withPivot('teacher_id','module_id','tvet_section_id',
        'room_no','hours_per_week' ,'period','class_start_date','class_end_date','exam_week');
    }
}
