@extends('layouts.main')

@section('title', 'Evaluasi Tugas — ' . $assignment->judul)
@section('header', 'Evaluasi Tugas')
@section('subheader', $assignment->judul . ' • ' . ($assignment->jadwal->matakuliah->nama ?? ''))

@section('content')
<style>
    .show-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .info-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 22px;
        box-shadow: var(--sh-sm);
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid var(--surf2);
    }
    .info-stat {
        display: flex;
        flex-direction: column;
    }
    .stat-label {
        font-size: 11.5px;
        color: var(--txt-g);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .stat-val {
        font-size: 15px;
        font-weight: 700;
        color: var(--txt);
        margin-top: 3px;
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
        font-size: 13.5px;
        text-align: left;
    }
    .custom-table th {
        background: #f8fafc;
        padding: 12px 18px;
        font-weight: 700;
        color: var(--txt-m);
        border-bottom: 1px solid var(--border);
        font-size: 12.5px;
    }
    .custom-table td {
        padding: 14px 18px;
        border-bottom: 1px solid var(--surf2);
        vertical-align: middle;
    }
    .custom-table tr:hover {
        background: var(--surf);
    }
    .badge-status {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
    }
    .status-ontime {
        background: var(--gbg);
        color: var(--green);
    }
    .status-late {
        background: var(--ybg);
        color: var(--yellow);
    }
    .status-none {
        background: #f3f4f6;
        color: var(--txt-lt);
    }
    .grade-form {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .input-grade {
        width: 70px;
        padding: 6px 8px;
        border-radius: 8px;
        border: 1px solid var(--border);
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        text-align: center;
    }
    .btn-grade {
        background: var(--teal);
        color: #fff;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
    }
    .btn-grade:hover {
        background: var(--teal-dk);
    }
</style>

<div class="show-container">
    <div style="margin-bottom: -10px;">
        <a href="{{ route('assignments.index') }}" style="color: var(--teal); text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
            &larr; Kembali ke Daftar Tugas
        </a>
    </div>

    <!-- Info Detail Tugas -->
    <div class="info-card">
        <h2 style="font-size: 18px; font-weight: 800; color: var(--txt); margin-bottom: 8px;">{{ $assignment->judul }}</h2>
        <div style="font-size: 13.5px; color: var(--txt-m); line-height: 1.6; margin-bottom: 12px;">
            {{ $assignment->deskripsi ?? 'Tidak ada petunjuk tambahan.' }}
        </div>

        @if ($assignment->file_petunjuk)
            <div style="margin-top: 10px;">
                <a href="{{ asset('storage/' . $assignment->file_petunjuk) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: var(--surf2); border-radius: 8px; font-size: 12.5px; color: var(--teal-dk); text-decoration: none; font-weight: 600;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Unduh Lampiran Soal / Petunjuk
                </a>
            </div>
        @endif

        <div class="info-grid">
            <div class="info-stat">
                <span class="stat-label">Mata Kuliah & Kelas</span>
                <span class="stat-val">{{ $assignment->jadwal->matakuliah->nama_mk ?? '-' }} (Kelas {{ $assignment->jadwal->kelas->nama_kelas ?? '-' }})</span>
            </div>
            <div class="info-stat">
                <span class="stat-label">Tenggat Waktu</span>
                <span class="stat-val">{{ $assignment->deadline->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
            <div class="info-stat">
                <span class="stat-label">Pengumpulan</span>
                <span class="stat-val">{{ $assignment->pengumpulan->count() }} / {{ $allMahasiswa->count() }} Mahasiswa</span>
            </div>
            <div class="info-stat">
                <span class="stat-label">Skor Maksimal</span>
                <span class="stat-val">{{ $assignment->max_score }} Poin</span>
            </div>
        </div>
    </div>

    <!-- Tabel Pengumpulan Mahasiswa -->
    <div class="table-card">
        <div class="table-head">
            <h3 style="font-size: 15px; font-weight: 700; color: var(--txt);">Daftar Pengumpulan Mahasiswa</h3>
            <span style="font-size: 12.5px; color: var(--txt-g);">Total {{ $allMahasiswa->count() }} Mahasiswa di Kelas</span>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>NIM & Nama Mahasiswa</th>
                    <th>Waktu Kumpul</th>
                    <th>Berkas / Link Tugas</th>
                    <th>Nilai (Max {{ $assignment->max_score }})</th>
                    <th>Catatan / Feedback</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($allMahasiswa as $index => $mhs)
                    @php
                        $sub = $submissionsByMhs->get($mhs->id);
                    @endphp
                    <tr>
                        <td style="color: var(--txt-g); font-size: 12.5px;">{{ $index + 1 }}</td>
                        <td>
                            <div style="font-weight: 700; color: var(--txt);">{{ $mhs->nama }}</div>
                            <div style="font-size: 11.5px; color: var(--txt-lt); font-family: 'DM Mono', monospace;">{{ $mhs->nim }}</div>
                        </td>
                        <td>
                            @if ($sub)
                                <span class="badge-status {{ $sub->status == 'tepat_waktu' ? 'status-ontime' : 'status-late' }}">
                                    {{ $sub->status == 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }}
                                </span>
                                <div style="font-size: 11px; color: var(--txt-g); margin-top: 3px;">
                                    {{ $sub->waktu_kumpul->translatedFormat('d/m/Y H:i') }}
                                </div>
                            @else
                                <span class="badge-status status-none">Belum Mengumpulkan</span>
                            @endif
                        </td>
                        <td>
                            @if ($sub)
                                @if ($sub->file_path)
                                    <div>
                                        <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" style="color: var(--teal); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                            Unduh File
                                        </a>
                                    </div>
                                @endif
                                @if ($sub->link_url)
                                    <div>
                                        <a href="{{ $sub->link_url }}" target="_blank" rel="noopener noreferrer" style="color: var(--blue); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                            Buka Link Tugas
                                        </a>
                                    </div>
                                @endif
                                @if ($sub->catatan_mahasiswa)
                                    <div style="font-size: 11.5px; color: var(--txt-g); margin-top: 4px; font-style: italic;">
                                        "{{ $sub->catatan_mahasiswa }}"
                                    </div>
                                @endif
                            @else
                                <span style="color: var(--txt-lt); font-size: 12px;">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($sub)
                                <form action="{{ route('assignments.grade', $sub) }}" method="POST" class="grade-form">
                                    @csrf
                                    <input type="number" step="0.5" name="nilai" class="input-grade" value="{{ $sub->nilai }}" min="0" max="{{ $assignment->max_score }}" placeholder="0-{{ $assignment->max_score }}" required>
                                    <button type="submit" class="btn-grade">Simpan</button>
                                </form>
                            @else
                                <span style="color: var(--txt-lt); font-size: 12px;">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($sub)
                                <div style="font-size: 12px; color: var(--txt-m);">
                                    {{ $sub->catatan_dosen ?? '—' }}
                                </div>
                            @else
                                <span style="color: var(--txt-lt); font-size: 12px;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--txt-lt); padding: 30px;">
                            Tidak ada mahasiswa terdaftar di kelas ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
