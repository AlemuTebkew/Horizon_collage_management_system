<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

  
    public $fillable=['first_name',
    'last_name','password','email','role_id','phone_no','status'];

    public function getFullNameAttribute(){
        return $this->first_name.' '.$this->last_name;
    }
    public function manages(){
        return $this->hasMany(DegreeDepartment::class,'department_head_id');
    }
}
