<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'car_id',
        'customer_id',
        'driver_id',
        'paket',
        'tanggal_keluar',
        'tanggal_kembali',
        'waktu_keluar',
        'total_hari',
        'waktu_kembali',
        'harga_harian',
        'estimasi_biaya',
        'identity_file',
        'status',
        'ttd',
        'lokasi_pengantaran',
        'lokasi_pengembalian',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function agreement()
{
    return $this->hasOne(Agreement::class);
}

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
      public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,  // model tujuan
            Invoice::class,  // model perantara
            'booking_id',    // Foreign key di tabel invoices
            'invoice_id',    // Foreign key di tabel payments
            'id',            // Primary key di tabel bookings
            'id'             // Primary key di tabel invoices
        );
    }

    public function penalty()
    {
        return $this->hasMany(Penalty::class);
    }
    protected function handleRecordCreation(array $data): Model
{
    $record = static::getModel()::create($data);

    // Ubah status booking menjadi 'selesai'
    if (isset($data['booking_id'])) {
        Booking::where('id', $data['booking_id'])
            ->update(['status' => 'selesai']);
    }

    return $record;
}
}
