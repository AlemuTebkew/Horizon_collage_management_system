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
public function tvet_department(){
    return $this->belongsTo(TvetDepartment::class);
}

public function program(){
    return $this->belongsTo(Program::class);
}
public function teachers(){
    return $this->belongsToMany(Teacher::class);
}
}
