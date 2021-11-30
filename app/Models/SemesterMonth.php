<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterMonth extends Model
{
    use HasFactory;
    public $fillable=['degree_semester_id','month_id',];

}
