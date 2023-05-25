<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Instruction;
use App\Answer;

class Question extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'instruction_id', 'type_id', 'question', 'answer',
    ];

    // 1 a muchos (ANSWERS)
    public function answers(){
        return $this->hasMany(Answer::class);
    }

    // 1 a muchos (INVERSA)
    public function instruction(){
        return $this->belongsTo(Instruction::class);
    }
}
