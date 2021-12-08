<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;
    public $fillable=['number','academic_year_id',
    'program_id','start_date','end_date','status','is_current'];
    public function degree_students(){
        return $this->belongsToMany(DegreeStudent::class)->withPivot(['year_no','semester_no',
        'semester_GPA','tution_type']);
    }
    public function months(){
        return $this->belongsToMany(Month::class);
    }
    public function student_payments(){
        return $this->belongsToMany(DegreeStudent::class,'student_semester_payment');
    }
    }

