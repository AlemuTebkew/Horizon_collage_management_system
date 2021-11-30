<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;
    public $fillable=['number','academic_year_id',
    'program_id','start_date','end_date','status','is_current'];
}
