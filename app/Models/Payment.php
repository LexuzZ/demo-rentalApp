<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'pembayaran',
        'metode_pembayaran',
        'tanggal_pembayaran',
        'proof',
        'status',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
