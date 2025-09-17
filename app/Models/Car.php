<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Car extends Model
{

    use HasFactory;

    protected $guarded = [];

    // Satu Mobil (Car) dimiliki oleh satu Model Mobil (CarModel)
    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function tempos()
    {
        return $this->hasMany(Tempo::class);
    }
    public function serviceHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ServiceHistory::class);
    }
}
