<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
    use HasFactory;
    public $fillable=['code',];
    public function semesters(){
        return $this->belongsToMany(Month::class);
    }
//students paid for this month
    public function degree_paid_students(){
        return $this->belongsToMany(DegreeStudent::class);
    }

    public function tvet_paid_students(){
        return $this->belongsToMany(TvetStudent::class,'tvet_student_month');
    }
}
