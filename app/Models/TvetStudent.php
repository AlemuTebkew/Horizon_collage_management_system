<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvetStudent extends Model
{
    use HasFactory;
    public $fillable=['student_id','first_name',
    'last_name','password','sex','dob',
    'phone_no','martial_status', 'emergency_contact_name','emergency_contact_relationship',
    'emergency_contact_phone_no','EGSSE_result','EHEEE_result',
    'birth_address_id','residential_address_id','emergency_address_id',
    'program_id','tvet_department_id','financial_source',
    'no_of_level','current_level_no',
    'isGraduated','isGraduated','fully_scholarship'
];

public function cocs(){
    // return $this->belongsToMany(TvetStudent::class)
    // ->withPivot(['application_date','result','nature_of_assesment']);
    return $this->morphToMany(Coc::class,'cocable');
}

public function program(){
    return $this->belongsTo(Program::class);
}

public function levels(){
    return $this->belongsToMany(Level::class,'tvet_studenet_level');
}
//for payment
public function month_payments(){
    return $this->belongsToMany(Month::class,'tvet_student_month')->withPivot(['receipt_no','academic_year_id']);

}
public function tvet_sections(){
    return $this->belongsToMany(TvetSection::class);
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


}
