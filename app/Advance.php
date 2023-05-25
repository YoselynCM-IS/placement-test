<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Instruction;
use App\Question;
use App\Student;
use App\Result;
use App\Level;
use App\Exam;

class Advance extends Model
{
    protected $fillable = [
        'student_id', 'exam_id', 'level_id', 'instruction_id',
        'correct', 'total'
    ];

    // 1 a muchos (Inverso)
    public function student() {
        return $this->belongsTo(Student::class);
    }

    // 1 a muchos (Inverso)
    public function exam() {
        return $this->belongsTo(Exam::class);
    }

    // 1 a muchos (Inverso)
    public function level() {
        return $this->belongsTo(Level::class);
    }

    // 1 a muchos (Inverso)
    public function instruction() {
        return $this->belongsTo(Instruction::class);
    }

    // 1 a muchos
    public function results(){
        return $this->hasMany(Result::class);
    }
}
