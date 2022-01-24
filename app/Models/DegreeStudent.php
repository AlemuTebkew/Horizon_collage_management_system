<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DegreeStudent extends Model
{
    use HasFactory;
   use SoftDeletes;
    public $fillable=['student_id','first_name',
    'last_name','middle_name','sex','dob',
    'phone_no','residence_office_tel','residence_tel','maritial_status',
     'contact_full_name','contact_relationship',
    'contact_tel','contact_phone_no','contact_office_tel','EGSSE_result','EHEEE_result',
    'birth_address_id','residential_address_id','emergency_address_id',
    'program_id','degree_department_id','financial_source',
    'current_semester_no','current_year_no','batch',
    'is_graduated','fully_scholarship'

];

public $timestamps = false;

public $hidden=['password','created_at','updated_at'];
public function getFullNameAttribute(){
    return $this->first_name.' '.$this->last_name;
}


public function cocs(){
    return $this->belongsToMany(Coc::class)
    ->withPivot(['application_date','result','nature_of_assesment','level_no','occupation_name','certificate_no']);
    //return $this->morphToMany(Coc::class,'cocable');
}

public function degree_other_fees(){

    return $this->belongsToMany(FeeType::class,'degree_other_fees');
}

public function program(){
     return $this->belongsTo(Program::class);
}
public function semesters(){
    return $this->belongsToMany(Semester::class)
    ->withPivot(['semester_no','year_no','semester_GPA','semester_grade_point','semester_credit_hour',
    'semester_average','cgpa','tuition_type','status','monthly_cp_fee','legible'])
                        ->withTimestamps();
}

/*
 *this mothode is for monthly payment not for month
*/
public function month_payments(){
    return $this->belongsToMany(Month::class)
    ->withPivot('receipt_no','academic_year_id','month_id','paid_date','paid_amount','fee_type_id')
    ->withTimestamps();
}
public function birth_address(){
    return $this->belongsTo(Address::class,'birth_address_id');
}
public function residential_address(){
    return $this->belongsTo(Address::class,'residential_address_id');
}
public function emergency_address(){
    return $this->belongsTo(Address::class,'emergency_address_id');
}

public function degree_department(){
    return $this->belongsTo(DegreeDepartment::class);
}

public function courses(){
    return $this->belongsToMany(Course::class,'student_semester_courses')
    ->withPivot('semester_id','total_mark','grade_point',
    'from_5','from_5s','from_25s','from_25','from_40','letter_grade','course_id','degree_student_id'
);


}

public function degree_sections(){
    return $this->belongsToMany(DegreeSection::class,'degree_student_section');
}

}
