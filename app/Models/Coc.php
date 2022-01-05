<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coc extends Model
{
    use HasFactory;
    public $fillable=['start_date','end_date','exam_week','academic_year_id'];
    public $timestamps = false;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // 'start_date' => 'datetime',
        // 'end_date' => 'datetime',
        // 'exam_week' => 'datetime',
    ];
    public function degree_students()
    {
        return $this->belongsToMany(DegreeStudent::class)
        ->withPivot(['application_date','result','nature_of_assesment','level_no','occupation_name']);
        // return $this->morphedByMany(DegreeStudent::class, 'cocable')
        // ->withPivot(['application_date','result','nature_of_assesment']);
     }

    public function tvet_students()
    {
        return $this->belongsToMany(TvetStudent::class)
        ->withPivot(['application_date','result','nature_of_assesment','level_no','occupation_name']);
        // return $this->morphedByMany(TvetStudent::class, 'cocable')
        // ->withPivot(['application_date','result','nature_of_assesment']);
    }
    public function academic_year(){
        return $this->belongsTo(AcademicYear::class);
    }
}
