<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Kehadiran Mahasiswa</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm 15mm 15mm 15mm;
        }

        * {
            margin: 2mm 1mm 1.5mm 1mm;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
        }

        .page {
            width: 100%;
        }

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
            font-size: 11pt;
            font-weight: normal;
            margin-bottom: 2px;
        }

        .kop p {
            font-size: 10pt;
        }

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

        .info-table {
            width: 100%;
            margin-bottom: 18px;
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

        table.data {
            width: 95%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data th {
            border: 1px solid #000;
            padding: 7px 8px;
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            background: #000;
            color: #fff;
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

        .pct-txt {
            font-size: 10pt;
            font-weight: bold;
        }

        .ttd {
            margin-top: 28px;
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

        <div class="kop">
            <h1>Rekap Kehadiran Mahasiswa</h1>
            <h2>{{ $mataKuliah }}</h2>
            <p>Kelas {{ $kelas }}</p>
        </div>

        <div class="doc-title">
            <h3>Laporan Rekap Presensi Perkuliahan</h3>
        </div>

        <table class="info-table">
            <tr>
                <td>Mata Kuliah</td>
                <td>:</td>
                <td>{{ $mataKuliah }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td>{{ $kelas }}</td>
            </tr>
            <tr>
                <td>Hari</td>
                <td>:</td>
                <td>{{ $jadwal->hari }}</td>
            </tr>
            <tr>
                <td>Jam</td>
                <td>:</td>
                <td>{{ substr($jadwal->jam_mulai, 0, 5) }} &ndash; {{ substr($jadwal->jam_selesai, 0, 5) }} WIB</td>
            </tr>
            <tr>
                <td>Jumlah Mahasiswa</td>
                <td>:</td>
                <td>{{ count($report) }} orang</td>
            </tr>
        </table>

        <table class="data">
            <thead>
                <tr>
                    <th style="width:32px;">No</th>
                    <th style="width:55px;">NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th style="width:42px;">Hadir</th>
                    <th style="width:42px;">Sakit</th>
                    <th style="width:42px;">Izin</th>
                    <th style="width:42px;">Alpha</th>
                    <th style="width:45px;">Total</th>
                    <th style="width:78px;">Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report as $i => $row)
                    @php $pct = $row['persen']; @endphp
                    <tr>
                        <td class="tc">{{ $i + 1 }}</td>
                        <td class="tc">{{ $row['mahasiswa']->nim }}
                        </td>
                        <td>{{ $row['mahasiswa']->nama }}</td>
                        <td class="tc">{{ $row['hadir'] }}</td>
                        <td class="tc">{{ $row['sakit'] }}</td>
                        <td class="tc">{{ $row['izin'] }}</td>
                        <td class="tc">{{ $row['alpha'] }}</td>
                        <td class="tc" style="font-weight:bold;">{{ $row['total'] }}</td>
                        <td class="tc">
                            <span class="pct-txt">{{ $pct }}%</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ttd">
            <div class="ttd-box">
                <p>Dosen Pengampu,</p>
                <div class="ttd-space"></div>
                <div class="ttd-name">( _________________________ )</div>
            </div>
        </div>

        <div class="doc-footer">
            <span>Dokumen ini dicetak secara otomatis oleh Sistem PresensiApp</span>
            <span>{{ now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB</span>
        </div>

    </div>
</body>

</html>
