<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;


    public $fillable=['first_name',
    'last_name','password','email','role','phone_no','status','sex'];

    protected $hidden=['password','created_at','updated_at','status'];

    public function getFullNameAttribute(){
        return $this->first_name.' '.$this->last_name;
    }
    public function manage(){
        return $this->hasOne(DegreeDepartment::class,'department_head_id');
    }

    public function managet(){
        return $this->hasOne(TvetDepartment::class,'department_head_id');
    }
}
