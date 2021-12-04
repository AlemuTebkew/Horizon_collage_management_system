<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeDepartment extends Model
{
    use HasFactory;

    public $fillable=['name','department_head_id',];

    public function programs(){
        return $this->belongsToMany(Program::class)->withPivot(['no_of_semester','no_of_year']);
    }
}
