<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterPayment extends Model
{
    use HasFactory;
    public $fillable=['degree_semester_id','degree_student_id',
    'academic_fee_id','receipt_no','amount'];
    public function degree_student(){
        return $this->belongsToMany(DegreeStudent::class);
   }
   public function semesters(){
    return $this->belongsTo(Semester::class);
}

}
