<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Result;

class Track extends Model
{
    protected $fillable = [
        'result_id', 'name', 'size', 'extension', 'public_url'
    ];

    public function getSizeInKbAttribute() {
        return number_format($this->size / 1024, 1);
    }

    // 1 A 1 (INVERSA)
    public function result(){
        return $this->belongsTo(Result::class);
    }
}
