@extends('layouts.main')
@section('title', 'Laporan Presensi')
@section('header', 'Laporan Presensi')

@section('content')
    <style>
        .filter-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px 22px;
            margin-bottom: 22px;
            box-shadow: var(--sh-sm);
        }

        .filter-card h3 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 14px;
            color: var(--txt);
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: flex-end;
        }

        .fld {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .fld label {
            font-size: 12px;
            font-weight: 600;
            color: var(--txt-g);
        }

        .fld select {
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-size: 13px;
            padding: 8px 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--txt);
            background: var(--surf);
            transition: .15s;
            cursor: pointer;
        }

        .fld select:focus {
            outline: none;
            border-color: var(--teal);
        }

        .btn-teal {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            background: var(--teal);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: .2s;
        }

        .btn-teal:hover {
            background: var(--teal-dk);
        }

        .table-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--sh-sm);
        }

        .tc-head {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tc-head h3 {
            font-size: 14px;
            font-weight: 700;
            color: var(--txt);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: var(--surf);
            border-bottom: 1px solid var(--border);
        }

        th {
            padding: 10px 14px;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            color: var(--txt-g);
        }

        td {
            padding: 11px 14px;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
            color: var(--txt-m);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: var(--surf);
        }

        .td-name {
            text-align: center;
            font-weight: 600;
            color: var(--txt);
        }

        .td-nim {
            text-align: center;
            font-weight: 600;
            color: var(--txt);
            margin-top: 1px;
        }

        .bd {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .bd-h {
            background: var(--gbg);
            color: var(--green);
        }

        .bd-i {
            background: var(--bbg);
            color: var(--blue);
        }

        .bd-s {
            background: var(--ybg);
            color: var(--yellow);
        }

        .bd-a {
            background: var(--rbg);
            color: var(--red);
        }

        .prog-wrap {
            height: 6px;
            background: var(--surf2);
            border-radius: 100px;
            overflow: hidden;
            margin-bottom: 3px;
            width: 80px;
        }

        .prog-fill {
            height: 100%;
            border-radius: 100px;
        }

        .pct-green {
            background: linear-gradient(90deg, var(--teal), var(--teal-lt));
        }

        .pct-yellow {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .pct-red {
            background: linear-gradient(90deg, #dc2626, #f87171);
        }

        .empty-state {
            padding: 56px 24px;
            text-align: center;
        }

        .empty-icon {
            width: 56px;
            height: 56px;
            background: var(--surf2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .empty-state h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--txt);
            margin-bottom: 6px;
        }

        .empty-state p {
            font-size: 13px;
            color: var(--txt-g);
        }
    </style>

    {{-- FILTER --}}
    <div class="filter-card">
        <h3>Filter Laporan</h3>
        <form method="GET" class="filter-row">
            <div class="fld">
                <label>Kelas</label>
                <select name="jadwal_id" required>
                    <option value="">-- Pilih Kelas --</option>

                    @foreach ($jadwals as $jadwal)
                        <option value="{{ $jadwal->id }}" {{ request('jadwal_id') == $jadwal->id ? 'selected' : '' }}>

                            {{ $jadwal->kelas->nama_kelas }}
                            -
                            {{ $jadwal->matakuliah->nama_mk }}

                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-teal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                Tampilkan
            </button>
        </form>
    </div>

    {{-- TABEL --}}
    @if (count($report) > 0)
        <div class="table-card">
            <div class="tc-head">
                <h3>Rekap</h3>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:12px;color:var(--txt-g)">{{ count($report) }} mahasiswa</span>
                    <span style="font-size:12px;color:var(--txt-g);font-weight:600;">|</span>
                    <span style="font-size:12px;color:var(--txt-g);font-weight:600;">Ekspor:</span>
                    <a href="{{ route('reports.export', 'excel') . '?jadwal_id=' . $jadwalId }}"
                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#1d6f42;color:#fff;border-radius:7px;font-size:11px;font-weight:600;text-decoration:none;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="7 10 12 15 17 10" />
                            <line x1="12" y1="15" x2="12" y2="3" />
                        </svg>
                        Excel
                    </a>
                    <a href="{{ route('reports.export', 'pdf') . '?jadwal_id=' . $jadwalId }}" target="_blank"
                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#ffffff;color:#000000;border-radius:7px;font-size:11px;font-weight:600;text-decoration:none;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="7 10 12 15 17 10" />
                            <line x1="12" y1="15" x2="12" y2="3" />
                        </svg>
                        PDF
                    </a>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th class="center">Hadir</th>
                            <th class="center">Sakit</th>
                            <th class="center">Izin</th>
                            <th class="center">Alpha</th>
                            <th class="center">Total</th>
                            <th class="center">Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report as $i => $row)
                            @php $pct = $row['persen']; @endphp
                            <tr>
                                <td style="text-align:center;:var(--txt-lt);font-size:12px;">{{ $i + 1 }}</td>
                                <td>
                                    <div class="td-nim">{{ $row['mahasiswa']['nim'] }}</div>
                                </td>
                                <td>
                                    <div class="td-name">{{ $row['mahasiswa']['nama'] }}</div>
                                </td>
                                <td style="text-align:center;"><span class="bd bd-h">{{ $row['hadir'] }}</span></td>
                                <td style="text-align:center;"><span class="bd bd-s">{{ $row['sakit'] }}</span></td>
                                <td style="text-align:center;"><span class="bd bd-i">{{ $row['izin'] }}</span></td>
                                <td style="text-align:center;"><span class="bd bd-a">{{ $row['alpha'] }}</span></td>
                                <td style="text-align:center;font-weight:700;">{{ $row['total'] }}</td>
                                <td style="text-align:center;">
                                    <div class="prog-wrap" style="margin:0 auto 4px;">
                                        <div class="prog-fill {{ $pct >= 75 ? 'pct-green' : ($pct >= 50 ? 'pct-yellow' : 'pct-red') }}"
                                            style="width:{{ $pct }}%"></div>
                                    </div>
                                    <span
                                        style="font-size:12px;font-weight:700;color:{{ $pct >= 75 ? 'var(--green)' : ($pct >= 50 ? 'var(--yellow)' : 'var(--red)') }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($jadwal)
        <div class="table-card">
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--teal-lt)"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                    </svg>
                </div>
                <h3>Tidak Ada Data</h3>
                <p>Belum ada data presensi untuk periode yang dipilih</p>
            </div>
        </div>
    @endif
@endsection
