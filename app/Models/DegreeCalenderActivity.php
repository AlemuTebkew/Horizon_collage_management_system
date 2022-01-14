<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeCalenderActivity extends Model
{
    use HasFactory;

    public $hidden=['created_at','updated_at'];

    public function semester(){
        return $this->belongsTo(Semester::class);
    }
}
