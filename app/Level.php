<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Topic;

class Level extends Model
{
    use SoftDeletes;

    protected $fillable = ['level'];
    
    // 1 A MUCHOS (TOPICS)
    public function topics(){
        return $this->hasMany(Topic::class);
    }
}
