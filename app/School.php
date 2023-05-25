<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Teacher;
use App\Exam;

class School extends Model
{

    protected $fillable = [ 'school' ];

    // 1 a muchos
    public function exams(){
        return $this->hasMany(Exam::class);
    }

    public function teachers(){
        return $this->hasMany(Teacher::class);
    }
}
