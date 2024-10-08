<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'score',
        'content',
    ];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
    }
    
}
