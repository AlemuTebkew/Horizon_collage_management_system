<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicFee extends Model
{
    use HasFactory;
    public $fillable=['academic_type_id','academic_year_id','amount'];

}
