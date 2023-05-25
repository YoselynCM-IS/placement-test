<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\School;
use App\Group;
use App\User;
use App\Exam;

class Teacher extends Model
{
    protected $fillable = [ 'user_id', 'school_id' ];

    // 1 A 1 (INVERSA)
    public function user(){
        return $this->belongsTo(User::class);
    }

    // 1 A MUCHOS (INVERSA)
    public function school(){
        return $this->belongsTo(School::class);
    }

    // 1 A MUCHOS
    public function groups(){
        return $this->hasMany(Group::class);
    }

    // 1 A MUCHOS
    public function exams(){
        return $this->hasMany(Exam::class);
    }
}
