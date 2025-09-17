<?php

namespace App\Observers;

use App\Models\ServiceHistory;
use App\Models\Tempo;

class ServiceHistoryObserver
{
    /**
     * Handle the "created" and "updated" event.
     */
    protected function saved(ServiceHistory $serviceHistory): void
    {
        // Jika tanggal service berikutnya diisi
        if ($serviceHistory->next_service_date) {
            // Cari atau buat pengingat 'service' di tabel tempos
            Tempo::updateOrCreate(
                [
                    'car_id' => $serviceHistory->car_id,
                    'perawatan' => 'service',
                ],
                [
                    'jatuh_tempo' => $serviceHistory->next_service_date,
                ]
            );
        }
    }

    public function created(ServiceHistory $serviceHistory): void
    {
        $this->saved($serviceHistory);
    }

    public function updated(ServiceHistory $serviceHistory): void
    {
        $this->saved($serviceHistory);
    }

    public function deleted(ServiceHistory $serviceHistory): void
    {
        // Jika riwayat service dihapus, hapus juga pengingatnya
        Tempo::where('car_id', $serviceHistory->car_id)
             ->where('perawatan', 'service')
             ->delete();
    }
}
