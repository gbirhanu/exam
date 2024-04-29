<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];


    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

}