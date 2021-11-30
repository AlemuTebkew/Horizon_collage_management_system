<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvetSection extends Model
{
    use HasFactory;
    public $fillable=['tvet_department_id','academic_year_id',
    'program_id','level_id'];

}
