@extends('layouts.main')
@section('title', 'Monitor Presensi')
@section('header', 'Monitor Presensi')
@section('subheader', $session->jadwal->matakuliah->nama_mk . ' — Kelas ' . $session->jadwal->kelas->nama_kelas)

@section('content')
    <style>
        .min-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 10px 16px;
            margin-bottom: 16px;
            box-shadow: var(--sh-sm);
        }

        .min-bar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--green);
            animation: lp 1.4s infinite;
            flex-shrink: 0;
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

        .min-bar-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--txt);
        }

        .min-bar-sub {
            font-size: 11px;
            color: var(--txt-g);
            margin-top: 1px;
        }

        .min-bar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .min-count {
            font-family: 'DM Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: var(--teal-dk);
            background: var(--surf2);
            border: 1px solid var(--border);
            padding: 4px 10px;
            border-radius: 8px;
        }

        .btn-min-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid var(--border);
            background: var(--surf);
            color: var(--txt-g);
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: .15s;
            text-decoration: none;
        }

        .btn-min-action:hover {
            background: var(--surf2);
            border-color: var(--teal-lt);
            color: var(--teal-dk);
        }

        .pres-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 16px;
            height: calc(100vh - 215px);
        }

        .pane {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: var(--sh-sm);
        }

        .qr-head {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .qr-head h3 {
            font-size: 14px;
            font-weight: 700;
        }

        .s-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .s-live {
            background: #d1fae5;
            color: #059669;
        }

        .s-done {
            background: var(--rbg);
            color: var(--red);
        }

        .qr-body {
            padding: 16px;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .qr-frame {
            background: #fff;
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-frame svg {
            width: 100%;
            max-width: 220px;
            height: auto;
        }

        .qr-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .qm-item {
            background: var(--surf);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 9px 10px;
            text-align: center;
        }

        .qm-lbl {
            font-size: 10px;
            color: var(--txt-lt);
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 2px;
        }

        .qm-val {
            font-size: 12px;
            font-weight: 700;
            color: var(--txt);
        }

        .qr-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: auto;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 15px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all .2s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            width: 100%;
            text-decoration: none;
        }

        .btn-teal {
            background: var(--teal);
            color: #fff;
        }

        .btn-teal:hover {
            background: var(--teal-dk);
        }

        .btn-red {
            background: var(--red);
            color: #fff;
        }

        .btn-red:hover {
            background: #b91c1c;
        }

        .btn-out {
            background: var(--surf);
            border: 1.5px solid var(--border);
            color: var(--txt-g);
        }

        .btn-out:hover {
            background: var(--surf2);
            border-color: var(--teal-lt);
            color: var(--txt);
        }

        .stu-top {
            padding: 12px 18px;
            background: var(--surf);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .att-summary {
            display: flex;
            gap: 20px;
        }

        .as-item {
            text-align: center;
        }

        .as-v {
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
        }

        .as-l {
            font-size: 10px;
            color: var(--txt-g);
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .prog-wrap {
            height: 4px;
            background: var(--surf2);
            border-radius: 100px;
            overflow: hidden;
            margin-top: 8px;
        }

        .prog-fill {
            height: 100%;
            border-radius: 100px;
            background: linear-gradient(90deg, var(--teal), var(--teal-lt));
            transition: width .5s ease;
        }

        .filter-bar {
            padding: 10px 18px;
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 6px;
            background: var(--white);
            flex-shrink: 0;
        }

        .flt {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid var(--border);
            background: var(--surf);
            color: var(--txt-g);
            transition: .15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .flt:hover {
            border-color: var(--teal-lt);
            color: var(--teal-dk);
        }

        .flt.on {
            border-color: var(--teal);
            background: rgba(8, 131, 149, .08);
            color: var(--teal-dk);
        }

        .stu-head {
            padding: 9px 18px;
            background: var(--surf);
            border-bottom: 1px solid var(--border);
            display: grid;
            grid-template-columns: 36px 200px 1fr 320px;
            gap: 8px;
            align-items: center;
            font-size: 12px;
            font-weight: 1000;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--txt);
            flex-shrink: 0;
        }

        .stu-scroll {
            flex: 1;
            overflow-y: auto;
        }

        .stu-row {
            padding: 10px 18px;
            border-bottom: 1px solid var(--border);
            display: grid;
            grid-template-columns: 36px 200px 1fr 320px;
            gap: 8px;
            align-items: center;
            transition: background .15s;
            text-align: center;
        }

        .stu-row:last-child {
            border-bottom: none;
        }

        .stu-row:hover {
            background: var(--surf);
        }

        .stu-num {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: var(--surf2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            color: var(--txt-m);
        }

        .stu-nim {
            font-size: 12px;
            font-weight: 600;
            color: var(--txt);
        }

        .stu-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--txt);
            text-align: left;
        }

        .stu-scan {
            font-size: 10px;
            color: var(--txt-lt);
            font-family: 'DM Mono', monospace;
            margin-top: 1px;
        }

        .ov-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ov-group input[type="radio"] {
            display: none;
        }

        .ov-group label {
            padding: 4px 9px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            color: var(--txt-g);
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: .12s;
            user-select: none;
            border: 1px solid transparent;
        }

        .ov-group label:hover {
            background: var(--surf2);
            color: var(--txt);
        }

        .ov-group input[value="hadir"]:checked+label {
            color: var(--red);
            font-weight: 700;
        }

        .ov-group input[value="izin"]:checked+label {
            color: var(--blue);
            font-weight: 700;
        }

        .ov-group input[value="sakit"]:checked+label {
            color: var(--yellow);
            font-weight: 700;
        }

        .ov-group input[value="alpha"]:checked+label {
            color: var(--red);
            font-weight: 700;
        }

        .ov-group input[type="radio"]:checked+label::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            margin-right: 4px;
            vertical-align: middle;
            margin-top: -1px;
        }

        .ov-group label::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            border: 1.5px solid var(--border);
            margin-right: 4px;
            vertical-align: middle;
            margin-top: -1px;
        }

        .stu-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .stu-scroll::-webkit-scrollbar-thumb {
            background: rgba(8, 131, 149, .2);
            border-radius: 10px;
        }
    </style>

    @if ($session->status === 'aktif')
        <div class="min-bar">
            <div class="min-bar-left">
                <span class="live-dot"></span>
                <div>
                    <div class="min-bar-title">{{ $session->jadwal->matakuliah->nama_mk }} — Kelas
                        {{ $session->jadwal->kelas->nama_kelas }}</div>
                    <div class="min-bar-sub">Sesi aktif · Kedaluwarsa {{ $session->qr_expired_at->format('H:i') }} WIB</div>
                </div>
            </div>
            <div class="min-bar-right">
                <span class="min-count">{{ $presentCount }}/{{ $totalCount }} hadir</span>
                <a href="{{ route('dashboard') }}" class="btn-min-action">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    @endif

    <div class="pres-grid">

        {{-- QR PANE --}}
        <div class="pane">
            <div class="qr-head">
                <h3>QR Code Presensi</h3>
                @if ($session->status === 'aktif')
                    <span class="s-badge s-live">
                        <span
                            style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#059669;margin-right:5px;animation:lp 1.4s infinite;"></span>
                        AKTIF
                    </span>
                @else
                    <span class="s-badge s-done">{{ strtoupper($session->status) }}</span>
                @endif
            </div>
            <div class="qr-body">
                <div class="qr-frame">{!! $qrImage !!}</div>
                <div class="qr-meta">
                    <div class="qm-item">
                        <div class="qm-lbl">Kelas</div>
                        <div class="qm-val">{{ $session->jadwal->kelas->nama_kelas }}</div>
                    </div>
                    <div class="qm-item">
                        <div class="qm-lbl">Tanggal</div>
                        <div class="qm-val">{{ $session->tanggal->format('d/m/Y') }}</div>
                    </div>
                    <div class="qm-item">
                        <div class="qm-lbl">Berlaku s/d</div>
                        <div class="qm-val" style="font-family:'DM Mono',monospace;">
                            {{ $session->qr_expired_at->format('H:i') }}</div>
                    </div>
                    <div class="qm-item">
                        <div class="qm-lbl">Radius</div>
                        <div class="qm-val">{{ config('attendance.max_distance_m', 10) }}m</div>
                    </div>
                </div>
                @if ($session->status === 'aktif')
                    <div class="qr-actions">
                        <form method="POST" action="{{ route('sessions.refresh', $session) }}">
                            @csrf
                            <button class="btn btn-out">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="23 4 23 10 17 10" />
                                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                                </svg>
                                Refresh QR
                            </button>
                        </form>
                        <form method="POST" action="{{ route('sessions.close', $session) }}"
                            onsubmit="return confirm('Tutup sesi? Mahasiswa tidak bisa scan lagi.')">
                            @csrf
                            <button class="btn btn-red">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                Tutup Sesi
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- STUDENT PANE --}}
        <div class="pane">
            <div class="stu-top">
                <div class="att-summary">
                    <div class="as-item">
                        <div class="as-v" style="color:var(--green)" id="cnt-hadir">{{ $presentCount }}</div>
                        <div class="as-l">Hadir</div>
                    </div>
                    <div class="as-item">
                        <div class="as-v" style="color:var(--yellow)" id="cnt-sakit">
                            {{ $students->where('status', 'sakit')->count() }}
                        </div>
                        <div class="as-l">Sakit</div>
                    </div>
                    <div class="as-item">
                        <div class="as-v" style="color:var(--blue)" id="cnt-izin">
                            {{ $students->where('status', 'izin')->count() }}
                        </div>
                        <div class="as-l">Izin</div>
                    </div>
                    <div class="as-item">
                        <div class="as-v" style="color:var(--red)" id="cnt-alpha">
                            {{ $students->where('status', 'alpha')->count() }}</div>
                        <div class="as-l">Alpha</div>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:12px;color:var(--txt-g);">Total: <strong>{{ $totalCount }}</strong> mhs</div>
                    <div class="prog-wrap" style="width:160px;margin-top:6px;">
                        <div class="prog-fill" id="prog-bar"
                            style="width:{{ $totalCount > 0 ? round(($presentCount / $totalCount) * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="filter-bar">
                <button class="flt on" onclick="filterRows('all',this)">Semua</button>
                <button class="flt" onclick="filterRows('hadir',this)">Hadir</button>
                <button class="flt" onclick="filterRows('sakit',this)">Sakit</button>
                <button class="flt" onclick="filterRows('izin',this)">Izin</button>
                <button class="flt" onclick="filterRows('alpha',this)">Alpha</button>
            </div>

            <div class="stu-head">
                <span>NO</span><span>NIM</span><span>NAMA MAHASISWA</span><span>STATUS</span>
            </div>

            <div class="stu-scroll">
                @foreach ($students as $i => $att)
                    @php
                        $sessionActive = $session->status === 'aktif';
                        $scanInfo = null;
                        if ($att->scan_time) {
                            $scanInfo = $att->scan_time->format('H:i');
                        }
                        if ($att->jarak_meter) {
                            $scanInfo .= ($scanInfo ? ' · ' : '') . $att->jarak_meter . 'm';
                        }
                    @endphp
                    <div class="stu-row" data-status="{{ $att->status }}" data-id="{{ $att->mahasiswa_id }}">
                        <div class="stu-num">{{ $i + 1 }}</div>
                        <div class="stu-nim">{{ $att->mahasiswa->nim ?? '-' }}</div>
                        <div>
                            <div class="stu-name">{{ $att->mahasiswa->nama }}</div>
                            @if ($scanInfo)
                                <div class="stu-scan">{{ $scanInfo }}</div>
                            @endif
                        </div>
                        <div class="ov-group">
                            @foreach (['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $val => $label)
                                <input type="radio" name="ov_{{ $att->mahasiswa_id }}"
                                    id="ov_{{ $att->mahasiswa_id }}_{{ $val }}" value="{{ $val }}"
                                    {{ $att->status === $val ? 'checked' : '' }}
                                    @if ($sessionActive) onchange="override({{ $att->mahasiswa_id }}, this.value)" @else disabled @endif>
                                <label for="ov_{{ $att->mahasiswa_id }}_{{ $val }}">{{ $label }}</label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            const OVERRIDE_URL = "{{ route('sessions.override', $session) }}";
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;
            const TOTAL = {{ $totalCount }};

            function filterRows(val, btn) {
                document.querySelectorAll('.flt').forEach(function(b) {
                    b.classList.remove('on');
                });
                btn.classList.add('on');
                document.querySelectorAll('.stu-row').forEach(function(r) {
                    r.style.display = (val === 'all' || r.dataset.status === val) ? '' : 'none';
                });
            }

            async function override(studentId, status) {
                if (!status) return;
                var res = await fetch(OVERRIDE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        mahasiswa_id: studentId,
                        status: status
                    }),
                });
                var data = await res.json();
                if (!data.success) {
                    // Kembalikan radio ke status sebelumnya jika gagal
                    var row = document.querySelector('[data-id="' + studentId + '"]');
                    if (row) {
                        var prev = row.dataset.status;
                        var prevRadio = document.getElementById('ov_' + studentId + '_' + prev);
                        if (prevRadio) prevRadio.checked = true;
                    }
                    return;
                }
                var row = document.querySelector('[data-id="' + studentId + '"]');
                if (row) row.dataset.status = status;
                updateCounts();
            }

            function updateCounts() {
                var rows = document.querySelectorAll('.stu-row');
                var counts = {
                    hadir: 0,
                    sakit: 0,
                    izin: 0,
                    alpha: 0
                };
                rows.forEach(function(r) {
                    var s = r.dataset.status;
                    if (typeof counts[s] !== 'undefined') {
                        counts[s]++;
                    }
                });
                var pct = TOTAL > 0 ? Math.round(counts.hadir / TOTAL * 100) : 0;

                function e(id) {
                    return document.getElementById(id);
                }
                if (e('cnt-hadir')) e('cnt-hadir').textContent = counts.hadir;
                if (e('cnt-sakit')) e('cnt-sakit').textContent = counts.sakit;
                if (e('cnt-izin')) e('cnt-izin').textContent = counts.izin;
                if (e('cnt-alpha')) e('cnt-alpha').textContent = counts.alpha;
                if (e('prog-bar')) e('prog-bar').style.width = pct + '%';
            }
        </script>
    @endpush
@endsection
