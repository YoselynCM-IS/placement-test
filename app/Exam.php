<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Instruction;
use App\Categorie;
use App\Question;
use App\Teacher;
use App\Student;
use App\Advance;
use App\School;
use App\Group;
use App\Topic;

class Exam extends Model
{
    protected $fillable = [
        'teacher_id', 'school_id', 'group_id', 'name', 'start_date', 
        'start_time', 'end_time', 'duration', 'number_reagents', 'error_range', 
        'indications', 'send'
    ];

    // 1 a muchos (Inverso)
    public function school() {
        return $this->belongsTo(School::class);
    }

    // MUCHOS A MUCHOS 
    public function instructions(){
        return $this->belongsToMany(Instruction::class)->withPivot('level_id');
    }

    // 1 a muchos
    public function teacher() {
        return $this->belongsTo(Teacher::class);
    }

    // 1 a muchos (inversa)
    public function group(){
        return $this->belongsTo(Group::class);
    }

    // MUCHOS A MUCHOS 
    public function students(){
        return $this->belongsToMany(Student::class)
                    ->withPivot('level_id', 'correct', 'total', 'duration', 'start_time', 'end_time')
                    ->withTimestamps();
    }

    // 1 a muchos
    public function advances(){
        return $this->hasMany(Advance::class);
    }

    // MUCHOS A MUCHOS
    public function topics(){
        return $this->belongsToMany(Topic::class);
    }

    // MUCHOS A MUCHOS
    public function questions(){
        return $this->belongsToMany(Question::class)->withPivot('instruction_id');
    }

    // MUCHOS A MUCHOS
    public function categories(){
        return $this->belongsToMany(Categorie::class);
    }
}
