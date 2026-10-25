<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Mahasiswa</title>
    <style>
        /* PERBAIKAN MARGIN PDF:
           Sebelumnya tidak ada aturan @page sama sekali, sehingga DomPDF
           memakai margin bawaan yang tidak konsisten. Aturan @media print
           di bawah (yang mengecilkan padding) JUGA TIDAK PERNAH terbaca
           oleh DomPDF — itu cuma berlaku saat preview manual di browser
           lewat window.print(), bukan saat generate PDF via kode. Akibatnya
           padding lama (40px 50px) dari versi preview tetap terpakai penuh
           dan berpotensi mendorong tabel sampai melebihi lebar kertas.
           Sekarang margin diatur SATU tempat lewat @page agar pasti
           konsisten dan tidak dobel dengan padding lain. */
        @page {
            size: A4 portrait;
            margin: 18mm 15mm 15mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
        }

        /* .page tidak lagi punya padding/max-width sendiri — batas kertas
           dan margin kanan-kiri-atas-bawah sepenuhnya diatur oleh @page
           di atas, supaya konten tidak pernah melebihi area cetak. */
        .page {
            width: 100%;
        }

        /* KOP SURAT */
        .kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .kop h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .kop h2 {
            font-size: 12pt;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .kop p {
            font-size: 11pt;
            font-weight: 700;
        }

        /* JUDUL DOKUMEN */
        .doc-title {
            text-align: center;
            margin: 18px 0 16px;
        }

        .doc-title h3 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: .5px;
        }

        /* INFO TABEL */
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }

        .info-table td {
            font-size: 11pt;
            padding: 2px 0;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 140px;
        }

        .info-table td:nth-child(2) {
            width: 12px;
        }

        /* TABEL DATA */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data th {
            border: 1px solid #000;
            padding: 7px 8px;
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            background: #ffffff;
            color: #000000;
        }

        table.data td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 11pt;
            vertical-align: middle;
        }

        table.data tbody tr:nth-child(even) {
            background: #f5f5f5;
        }

        .tc {
            text-align: center;
        }

        /* TTD */
        .ttd {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-box {
            text-align: center;
            min-width: 200px;
        }

        .ttd-box p {
            font-size: 11pt;
            margin-bottom: 2px;
        }

        .ttd-space {
            height: 56px;
        }

        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 11pt;
        }

        .ttd-role {
            font-size: 10pt;
        }

        /* FOOTER */
        .doc-footer {
            margin-top: 18px;
            border-top: 1px solid #000;
            padding-top: 6px;
            font-size: 9pt;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <div class="page">

        {{-- KOP --}}
        <div class="kop">
            <h1>Daftar Hadir Mahasiswa</h1>
            <h2>{{ $session->jadwal->matakuliah->nama_mk }}</h2>
            <p>Kelas {{ $session->jadwal->kelas->nama_kelas }}</p>
        </div>

        {{-- JUDUL --}}
        <div class="doc-title">
            <h3>Rekap Kehadiran Sesi Perkuliahan</h3>
        </div>

        {{-- INFO --}}
        <table class="info-table">
            <tr>
                <td>Mata Kuliah</td>
                <td>:</td>
                <td>{{ $session->jadwal->matakuliah->nama_mk }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td>{{ $session->jadwal->kelas->nama_kelas }}</td>
            </tr>
            <tr>
                <td>Hari / Tanggal</td>
                <td>:</td>
                <td>{{ $session->tanggal->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</td>
            </tr>
            <tr>
                <td>Jam</td>
                <td>:</td>
                <td>{{ substr($session->jadwal->jam_mulai, 0, 5) }} &ndash;
                    {{ substr($session->jadwal->jam_selesai, 0, 5) }} WIB</td>
            </tr>
            <tr>
                <td>Jumlah Mahasiswa</td>
                <td>:</td>
                <td>{{ $presensi->count() }} orang</td>
            </tr>
        </table>

        {{-- TABEL --}}
        <table class="data">
            <thead>
                <tr>
                    <th style="width:36px;">No</th>
                    <th style="width:110px;">NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th style="width:75px;">Status</th>
                    <th style="width:90px;">Waktu Scan</th>
                    <th style="width:65px;">Jarak</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($presensi as $i => $att)
                    <tr>
                        <td class="tc">{{ $i + 1 }}</td>
                        <td class="tc" style="font-family:monospace;font-size:10.5pt;">{{ $att->mahasiswa->nim }}
                        </td>
                        <td>{{ $att->mahasiswa->nama }}</td>
                        <td class="tc">{{ ucfirst($att->status) }}</td>
                        <td class="tc" style="font-family:monospace;font-size:10pt;">
                            {{ $att->scan_time ? $att->scan_time->format('H:i:s') : '-' }}</td>
                        <td class="tc">{{ $att->jarak_meter ? $att->jarak_meter . ' m' : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- TTD --}}
        <div class="ttd">
            <div class="ttd-box">
                <p>Dosen Pengampu,</p>
                <div class="ttd-space"></div>
                <div class="ttd-name">( _________________________ )</div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="doc-footer">
            <span>Dokumen ini dicetak secara otomatis oleh Sistem PresensiApp</span>
            <span>{{ now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB</span>
        </div>

    </div>
</body>

</html>
