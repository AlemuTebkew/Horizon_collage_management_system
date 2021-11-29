<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeDepartment extends Model
{
    use HasFactory;

    public $fillable=['name','no_of_semester','no_of_year'];
}
