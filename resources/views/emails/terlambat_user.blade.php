@component('mail::message')
# Pengingat Pengembalian Alat

Hai {{ $user->name }},<br>
Kamu belum mengembalikan alat yang kamu pinjam, berikut detailnya:

@foreach ($peminjamans as $item)
    - {{ $item->alat->nama_alat }} (Batas: {{ \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d M Y') }})
@endforeach

Harap segera dikembalikan agar tidak mengganggu peminjaman lain.

@endcomponent
