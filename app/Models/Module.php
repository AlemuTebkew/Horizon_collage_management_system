<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    public $fillable=['code','title','training_hour','tvet_department_id','level_id'];

public function level(){
    return $this->belongsTo(Level::class);
}
public function department(){
    return $this->belongsTo(TvetDepartment::class,'tvet_department_id');
}

public function program(){
    return $this->belongsTo(Program::class);
}
public function teachers(){
    return $this->belongsToMany(Teacher::class);
}

public function tvet_students(){
    return $this->belongsToMany(TvetStudent::class,'student_level_module')
    ->withPivot('level_id','module_id','total_mark');


}
}
