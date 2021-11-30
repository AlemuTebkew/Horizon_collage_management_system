<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeStudentSemester extends Model
{
    use HasFactory;

    public $fillable=['number','degree_student_id',
    'semester_id','semester_GPA','tution_type',
];
}
