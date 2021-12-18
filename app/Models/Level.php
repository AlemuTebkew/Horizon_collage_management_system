<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;
    protected $fillable= ['level_no','occupation_name','tvet_department_id'];

    public function tvet_departments(){
        return $this->belongsTo(TvetDepartment::class);
    }
    public function module(){
        return $this->hasMany(Module::class);
    }
    public function tvet_section(){
        return $this->hasMany(TvetSection::class);
    }

    public function tvet_students(){
        return $this->belongsToMany(TvetStudent::class,'tvet_student_level');
    }
}
