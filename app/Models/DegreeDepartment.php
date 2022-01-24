<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeDepartment extends Model
{
    use HasFactory;

    public $fillable=['name','department_head_id','short_name'];
    public $hidden=['created_at','updated_at',];

    public function programs(){
        return $this->belongsToMany(Program::class)
        ->withPivot(['no_of_semester','no_of_year','program_id','degree_department_id']);
    }
    public function degree_sections(){
        return $this->hasMany(DegreeSection::class);
    }

    public function degree_students(){
        return $this->hasMany(DegreeStudent::class);
    }
    public function courses(){
        return $this->hasMany(Course::class);
    }
    // public function degree_departments(){
    //     return $this->hasMany(DegreeDepartment::class);
    // }

    public function manager(){
        return $this->belongsTo(Employee::class,'department_head_id');
    }

}
