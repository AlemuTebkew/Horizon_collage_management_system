<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeStudent extends Model
{
    use HasFactory;
    public $fillable=['student_id','first_name',
    'last_name','password','sex','dob',
    'phone_no','martial_status', 'emergency_contact_name','emergency_contact_relationShip',
    'emergency_contact_phone_no','EGSSE_result','EHEEE_result',
    'birth_address_id','residential_address_id','contact_address_id',
    'program_id','degree_department_id','financial_source',
    'current_semester_no','current_year_no','batch',
    'employment_profile','isGraduated',

];

}
