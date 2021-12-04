<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;
    protected $fillable= ['level_no','occupation_name','tvet_department_id'];

    public function tvet_departments(){
        return $this->belongsTo(TvetDepartment::class);
    }
}
