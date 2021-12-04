<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    public $fillable=['code','title','type','cp','program_id','degree_department_id',

'semester_no','year_no',];

}
