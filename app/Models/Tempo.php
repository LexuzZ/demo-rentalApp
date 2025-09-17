<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tempo extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'car_id',
        'perawatan',
        'jatuh_tempo',
        'description',
    ];
    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
