<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvetStudentFee extends Model
{
    use HasFactory;
    public $fillable=['tvet_student_id','academic_fee_id',
    'academic_year_id','month_id','paid_amount'
    ,'paid_date','receipt_no',
    'is_paid'];
    
}
