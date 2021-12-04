<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    public $fillable=['name','type','semesters_in_year'];


    public function degree_departments(){
        return $this->belongsToMany(DegreeDepartment::class)->withPivot(['no_of_semester','no_of_year']);
    }

}
