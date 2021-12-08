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
    public function paid_students(){
        return $this->belongsToMany(DegreeStudent::class);
    }
}
