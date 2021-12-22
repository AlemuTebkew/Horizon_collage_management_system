<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    public $fillable=['first_name',
    'last_name','password','email','type','phone_no','profession','status'];

    public $hidden=['password','created_at','updated_at'];
    public function getFullNameAttribute(){
    return $this->first_name.' '.$this->last_name;
}
    public function modules(){
        return $this->belongsToMany(Module::class);
    }
    
    public function courses(){
        return $this->belongsToMany(Course::class,'teacher_section_courses');
    }
 
    public function course(){
        return $this->belongsTo(Course::class,'teacher_section_courses');
    }

}

