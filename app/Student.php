<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Advance;
use App\Group;
use App\User;
use App\Exam;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'group_id'
    ];

    // 1 A MUCHOS (INVERSA)
    public function user(){
        return $this->belongsTo(User::class);
    }

    // 1 a muchos (Inversa)
    public function group(){
        return $this->belongsTo(Group::class);
    }

    // MUCHOS A MUCHOS 
    public function exams(){
        return $this->belongsToMany(Exam::class)
                    ->withPivot('level_id', 'correct', 'total', 'duration', 'start_time', 'end_time')
                    ->withTimestamps();
    }

    // 1 a muchos
    public function advances(){
        return $this->hasMany(Advance::class);
    }
}
