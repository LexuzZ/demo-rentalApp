<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    //
    use HasFactory;

    protected $guarded = [];

    // Satu Merek (Brand) memiliki banyak Model Mobil (CarModel)
    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class);
    }
}
