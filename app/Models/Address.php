<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    public $fillable=['country','region','zone','subcity','kebele','house_no','town','contact_relationship'];
    public $timestamps=false;
    protected $hidden=['created_at','updated_at'];
public function degree_student(){
    return $this->hasOne(DegreeStudent::class);
}
public function tvet_student(){
    return $this->hasOne(TvetStudent::class);
}

}
