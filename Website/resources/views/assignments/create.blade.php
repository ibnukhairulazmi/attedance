@extends('layouts.main')

@section('title', 'Buat Tugas Baru — PresensiApp')
@section('header', 'Buat Tugas Baru')
@section('subheader', 'Atur judul, instruksi, tenggat waktu, dan format pengumpulan')

@section('content')
<style>
    .form-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 26px;
        max-width: 780px;
        box-shadow: var(--sh-sm);
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--txt);
        margin-bottom: 6px;
    }
    .form-hint {
        font-size: 11.5px;
        color: var(--txt-g);
        margin-top: 4px;
    }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: 9px;
        border: 1px solid var(--border);
        font-family: inherit;
        font-size: 13.5px;
        outline: none;
        transition: border-color .2s;
    }
    .form-control:focus {
        border-color: var(--teal);
    }
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .btn-row {
        display: flex;
        gap: 12px;
        margin-top: 28px;
    }
    .btn-submit {
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
    .btn-submit:hover {
        background: var(--teal-dk);
    }
    .btn-cancel {
        background: #fff;
        color: var(--txt-m);
        border: 1px solid var(--border);
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13.5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .btn-cancel:hover {
        background: var(--surf);
    }
</style>

<div class="form-card">
    <form action="{{ route('assignments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label class="form-label" for="jadwal_id">Mata Kuliah & Kelas *</label>
            <select name="jadwal_id" id="jadwal_id" class="form-control" required>
                <option value="">Pilih Mata Kuliah...</option>
                @foreach ($jadwals as $j)
                    <option value="{{ $j->id }}" {{ (old('jadwal_id', $defaultJadwalId) == $j->id) ? 'selected' : '' }}>
                        {{ $j->matakuliah->nama_mk ?? 'Mata Kuliah' }} — Kelas {{ $j->kelas->nama_kelas ?? 'Kelas' }} ({{ $j->hari }}, {{ $j->jam_mulai }} - {{ $j->jam_selesai }})
                    </option>
                @endforeach
            </select>
            @error('jadwal_id')
                <div style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="judul">Judul Tugas *</label>
            <input type="text" name="judul" id="judul" class="form-control" value="{{ old('judul') }}" placeholder="Contoh: Tugas 1: Desain Arsitektur Sistem" required>
            @error('judul')
                <div style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="deskripsi">Petunjuk & Deskripsi Tugas</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control" placeholder="Tuliskan petunjuk pengerjaan tugas di sini...">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <div style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="deadline">Batas Waktu (Deadline) *</label>
                <input type="datetime-local" name="deadline" id="deadline" class="form-control" value="{{ old('deadline') }}" required>
                @error('deadline')
                    <div style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="max_score">Nilai Maksimal *</label>
                <input type="number" name="max_score" id="max_score" class="form-control" value="{{ old('max_score', 100) }}" min="1" max="100" required>
                @error('max_score')
                    <div style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="tipe_pengumpulan">Format Pengumpulan *</label>
                <select name="tipe_pengumpulan" id="tipe_pengumpulan" class="form-control" required>
                    <option value="file" {{ old('tipe_pengumpulan') == 'file' ? 'selected' : '' }}>Upload File Dokumen (PDF, Word, Zip)</option>
                    <option value="link" {{ old('tipe_pengumpulan') == 'link' ? 'selected' : '' }}>Link Eksternal (GitHub, Google Drive, dsb)</option>
                    <option value="keduanya" {{ old('tipe_pengumpulan') == 'keduanya' ? 'selected' : '' }}>Fleksibel (File atau Link)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="file_petunjuk">File Lampiran / Soal (Opsional)</label>
                <input type="file" name="file_petunjuk" id="file_petunjuk" class="form-control" accept=".pdf,.doc,.docx,.zip,.rar,.ppt,.pptx,.xls,.xlsx">
                <div class="form-hint">Maksimal 10MB (PDF, DOCX, ZIP, PPT, XLS)</div>
                @error('file_petunjuk')
                    <div style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="btn-row">
            <button type="submit" class="btn-submit">Publikasikan Tugas</button>
            <a href="{{ route('assignments.index') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>
@endsection
