<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Categorie;
use App\Question;
use App\Level;
use App\Topic;
use App\Exam;

class Instruction extends Model
{
    protected $fillable = ['topic_id', 'instruction', 'categorie_id', 'link'];
    
    use SoftDeletes;
    
    // 1 a muchos (QUESTIONS)
    public function questions(){
        return $this->hasMany(Question::class);
    }

    // MUCHOS A MUCHOS 
    public function exams(){
        return $this->belongsToMany(Exam::class)->withPivot('level_id');
    }

    // 1 a muchos (INVERSA)
    public function level(){
        return $this->belongsTo(Level::class);
    }

    // 1 a muchos (INVERSA)
    public function topic(){
        return $this->belongsTo(Topic::class);
    }

    // 1 a muchos (INVERSA)
    public function categorie(){
        return $this->belongsTo(Categorie::class);
    }
}
