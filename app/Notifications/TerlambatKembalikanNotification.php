<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Peminjaman;

class TerlambatKembalikanNotification extends Notification
{
    use Queueable;

    public $peminjaman;

    public function __construct(Peminjaman $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pengembalian Alat Terlambat')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Pengembalian alat yang Anda pinjam telah melewati batas waktu.')
            ->line('Tanggal seharusnya dikembalikan: ' . \Carbon\Carbon::parse($this->peminjaman->tanggal_kembali)->translatedFormat('l, d F Y'))
            ->line('Mohon segera mengembalikan alat tersebut ke petugas.')
            ->action('Lihat Detail Peminjaman', url('/admin/peminjaman')) // sesuaikan route jika perlu
            ->line('Terima kasih.');
    }
}