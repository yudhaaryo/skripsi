<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Peminjaman</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 40px;
        }

        .underline {
            text-decoration: underline;
        }

        table.border,
        table.border th,
        table.border td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 5px;
            font-size: 14px;
        }

        .signature-section {
            width: 100%;
            margin-top: 60px;
            font-size: 14px;
        }

        .signature-section td {
            vertical-align: top;
            text-align: center;
            height: 100px;
        }
    </style>
</head>

<body>

    {{-- KOP SURAT --}}
    <table style="width: 100%;">
        <tr>
            {{-- Logo DIY kiri --}}
            <td style="width: 15%; text-align: left;">
                <img src="{{ public_path('img/logodiy.png') }}" alt="Logo DIY" style="width: 80px; height: auto;">
            </td>

            {{-- Teks tengah --}}
            <td style="width: 70%; text-align: center;">
                <h4 style="margin: 0;">PEMERINTAH DAERAH ISTIMEWA YOGYAKARTA</h4>
                <h4 style="margin: 0;">DINAS PENDIDIKAN, PEMUDA, DAN OLAHRAGA</h4>
                <h4 style="margin: 0;">BALAI PENDIDIKAN MENENGAH KOTA YOGYAKARTA</h4>
                <h2 style="margin: 6px 0;">SMKN 3 YOGYAKARTA</h2>
                <p style="font-size: 12px; margin: 4px 0;">ꦱꦩ꧀ꦏ꧀ ꧓ ꦪꦺꦴꦒ꧀ꦪꦏꦂꦠ</p>
                <p style="font-size: 12px; margin: 2px 0;">
                    Jalan RW. Monginsidi No. 2 Yogyakarta, Kode Pos 55233, Telp. (0274) 513503
                </p>
                <p style="font-size: 12px; margin: 0;">
                    Pos-el:humas@smkn3jogja.sch.id Laman:http://smkn3jogja.sch.id
                </p>
            </td>

            {{-- Logo SMK kanan --}}
            <td style="width: 15%; text-align: right;">
                <img src="{{ public_path('img/logosmk3yk.png') }}" alt="Logo SMK" style="width: 80px; height: auto;">
            </td>
        </tr>
    </table>


    <hr style="border: 1px solid black; margin: 10px 0 24px;">

    <h3 style="text-align: center; text-decoration: underline; margin-bottom: 20px;">
        FORMULIR PEMINJAMAN SARANA PRASARANA
    </h3>

    {{-- ISI SURAT --}}
    <p>Disampaikan dengan hormat, kepada pengelola barang milik sekolah bahwa saya:</p>
    <p>Nama: <span class="underline">{{ $peminjaman->user->name ?? '-' }}</span></p>
    <p>NIS: <span class="underline">{{ $peminjaman->nis_peminjam }}</span></p>
    <p>Kelas: <span class="underline">{{ $peminjaman->kelas_peminjam }}</span></p>
    <p>Mohon pinjam barang/peralatan seperti tertulis di bawah ini untuk keperluan <span
            class="underline">{{ $peminjaman->keperluan }}</span></p>
    <p>Dan akan kami kembalikan pada hari <span
            class="underline">{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('l, d F Y') }}</span>
    </p>
    <p>Demikian permohonan pinjam kami buat, atas perhatiannya diucapkan terima kasih.</p>

    <br>

    {{-- TABEL BARANG --}}
    <table class="border" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Barang & Spesifikasi</th>
                <th>Jumlah</th>
                <th>Kondisi Alat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($peminjaman->alats as $index => $alat)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $alat->nama_alat ?? '-' }}</td>
                    <td>{{ $alat->pivot->jumlah_pinjam ?? '-' }}</td>
                    <td>{{ $alat->pivot->kondisi_peminjaman ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    {{-- TANDA TANGAN --}}
    <table class="signature-section">
        <tr>
            <td>
                KPK TKJ & MM<br><br><br>
                <strong>Maryuli Darmawan, S.Pd., M.Eng</strong><br>
                NIP. 197007201998011003
            </td>
            <td>
                KABENG / Guru Pengampu<br><br><br> <br>
                .........................................
            </td>
            <td>
                Yogyakarta, {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}<br>
                Yang Meminjam<br><br><br>
                .........................................
            </td>
        </tr>
    </table>

</body>

</html>
