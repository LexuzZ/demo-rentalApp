<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceHistory extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'car_id',
        'service_date',
        'jenis_service',
        'current_km',
        'description',
        'workshop',
        'next_km',
        'next_service_date',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
