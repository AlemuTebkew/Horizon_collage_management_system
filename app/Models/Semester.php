<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;
    public $fillable=['number','academic_year_id',
    'program_id','start_date','end_date','status','is_current'];

    public $hidden=['created_at','updated_at'];

    public function degree_students(){
        return $this->belongsToMany(DegreeStudent::class)->withPivot(['semester_no','year_no',
        'semester_GPA','semester_grade_point','semester_credit_hour','semester_avarege','cgpa','tuition_type','status','partial_scholarship']);
    }
    public function months(){
        return $this->belongsToMany(Month::class,'semester_months');
    }
    public function student_payments(){
        return $this->belongsToMany(DegreeStudent::class,'student_semester_payment');
    }

    public function academic_year(){
        return $this->belongsTo(AcademicYear::class);
    }

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function degree_sections(){
        return $this->hasMany(DegreeSection::class);
    }

    public function degree_calender_activities(){
        return $this->hasMany(DegreeCalenderActivity::class,'semester_id');
    }

    }

