<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalCocApplicant extends Model
{
    use HasFactory;

    public $fillable=['first_name','last_name',
    'sex','dob','phone_no',
    'maritial_status','coc_id',
    'level_no','occupation_name','application_date','result','nature_of_assesment'
];
}
