<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    public $fillable=['year','start_date','end_date','status','is_current'];

    public $hidden=['created_at','updated_at'];
    public function tvet_section(){
        return $this->hasMany(TvetSection::class);
    }

    public function degree_sections(){
        return $this->hasMany(DegreeSection::class);
    }
    public function cocs(){
        return $this->hasMany(Coc::class);
    }

    public function months(){
        return $this->belongsToMany(Month::class)->withPivot('month_id','academic_year_id','selected');
    }
    public function semesters(){
        return $this->hasMany(Semester::class);
    }
    public function fee_types(){
        return $this->belongsToMany(FeeType::class,'academic_fees')
        ->withPivot('fee_type_id','academic_year_id','amount');
    }

    public function tvet_calender_activities(){
        return $this->hasMany(TvetCalenderActivity::class,'academic_year_id');
    }
}
