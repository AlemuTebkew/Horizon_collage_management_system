<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $fillable=['short_description','more_description','end_date','vision','values','vission' ,'educational_goal'];


}
