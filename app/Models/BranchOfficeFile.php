<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchOfficeFile extends Model
{
    use HasFactory;
    public $fillable=['branch_office_id','file_id','description'];

}
