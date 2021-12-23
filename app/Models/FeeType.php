<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    use HasFactory;
    public $fillable=['name'];


    public function degree_paids(){

        return $this->belongsToMany(FeeType::class,'degree_other_fees');
    }
    public function tvet_paids(){

        return $this->belongsToMany(FeeType::class,'tvet_other_fees');
    }
    public function academic_years(){
        return $this->belongsToMany(AcademicYear::class,'academic_fees')
        ->withPivot('fee_type_id','academic_year_id','amount');
    }
}
