<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    public $fillable=['first_name',
    'last_name','password','email','type','phone_no','profession','status'];

    protected $hidden=['password'];

    public function modules(){
        return $this->belongsToMany(Module::class);
    }
    
    public function coureses(){
        return $this->belongsToMany(Course::class);
    }
}

