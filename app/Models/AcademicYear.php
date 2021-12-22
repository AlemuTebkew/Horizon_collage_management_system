<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    public $fillable=['year','start_date','end_date','status','is_current'];
    public function tvet_section(){
        return $this->hasMany(TvetSection::class);
    }
    public function academic_fees(){
        return $this->hasMany(AcademicFee::class);
    }
    public function degree_sections(){
        return $this->hasMany(DegreeSection::class);
    }
    public function cocs(){
        return $this->hasMany(Coc::class);
    }

    public function months(){
        return $this->belongsToMany(Month::class);
    }
    public function semesters(){
        return $this->hasMany(Semester::class);
    }
}
