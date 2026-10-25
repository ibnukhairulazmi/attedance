@extends('layouts.main')
@section('title', 'Laporan Sesi')
@section('header', 'Laporan Presensi')
@section('subheader', $session->jadwal->matakuliah->nama_mk . ' ' . $session->jadwal->kelas->nama_kelas)

@section('content')
    <style>
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 22px;
        }

        .sum-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            box-shadow: var(--sh-sm);
        }

        .sum-val {
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 6px;
        }

        .sum-lbl {
            font-size: 12px;
            color: var(--txt-g);
        }

        .table-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--sh-sm);
            margin-bottom: 22px;
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
            text-align: left;
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
            font-weight: 600;
            color: var(--txt);
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

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: .2s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: none;
        }

        .btn-teal {
            background: var(--teal);
            color: #fff;
        }

        .btn-teal:hover {
            background: var(--teal-dk);
        }

        .info-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .info-item {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--sh-sm);
        }

        .info-item span {
            font-size: 13px;
            color: var(--txt-g);
        }

        .info-item strong {
            color: var(--txt);
        }
    </style>

    <div class="info-row">
        <div class="info-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
            </svg>
            <span>Mata Kuliah: <strong>{{ $session->jadwal->matakuliah->nama_mk }}</strong></span>
        </div>
        <div class="info-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
            </svg>
            <span>Kelas: <strong>{{ $session->jadwal->kelas->nama_kelas }}</strong></span>
        </div>
        <div class="info-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            <span>Tanggal: <strong>{{ $session->tanggal->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</strong></span>
        </div>
        <div class="info-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
            </svg>
            <span>Jam: <strong>{{ substr($session->jadwal->jam_mulai, 0, 5) }} –
                    {{ substr($session->jadwal->jam_selesai, 0, 5) }}</strong></span>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="summary-grid">
        <div class="sum-card">
            <div class="sum-val" style="color:var(--green)">{{ $summary->get('hadir', 0) }}</div>
            <div class="sum-lbl">Hadir</div>
        </div>
        <div class="sum-card">
            <div class="sum-val" style="color:var(--yellow)">{{ $summary->get('sakit', 0) }}</div>
            <div class="sum-lbl">Sakit</div>
        </div>
        <div class="sum-card">
            <div class="sum-val" style="color:var(--blue)">{{ $summary->get('izin', 0) }}</div>
            <div class="sum-lbl">Izin</div>
        </div>
        <div class="sum-card">
            <div class="sum-val" style="color:var(--red)">{{ $summary->get('alpha', 0) }}</div>
            <div class="sum-lbl">Alpha</div>
        </div>
    </div>

    {{-- TABEL DETAIL --}}
    <div class="table-card">
        <div class="tc-head">
            <h3>Detail Kehadiran Mahasiswa</h3>
            <span style="font-size:12px;color:var(--txt-g)">{{ $session->presensi->count() }} mahasiswa</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Status</th>
                    <th>Waktu Scan</th>
                    <th>Jarak</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($session->presensi->sortBy('mahasiswa.nim') as $i => $att)
                    @php
                        $badgeClass = 'bd-a';
                        if ($att->status === 'hadir') {
                            $badgeClass = 'bd-h';
                        } elseif ($att->status === 'izin') {
                            $badgeClass = 'bd-i';
                        } elseif ($att->status === 'sakit') {
                            $badgeClass = 'bd-s';
                        }
                    @endphp
                    <tr>
                        <td style="color:var(--txt-lt);font-size:12px;">{{ $i + 1 }}</td>
                        <td>
                            <div class="td-name">{{ $att->mahasiswa->nim }}</div>
                        </td>
                        <td>
                            <div class="td-name">{{ $att->mahasiswa->nama }}</div>
                        </td>
                        <td><span class="bd {{ $badgeClass }}">{{ ucfirst($att->status) }}</span></td>
                        <td style="font-family:'DM Mono',monospace;font-size:12px;">
                            {{ $att->scan_time ? $att->scan_time->format('H:i:s') : '—' }}
                        </td>
                        <td style="font-size:12px;color:var(--txt-g);">
                            {{ $att->jarak_meter ? $att->jarak_meter . 'm' : '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ACTIONS --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('dashboard') }}" class="btn btn-teal">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
            Kembali ke Dashboard
        </a>

        <div style="margin-left:auto;display:flex;gap:8px;flex-wrap:wrap;">
            <span style="font-size:12px;color:var(--txt-g);align-self:center;font-weight:600;">Ekspor:</span>

            <a href="{{ route('sessions.export', [$session, 'excel']) }}"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 15px;background:#1d6f42;color:#fff;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;transition:.2s;"
                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg>
                Excel
            </a>

            <a href="{{ route('sessions.export', [$session, 'pdf']) }}" target="_blank"
                style="display:inline-flex;align-items:center;gap:6px;padding:9px 15px;background:#ffffff;color:#000000;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;transition:.2s;"
                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg>
                PDF
            </a>
        </div>
    </div>
@endsection
