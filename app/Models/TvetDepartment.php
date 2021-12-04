<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvetDepartment extends Model
{
    use HasFactory;
    public $fillable=['name','sector','department_head_id'];

    public function levels(){
        return $this->hasMany(Level::class);
    }
}
