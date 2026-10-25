@extends('layouts.main')

@section('title', 'Manajemen Tugas — PresensiApp')
@section('header', 'Manajemen Tugas')
@section('subheader', 'Kelola penugasan perkuliahan dan evaluasi tugas mahasiswa')

@section('content')
<style>
    .asg-header {
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
    .btn-teal {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--teal);
        color: #fff;
        padding: 10px 18px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 13.5px;
        transition: background .2s;
        border: none;
        cursor: pointer;
    }
    .btn-teal:hover {
        background: var(--teal-dk);
    }
    .asg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }
    .asg-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--sh-sm);
        transition: transform .2s, box-shadow .2s;
    }
    .asg-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--sh-md);
    }
    .asg-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 12px;
    }
    .badge-active {
        background: var(--gbg);
        color: var(--green);
        border: 1px solid #a7f3d0;
    }
    .badge-past {
        background: var(--rbg);
        color: var(--red);
        border: 1px solid #fecaca;
    }
    .asg-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--txt);
        margin-bottom: 6px;
        line-height: 1.4;
    }
    .asg-course {
        font-size: 12.5px;
        color: var(--teal-dk);
        font-weight: 600;
        margin-bottom: 12px;
    }
    .asg-meta {
        font-size: 12px;
        color: var(--txt-g);
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .asg-meta-row {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .asg-footer {
        padding-top: 14px;
        border-top: 1px solid var(--surf2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .btn-detail {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--teal);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-detail:hover {
        color: var(--teal-dk);
        text-decoration: underline;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 16px;
        border: 1px dashed var(--border);
        grid-column: 1 / -1;
    }
    .empty-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 12px;
        stroke: var(--txt-lt);
    }
</style>

<div class="asg-header">
    <form method="GET" action="{{ route('assignments.index') }}" style="display: flex; align-items: center; gap: 10px;">
        <label for="jadwal_id" style="font-size: 13px; font-weight: 600; color: var(--txt-g);">Filter Mata Kuliah:</label>
        <select name="jadwal_id" id="jadwal_id" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua Mata Kuliah / Kelas</option>
            @foreach ($jadwals as $j)
                <option value="{{ $j->id }}" {{ $selectedJadwalId == $j->id ? 'selected' : '' }}>
                    {{ $j->matakuliah->nama_mk ?? 'Mata Kuliah' }} ({{ $j->kelas->nama_kelas ?? 'Kelas' }}) — {{ $j->hari }}
                </option>
            @endforeach
        </select>
    </form>

    <a href="{{ route('assignments.create', ['jadwal_id' => $selectedJadwalId]) }}" class="btn-teal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Buat Tugas Baru
    </a>
</div>

<div class="asg-grid">
    @forelse ($assignments as $tugas)
        <div class="asg-card">
            <div>
                @if ($tugas->isPastDeadline())
                    <span class="asg-badge badge-past">Batas Waktu Berakhir</span>
                @else
                    <span class="asg-badge badge-active">Sedang Berlangsung</span>
                @endif

                <h3 class="asg-title">{{ $tugas->judul }}</h3>
                <div class="asg-course">
                    {{ $tugas->jadwal->matakuliah->nama_mk ?? 'Mata Kuliah' }} &bull; Kelas {{ $tugas->jadwal->kelas->nama_kelas ?? 'Kelas' }}
                </div>

                <div class="asg-meta">
                    <div class="asg-meta-row">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>Deadline: <strong>{{ $tugas->deadline->translatedFormat('d M Y, H:i') }} WIB</strong></span>
                    </div>
                    <div class="asg-meta-row">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>Dikumpulkan: <strong>{{ $tugas->pengumpulan_count }} mahasiswa</strong></span>
                    </div>
                    <div class="asg-meta-row">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="4" y1="9" x2="20" y2="9"></line>
                            <line x1="4" y1="15" x2="20" y2="15"></line>
                            <line x1="10" y1="3" x2="8" y2="21"></line>
                            <line x1="16" y1="3" x2="14" y2="21"></line>
                        </svg>
                        <span>Tipe: <strong>{{ ucfirst($tugas->tipe_pengumpulan) }}</strong> (Maks: {{ $tugas->max_score }})</span>
                    </div>
                </div>
            </div>

            <div class="asg-footer">
                <a href="{{ route('assignments.show', $tugas) }}" class="btn-detail">
                    Periksa & Beri Nilai &rarr;
                </a>

                <form action="{{ route('assignments.destroy', $tugas) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini? Data pengumpulan juga akan terhapus.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; cursor: pointer; color: var(--red); opacity: .7; transition: opacity .2s;" title="Hapus Tugas">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="12" y1="18" x2="12" y2="12"></line>
                <line x1="9" y1="15" x2="15" y2="15"></line>
            </svg>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--txt-m); margin-bottom: 6px;">Belum Ada Tugas Perkuliahan</h3>
            <p style="font-size: 13px; color: var(--txt-g); margin-bottom: 18px;">Buat tugas pertama Anda untuk mempermudah evaluasi capaian belajar mahasiswa.</p>
            <a href="{{ route('assignments.create') }}" class="btn-teal">Buat Tugas Baru</a>
        </div>
    @endforelse
</div>
@endsection
