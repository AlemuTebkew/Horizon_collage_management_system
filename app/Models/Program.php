<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    public $fillable=['name','type',];

    protected $hidden=['created_at','updated_at'];
    public function tvet_student(){
        return $this->hasMany(TvetStudent::class);
    }

    public function courses(){
        return $this->hasMany(Course::class);
    }
    public function degree_departments(){
        return $this->belongsToMany(DegreeDepartment::class)
        ->withPivot(['no_of_semester','no_of_year','program_id','degree_department_id']);
    }
      public function tvet_departments(){
        return $this->belongsToMany(TvetDepartment::class,'tvet_department_program');
    }
    public function tvet_sections(){
        return $this->hasMany(TvetSection::class);
    }
    public function degree_sections(){
        return $this->hasMany(DegreeSection::class);
    }
    public function degree_students(){
        return $this->hasMany(DegreeStudent::class);
    }
    public function semesters(){
        return $this->hasMany(Semester::class);
    }
}
