<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegularHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'day_index',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function restaurants_regular_holidays() {
        return $this->belongsToMany(Restaurant::class)->withTimestamps();
    }
}
