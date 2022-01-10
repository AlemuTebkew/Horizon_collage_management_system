<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeStudent extends Model
{
    use HasFactory;
    public $fillable=['student_id','first_name',
    'last_name','middle_name','sex','dob',
    'phone_no','residence_office_tel','residence_tel','maritial_status',
     'contact_full_name','contact_relationship',
    'contact_tel','contact_phone_no','contact_office_tel','EGSSE_result','EHEEE_result',
    'birth_address_id','residential_address_id','emergency_address_id',
    'program_id','degree_department_id','financial_source',
    'current_semester_no','current_year_no','batch',
    'is_graduated','fully_scholarship',''

];

public $hidden=['password','created_at','updated_at'];
public function getFullNameAttribute(){
    return $this->first_name.' '.$this->last_name;
}


public function cocs(){
    return $this->belongsToMany(Coc::class)
    ->withPivot(['application_date','result','nature_of_assesment','level_no','occupation_name']);
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
    'semester_avarege','cgpa','tuition_type','status','monthly_cp_fee'])
                        ->withTimestamps();
}

/*
 *this mothode is for monthly payment not for month
*/
public function month_payments(){
    return $this->belongsToMany(Month::class)
    ->withPivot('receipt_no','academic_year_id','month_id','paid_date','paid_amount','payable_status','fee_type_id')
    ->withTimestamps();
}
public function birth_address(){
    return $this->belongsTo(Address::class,'birth_address_id');
}
public function residential_address(){
    return $this->belongsTo(Address::class,'residential_address_id');
}
public function contact_address(){
    return $this->belongsTo(Address::class,'contact_address_id');
}

public function degree_department(){
    return $this->belongsTo(DegreeDepartment::class);
}

public function courses(){
    return $this->belongsToMany(Course::class,'student_semester_courses')
    ->withPivot('semester_id','total_mark','grade_point',
    'from_11','from_12','from_12s','from_25','from_40'
);


}

public function degree_sections(){
    return $this->belongsToMany(DegreeSection::class,'degree_student_section');
}

}
