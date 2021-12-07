<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvetDepartment extends Model
{
    use HasFactory;
    public $fillable=['name','sector','department_head_id'];

    public function levels(){
        return $this->hasMany(Level::class);
    }
    public function modules(){
        return $this->hasMany(Module::class);
    }
    public function tvet_sections(){
        return $this->hasMany(TvetSection::class);
    }
   
    // public function department_head(){
    //     return $this->belongsTo(Dep)
    // }
}
