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
    return $this->belongsToMany(Teacher::class,'teacher_section_modules')->withPivot('teacher_id','module_id','tvet_section_id',
    'room_no','hours_per_week' ,'period','class_start_date','class_end_date','exam_week',
 );
}

public function tvet_students(){
    return $this->belongsToMany(TvetStudent::class,'student_level_module')
    ->withPivot('level_id','module_id','total_mark','from_20','from_30','from_50',

);


}
}
