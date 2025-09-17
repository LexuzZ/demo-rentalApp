<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tempo;
use Carbon\Carbon;

class CheckCarStatusByTempo extends Command
{
    protected $signature = 'mobil:nonaktif-jatuh-tempo';
    protected $description = 'Nonaktifkan mobil yang sudah jatuh tempo';

    public function handle()
    {
        $today = Carbon::today();

        $tempos = Tempo::with('car')
            ->whereDate('jatuh_tempo', '<=', $today)
            ->get();

        foreach ($tempos as $tempo) {
            if ($tempo->car && $tempo->car->status !== 'nonaktif') {
                $tempo->car->update(['status' => 'nonaktif']);
                $this->info("Mobil {$tempo->car->nopol} berhasil di-nonaktifkan (jatuh tempo).");
            }
        }

        return Command::SUCCESS;
    }
}
