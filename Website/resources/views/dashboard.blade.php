@extends('layouts.main')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
    <style>
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: var(--sh-sm);
            transition: transform .2s, box-shadow .2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--sh-md);
        }

        .st-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .st-val {
            font-size: 26px;
            font-weight: 800;
            line-height: 1;
            color: var(--txt);
        }

        .st-lbl {
            font-size: 12px;
            color: var(--txt-g);
            margin-top: 4px;
        }

        .sec-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--txt);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pill {
            font-size: 11px;
            padding: 3px 9px;
            border-radius: 20px;
            font-weight: 600;
            background: rgba(8, 131, 149, .11);
            color: var(--teal-dk);
        }

        .cc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 14px;
        }

        .cc {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: 16px;
            padding: 18px;
            transition: all .2s;
            box-shadow: var(--sh-sm);
        }

        .cc:hover {
            border-color: var(--teal-lt);
            box-shadow: var(--sh-md);
            transform: translateY(-2px);
        }

        .cc-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 11px;
        }

        .cc-code {
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            color: var(--txt-g);
            background: var(--surf);
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid var(--border);
        }

        .s-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .s-badge.s-done {
            background: color-mix(in srgb, var(--txt-g) 15%, transparent);
            color: var(--txt-g);
        }

        .s-live {
            background: #d1fae5;
            color: var(--green);
        }

        .s-up {
            background: var(--surf2);
            color: var(--txt-g);
        }

        .live-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
            margin-right: 5px;
            animation: lp 1.4s infinite;
        }

        @keyframes lp {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .4;
                transform: scale(1.5);
            }
        }

        .cc-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--txt);
            margin-bottom: 9px;
        }

        .cc-meta {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 14px;
        }

        .cc-mr {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            color: var(--txt-g);
        }

        .cc-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid var(--border);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 15px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all .2s;
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

        .btn-out {
            background: var(--surf);
            border: 1.5px solid var(--border);
            color: var(--txt-g);
        }

        .btn-out:hover {
            background: var(--surf2);
            color: var(--txt);
            border-color: var(--teal-lt);
        }

        .btn-dis {
            background: var(--surf2);
            color: var(--txt);
            opacity: .55;
            border: 1px solid var(--border);
        }

        .empty-state {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 56px 24px;
            text-align: center;
            box-shadow: var(--sh-sm);
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

    {{-- STATS --}}
    <div class="stats">
        <div class="stat-card">
            <div class="st-icon" style="background:#dbeafe;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
            </div>
            <div>
                <div class="st-val">{{ $schedules->count() }}</div>
                <div class="st-lbl">Jadwal Hari Ini</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="st-icon" style="background:var(--gbg);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
            </div>
            <div>
                <div class="st-val">{{ $stats['active_sessions'] }}</div>
                <div class="st-lbl">Sesi Aktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="st-icon" style="background:var(--surf2);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
            </div>
            <div>
                <div class="st-val" style="font-size:18px;">{{ now()->format('H:i') }}</div>
                <div class="st-lbl">Waktu Sekarang</div>
            </div>
        </div>
    </div>

    {{-- JADWAL --}}
    <div class="sec-title">
        Jadwal Mengajar
        <span class="pill">{{ now()->locale('id')->isoFormat('dddd') }}</span>
    </div>

    @if ($schedules->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--teal-lt)"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
            </div>
            <h3>Tidak Ada Jadwal</h3>
            <p>Tidak ada jadwal mengajar untuk hari ini</p>
        </div>
    @else
        <div class="cc-grid">
            @foreach ($schedules as $jadwal)
                @php
                    $isActiveNow = $jadwal->isActiveNow();
                    $isFinished = $jadwal->isFinished();
                    $session = $jadwal->activeSession;
                    $todaySession = $jadwal->todaySession;
                    $isDone = $todaySession?->status === 'selesai' || $isFinished;
                @endphp
                <div class="cc">
                    <div class="cc-top">
                        <span class="cc-code">{{ $jadwal->matakuliah->kode_mk }}</span>

                        @if ($isDone)
                            <span class="s-badge s-done">Selesai</span>
                        @elseif($session)
                            <span class="s-badge s-live"><span class="live-dot"></span>Berlangsung</span>
                        @elseif($isActiveNow)
                            <span class="s-badge s-live"><span class="live-dot"></span>Aktif</span>
                        @else
                            <span class="s-badge s-up">Terjadwal</span>
                        @endif
                    </div>

                    <div class="cc-name">{{ $jadwal->matakuliah->nama_mk }}</div>

                    <div class="cc-meta">
                        <div class="cc-mr">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>
                            Kelas {{ $jadwal->kelas->nama_kelas }}
                        </div>
                        <div class="cc-mr">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            {{ substr($jadwal->jam_mulai, 0, 5) }} – {{ substr($jadwal->jam_selesai, 0, 5) }} WIB
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px dashed var(--surf2);">
                        <a href="{{ route('gradebook.index', ['jadwal_id' => $jadwal->id]) }}" style="font-size: 11.5px; font-weight: 600; color: var(--teal); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: var(--surf); padding: 4px 8px; border-radius: 6px; border: 1px solid var(--border);" title="Buka Buku Nilai tanpa perlu sesi aktif">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            Buku Nilai
                        </a>
                        <a href="{{ route('assignments.index', ['jadwal_id' => $jadwal->id]) }}" style="font-size: 11.5px; font-weight: 600; color: var(--blue); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: var(--surf); padding: 4px 8px; border-radius: 6px; border: 1px solid var(--border);">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Tugas Kelas
                        </a>
                    </div>
                    <div class="cc-foot">
                        @if ($isDone)
                            {{-- Jam sudah lewat atau sesi sudah ditutup --}}
                            @if ($todaySession)
                                <span style="font-size:12px;color:var(--txt-g);">
                                    <strong style="color:var(--teal-dk);">{{ $todaySession->presentCount() }}</strong>
                                    / {{ $todaySession->totalStudents() }} hadir
                                </span>
                                <a href="{{ route('sessions.report', $todaySession) }}" class="btn btn-out">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                        <line x1="16" y1="13" x2="8" y2="13" />
                                        <line x1="16" y1="17" x2="8" y2="17" />
                                    </svg>
                                    Lihat Laporan
                                </a>
                            @else
                                <span style="font-size:12px;color:var(--txt-lt);">Tidak ada sesi</span>
                                <span class="btn btn-dis">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2"
                                            ry="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    Terkunci
                                </span>
                            @endif
                        @elseif($session)
                            {{-- Sesi sedang berlangsung --}}
                            <span style="font-size:12px;color:var(--txt-g);">
                                <strong style="color:var(--teal-dk);">{{ $session->presentCount() }}</strong>
                                / {{ $session->totalStudents() }} hadir
                            </span>
                            <a href="{{ route('sessions.show', $session) }}" class="btn btn-teal">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                                </svg>
                                Monitor
                            </a>
                        @elseif($isActiveNow)
                            {{-- Jam aktif, belum ada sesi --}}
                            <span style="font-size:12px;color:var(--txt-g);">Siap dimulai</span>
                            <form method="POST" action="{{ route('sessions.start', $jadwal) }}">
                                @csrf
                                <button type="submit" class="btn btn-teal">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polygon points="5 3 19 12 5 21 5 3" />
                                    </svg>
                                    Mulai Presensi
                                </button>
                            </form>
                        @else
                            {{-- Di luar jam mengajar --}}
                            <span style="font-size:12px;color:var(--txt-lt);">Di luar jam mengajar</span>
                            <span class="btn btn-dis">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                Terkunci
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
