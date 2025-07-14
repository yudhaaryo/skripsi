<table border="1" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th colspan="12" align="center">
                SMK Negeri 3 Yogyakarta<br>
                LAPORAN BARANG PERSEDIAAN HABIS PAKAI<br>
                PROGRAM KEAHLIAN: {{ $program }}<br>
                BULAN: {{ $bulan }}
            </th>
        </tr>
        <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">Kode Barang</th>
            <th rowspan="2">Nama Barang<br><small><em>ASLI</em></small></th>
            <th rowspan="2">Nama Barang di Aplikasi</th>
            <th rowspan="2">Harga Beli (Rp)</th>
            <th rowspan="2">Jumlah Awal</th>
            <th colspan="2">Saldo Awal</th>
            <th colspan="2">Mutasi Bulan {{ $bulan }}</th>
            <th rowspan="2">SALDO AKHIR</th>
            <th rowspan="2">Jumlah (Rp)</th>
        </tr>
        <tr>
            <th> {{ $bulan }}</th>
            <th>Satuan</th>
            <th>Tambah</th>
            <th>Digunakan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($barangs as $i => $barang)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $barang['kode_barang'] }}</td>
                <td>{{ $barang['nama_asli'] }}</td>
                <td>{{ $barang['nama_aplikasi'] }}</td>
                <td>{{ $barang['harga_beli'] }}</td>
                <td>{{ $barang['jumlah_awal'] }}</td>
                <td>{{ $barang['saldo_awal'] }}</td>
                <td>{{ $barang['satuan'] }}</td>
                <td>{{ $barang['tambah'] }}</td>
                <td>{{ $barang['digunakan'] }}</td>
                <td>{{ $barang['saldo_akhir'] }}</td>
                <td>{{ $barang['jumlah_rupiah'] }}</td>
            </tr>
        @endforeach

        <!-- Total row -->
<tr style="font-weight: bold; background: #f3f4f6;">
    <td colspan="11" align="center">TOTAL</td>
    <td>{{ number_format($barangs->sum('jumlah_rupiah')) }}</td>
</tr>
</tbody>
</table>
<br><br>


<table style="width: 100%; border: none;">
    <tr>
        <td colspan="6" style="text-align: left;">
            Mengetahui,
        </td>
        <td colspan="6" style="text-align: right;">
            {{ 'Yogyakarta, ' . \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: left;">
            KPK, TJ, dan BP
        </td>
        <td colspan="6" style="text-align: right;">
            Kepala Bengkel TJ
        </td>
    </tr>
    <!-- Tambah baris kosong untuk ruang ttd -->
    <tr>
        <td colspan="6" style="height: 60px;">&nbsp;</td>
        <td colspan="6" style="height: 60px;">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: left;">
            Maryuli Darmawan S.Pd., M.Eng
        </td>
        <td colspan="6" style="text-align: right;">
            Agung Hari Wibowo S., ST
        </td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: left;">
            NIP. 19700720 199802 1 003
        </td>
        <td colspan="6" style="text-align: right;">
            NIP. 19770424 200604 1 011
        </td>
    </tr>
</table>







