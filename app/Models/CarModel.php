<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarModel extends Model
{
    //
    use HasFactory;

    protected $guarded = [];

    // Satu Model Mobil (CarModel) dimiliki oleh satu Merek (Brand)
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    // Satu Model Mobil (CarModel) bisa dimiliki oleh banyak Mobil (Car)
    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
