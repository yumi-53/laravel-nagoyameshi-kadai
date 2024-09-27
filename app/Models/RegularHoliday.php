<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegularHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'regular_holiday_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function restaurants_regular_holidays() {
        return $this->belongsToMany(Restaurant::class)->withTimestamps();
    }
}
