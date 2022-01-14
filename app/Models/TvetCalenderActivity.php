<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvetCalenderActivity extends Model
{
    use HasFactory;

    public function academic_year(){
        return $this->belongsTo(AcademicYear::class);
    }
}
