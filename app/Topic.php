<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Instruction;
use App\Level;

class Topic extends Model
{
    use SoftDeletes;

    protected $fillable = ['level_id', 'topic'];
    
    // 1 a muchos (INSTRUCTIONS)
    public function instructions(){
        return $this->hasMany(Instruction::class);
    }

    // 1 a muchos (INVERSA)
    public function level(){
        return $this->belongsTo(Level::class);
    }
}
