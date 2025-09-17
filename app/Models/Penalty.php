<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
     use HasFactory;

    protected $fillable = [
        'booking_id',
        'klaim',
        'description',
        'amount',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    
}
