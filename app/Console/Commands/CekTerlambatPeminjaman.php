<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peminjaman;
use App\Notifications\TerlambatKembalikanNotification;
use Illuminate\Support\Carbon;

class CekTerlambatPeminjaman extends Command
{
    protected $signature = 'cek:terlambat';
    protected $description = 'Kirim notifikasi ke peminjam yang terlambat mengembalikan alat';

    public function handle()
    {
        $hariIni = Carbon::now()->startOfDay();

        $terlambat = Peminjaman::with('user')
            ->where('status_pinjam', 'dipinjam')
            ->whereDate('tanggal_kembali', '<', $hariIni)
            ->get();

        foreach ($terlambat as $pinjam) {
            if ($pinjam->user && $pinjam->user->email) {
                $pinjam->user->notify(new TerlambatKembalikanNotification($pinjam));
                $this->info("Notifikasi dikirim ke: " . $pinjam->user->email);
            }
        }

        return Command::SUCCESS;
    }
}