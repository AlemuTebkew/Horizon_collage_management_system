<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGalary extends Model
{
    use HasFactory;
    public $timestamps = false;

    // protected $appends=true;

    public function getUrlAttribute($key)
    {
        return asset('/images/student_images/'.$key);
    }
}
