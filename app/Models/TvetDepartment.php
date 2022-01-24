<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvetDepartment extends Model
{
    use HasFactory;
    public $fillable=['name','sector','department_head_id','short_name'];

    public function levels(){
        return $this->hasMany(Level::class);
    }
    public function modules(){
        return $this->hasMany(Module::class);
    }
    public function tvet_sections(){
        return $this->hasMany(TvetSection::class);
    }

    public function tvet_students(){
        return $this->hasMany(TvetStudent::class);
    }

    public function programs(){
        return $this->belongsToMany(Program::class,'tvet_department_program');
    }

    public function manager(){
        return $this->belongsTo(Employee::class,'department_head_id');
    }
    // public function department_head(){
    //     return $this->belongsTo(Dep)
    // }
}
