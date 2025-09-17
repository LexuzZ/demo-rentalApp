<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'tanda_tangan_customer',

    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
