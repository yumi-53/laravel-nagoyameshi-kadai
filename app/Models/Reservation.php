<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reserved_datetime',
        'number_of_people',
        'restaurant_id',
        'user_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function user() {
        return $this->belongsTo(User::class);
    }
    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
    }
}
