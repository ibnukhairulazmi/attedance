@extends('layouts.main')

@section('title', 'Buku Nilai (Gradebook) — PresensiApp')
@section('header', 'Buku Nilai (Gradebook)')
@section('subheader', 'Rekapitulasi otomatis presensi, tugas, input UTS/UAS, dan konversi nilai akhir')

@section('content')
<style>
    .gb-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }
    .filter-select {
        padding: 9px 16px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: #fff;
        font-family: inherit;
        font-size: 13.5px;
        color: var(--txt);
        outline: none;
        cursor: pointer;
    }
    .weight-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 18px 22px;
        margin-bottom: 24px;
        box-shadow: var(--sh-sm);
    }
    .weight-form {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 12px;
    }
    .weight-input-group {
        display: flex;
        align-items: center;
        gap: 6px;
        background: var(--surf);
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid var(--surf2);
    }
    .weight-input-group label {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--txt-m);
    }
    .weight-input-group input {
        width: 48px;
        text-align: center;
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 4px;
        font-weight: 700;
        outline: none;
    }
    .btn-save-weight {
        background: var(--teal);
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-save-weight:hover {
        background: var(--teal-dk);
    }
    .table-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: var(--sh-sm);
    }
    .table-head {
        padding: 16px 22px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--surf);
    }
    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }
    .custom-table th {
        background: #f8fafc;
        padding: 12px 14px;
        font-weight: 700;
        color: var(--txt-m);
        border-bottom: 1px solid var(--border);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .custom-table td {
        padding: 12px 14px;
        border-bottom: 1px solid var(--surf2);
        vertical-align: middle;
    }
    .custom-table tr:hover {
        background: var(--surf);
    }
    .score-input {
        width: 65px;
        padding: 6px;
        text-align: center;
        border-radius: 6px;
        border: 1px solid var(--border);
        font-family: inherit;
        font-weight: 600;
        font-size: 13px;
        outline: none;
    }
    .score-input:focus {
        border-color: var(--teal);
    }
    .badge-grade {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 800;
        text-align: center;
    }
    .grade-A { background: #d1fae5; color: #047857; }
    .grade-B { background: #dbeafe; color: #1d4ed8; }
    .grade-C { background: #fef3c7; color: #b45309; }
    .grade-D { background: #ffedd5; color: #c2410c; }
    .grade-E { background: #fee2e2; color: #b91c1c; }
    .btn-save-all {
        background: var(--teal);
        color: #fff;
        padding: 10px 22px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13.5px;
        border: none;
        cursor: pointer;
        transition: background .2s;
    }
    .btn-save-all:hover {
        background: var(--teal-dk);
    }
</style>

<div class="gb-header">
    <form method="GET" action="{{ route('gradebook.index') }}" style="display: flex; align-items: center; gap: 10px;">
        <label for="jadwal_id" style="font-size: 13px; font-weight: 600; color: var(--txt-g);">Pilih Jadwal Perkuliahan:</label>
        <select name="jadwal_id" id="jadwal_id" class="filter-select" onchange="this.form.submit()">
            @foreach ($jadwals as $j)
                <option value="{{ $j->id }}" {{ $selectedJadwalId == $j->id ? 'selected' : '' }}>
                    {{ $j->matakuliah->nama_mk ?? $j->matakuliah->nama ?? 'Mata Kuliah' }} ({{ $j->kelas->nama_kelas ?? $j->kelas->nama ?? 'Kelas' }}) — {{ $j->hari }}
                </option>
            @endforeach
        </select>
    </form>
</div>

@if ($selectedJadwal)
    <!-- Pengaturan Bobot Penilaian -->
    <div class="weight-card">
        <div style="font-size: 14px; font-weight: 700; color: var(--txt);">
            ⚙️ Bobot Komponen Penilaian (Harus Total 100%)
        </div>
        <form action="{{ route('gradebook.weights', $selectedJadwal) }}" method="POST" class="weight-form">
            @csrf
            <div class="weight-input-group">
                <label>Presensi:</label>
                <input type="number" name="bobot_presensi" value="{{ $bobot->bobot_presensi }}" min="0" max="100" required>%
            </div>
            <div class="weight-input-group">
                <label>Tugas:</label>
                <input type="number" name="bobot_tugas" value="{{ $bobot->bobot_tugas }}" min="0" max="100" required>%
            </div>
            <div class="weight-input-group">
                <label>UTS:</label>
                <input type="number" name="bobot_uts" value="{{ $bobot->bobot_uts }}" min="0" max="100" required>%
            </div>
            <div class="weight-input-group">
                <label>UAS:</label>
                <input type="number" name="bobot_uas" value="{{ $bobot->bobot_uas }}" min="0" max="100" required>%
            </div>
            <button type="submit" class="btn-save-weight">Perbarui Bobot</button>
        </form>
    </div>

    <!-- Tabel Rekap Nilai -->
    <div class="table-card">
        <form action="{{ route('gradebook.scores', $selectedJadwal) }}" method="POST">
            @csrf

            <div class="table-head">
                <div>
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--txt);">
                        Daftar Nilai Mahasiswa — {{ $selectedJadwal->matakuliah->nama_mk ?? '' }} (Kelas {{ $selectedJadwal->kelas->nama_kelas ?? '' }})
                    </h3>
                    <span style="font-size: 12px; color: var(--txt-g);">
                        Presensi & Tugas dihitung secara realtime dari aktivitas di sistem
                    </span>
                </div>
                <button type="submit" class="btn-save-all">Simpan Nilai UTS & UAS</button>
            </div>

            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Mahasiswa</th>
                        <th style="text-align: center;">Presensi ({{ $bobot->bobot_presensi }}%)</th>
                        <th style="text-align: center;">Tugas ({{ $bobot->bobot_tugas }}%)</th>
                        <th style="text-align: center;">UTS ({{ $bobot->bobot_uts }}%)</th>
                        <th style="text-align: center;">UAS ({{ $bobot->bobot_uas }}%)</th>
                        <th style="text-align: center;">Nilai Akhir</th>
                        <th style="text-align: center; width: 80px;">Huruf</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gradeData as $idx => $item)
                        @php
                            $mhs = $item['mahasiswa'];
                            $gradeClass = 'grade-' . substr($item['nilai_huruf'], 0, 1);
                        @endphp
                        <tr>
                            <td style="color: var(--txt-g);">{{ $idx + 1 }}</td>
                            <td>
                                <div style="font-weight: 700; color: var(--txt);">{{ $mhs->nama }}</div>
                                <div style="font-size: 11px; color: var(--txt-lt); font-family: 'DM Mono', monospace;">{{ $mhs->nim }}</div>
                            </td>
                            <td style="text-align: center;">
                                <div style="font-weight: 700; color: var(--teal-dk);">{{ $item['nilai_presensi'] }}</div>
                                <div style="font-size: 10.5px; color: var(--txt-lt);">{{ $item['hadir_count'] }}/{{ $item['total_sesi'] }} Hadir</div>
                            </td>
                            <td style="text-align: center;">
                                <div style="font-weight: 700; color: var(--blue);">{{ $item['nilai_tugas'] }}</div>
                                <div style="font-size: 10.5px; color: var(--txt-lt);">{{ $item['tugas_selesai'] }}/{{ $item['total_tugas'] }} Selesai</div>
                            </td>
                            <td style="text-align: center;">
                                <input type="number" step="0.5" min="0" max="100" name="scores[{{ $mhs->id }}][uts]" value="{{ $item['nilai_uts'] }}" class="score-input" placeholder="0-100">
                            </td>
                            <td style="text-align: center;">
                                <input type="number" step="0.5" min="0" max="100" name="scores[{{ $mhs->id }}][uas]" value="{{ $item['nilai_uas'] }}" class="score-input" placeholder="0-100">
                            </td>
                            <td style="text-align: center;">
                                <div style="font-size: 15px; font-weight: 800; color: var(--txt);">{{ $item['nilai_akhir'] }}</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-grade {{ $gradeClass }}">{{ $item['nilai_huruf'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--txt-lt); padding: 30px;">
                                Belum ada mahasiswa di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>
@else
    <div style="background: #fff; padding: 40px; border-radius: 14px; text-align: center; border: 1px dashed var(--border);">
        <p style="color: var(--txt-g); font-size: 14px;">Belum ada jadwal mengajar yang terdaftar untuk akun Dosen Anda.</p>
    </div>
@endif
@endsection
