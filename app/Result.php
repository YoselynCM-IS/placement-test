<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Question;
use App\Advance;
use App\Track;

class Result extends Model
{
    protected $fillable = [
        'advance_id', 'question_id', 'answer', 'value', 'points'
    ];

    // 1 a muchos (Inverso)
    public function advance() {
        return $this->belongsTo(Advance::class);
    }

    // 1 a muchos (Inverso)
    public function question() {
        return $this->belongsTo(Question::class);
    }

    public function track(){
        return $this->hasOne(Track::class);
    }
}
