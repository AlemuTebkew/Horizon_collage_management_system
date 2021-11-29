<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coc extends Model
{
    use HasFactory;
    public $fillable=['start_date','end_date','exam_week','academic_year_id'];

}
