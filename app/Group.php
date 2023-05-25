<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Student;
use App\Exam;

class Group extends Model
{
    protected $fillable = [
        'school_id', 'teacher_id', 'group'
    ];

    public function students(){
        return $this->hasMany(Student::class);
    }

    public function exams(){
        return $this->hasMany(Exam::class);
    }
}
