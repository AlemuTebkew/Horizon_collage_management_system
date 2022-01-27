<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
    use HasFactory;
    public $fillable=['code',];
    public $hidden=['created_at','updated_at','pivot'];
    public function semesters(){
        return $this->belongsToMany(Semester::class,'semester_months');
    }
//students paid for this month
    public function degree_paid_students(){
        return $this->belongsToMany(DegreeStudent::class)
        ->withPivot('receipt_no','academic_year_id','month_id','paid_date','paid_amount','fee_type_id');
    }

    public function tvet_paid_students(){
        return $this->belongsToMany(TvetStudent::class,'tvet_student_month')
        ->withPivot('receipt_no','academic_year_id','month_id','paid_date','paid_amount','fee_type_id');

    }

    public function academic_years(){
        return $this->belongsToMany(AcademicYear::class)->withPivot('month_id','academic_year_id','selected');
    }
}
