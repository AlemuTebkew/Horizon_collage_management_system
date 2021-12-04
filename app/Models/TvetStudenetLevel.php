<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvetStudenetLevel extends Model
{
    use HasFactory;
    public $fillable=['tvet_student_id','academic_year_id',
    'level_id'];

    public function cocs(){
        return $this->morphMany(Coc::class,'cocable');
        }
}
