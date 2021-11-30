<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvetStudentSection extends Model
{
    use HasFactory;
    public $fillable=['tvet_student_id','tvet_section_id',];
}
