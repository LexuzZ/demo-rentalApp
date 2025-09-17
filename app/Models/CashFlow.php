<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CashFlow extends Model
{
    // Model ini tidak terkait tabel langsung
    protected $table = null;
    public $timestamps = false;

    public static function getQuery()
    {
        return DB::table('pengeluarans')
            ->select(
                'id',
                'tanggal_pengeluaran as tanggal',
                'nama_pengeluaran as keterangan',
                DB::raw("'Kas Keluar' as jenis"),
                'pembayaran'
            )
            ->unionAll(
                DB::table('payments')
                    ->select(
                        'id',
                        'tanggal_pembayaran as tanggal',
                        DB::raw("'Pembayaran Sewa' as keterangan"),
                        DB::raw("'Kas Masuk' as jenis"),
                        'pembayaran'
                    )
            );
    }
}
