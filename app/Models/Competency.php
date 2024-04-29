<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competency extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];



    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function exams()
    {
        // Define a hasManyThrough relationship to retrieve exams indirectly
        return $this->hasManyThrough(
            Exam::class,
            Course::class,
            'competency_id',
            'course_id',
            'id',
            'id'
        );
    }

    public function getExamNamesAttribute()
    {
        return $this->exams->pluck('name')->join(', ');
    }

    public function getExamDescriptionsAttribute()
    {
        return $this->exams->pluck('description')->join(', ');
    }

    //course name   
    public function getCourseNamesAttribute()
    {
        return $this->exams->pluck('course.name')->unique()->join(', ');
    }
    //exam id 
    public function getExamIdsAttribute()
    {
        return $this->exams->pluck('id')->join(', ');
    }
}
