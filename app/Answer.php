<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'question_id', 'answer', 'value',
    ];
}
