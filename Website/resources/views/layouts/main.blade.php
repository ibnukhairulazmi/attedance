<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHJlY3Qgd2lkdGg9IjI0IiBoZWlnaHQ9IjI0IiByeD0iNiIgZmlsbD0iIzA4ODM5NSIvPjxnIGZpbGw9Im5vbmUiIHN0cm9rZT0iI2ZmZiIgc3Ryb2tlLXdpZHRoPSIyLjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PGxpbmUgeDE9IjQiIHkxPSI5IiB4Mj0iNCIgeTI9IjE5Ii8+PGxpbmUgeDE9IjkiIHkxPSI2IiB4Mj0iOSIgeTI9IjE5Ii8+PGxpbmUgeDE9IjE0IiB5MT0iMTEiIHgyPSIxNCIgeTI9IjE5Ii8+PGxpbmUgeDE9IjE5IiB5MT0iOCIgeDI9IjE5IiB5Mj0iMTkiLz48L2c+PC9zdmc+">
    <title>@yield('title', 'Dashboard') — PresensiApp</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --teal: #088395;
            --teal-dk: #09637E;
            --teal-lt: #7AB2B2;
            --navy: #111827;
            --bg: #EBF4F6;
            --white: #fff;
            --surf: #f2f9fb;
            --surf2: #d4eaf0;
            --border: #bcd9e2;
            --txt: #111827;
            --txt-m: #374151;
            --txt-g: #6B7280;
            --txt-lt: #9CA3AF;
            --green: #059669;
            --gbg: #d1fae5;
            --yellow: #d97706;
            --ybg: #fef3c7;
            --red: #dc2626;
            --rbg: #fee2e2;
            --blue: #2563eb;
            --bbg: #dbeafe;
            --sh-sm: 0 1px 4px rgba(8, 131, 149, .10);
            --sh-md: 0 4px 20px rgba(8, 131, 149, .14);
            --sh-lg: 0 8px 40px rgba(8, 131, 149, .20);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--txt);
            display: flex;
            min-height: 100vh;
            overflow: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: 252px;
            min-height: 100vh;
            background: var(--navy);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
        }

        .sb-logo {
            padding: 22px 20px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
        }

        .sb-logo-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sb-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--teal), var(--teal-dk));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(8, 131, 149, .4);
        }

        .sb-name {
            font-size: 21px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.5px;
        }

        .sb-name span {
            color: var(--teal-lt);
        }

        .sb-tag {
            font-size: 10px;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, .26);
            margin-top: 4px;
            text-transform: uppercase;
        }

        .sb-profile {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .sb-av {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .1);
            border: 2px solid rgba(122, 178, 178, .3);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sb-pname {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        .sb-pnip {
            font-size: 11px;
            color: rgba(255, 255, 255, .3);
            font-family: 'DM Mono', monospace;
        }

        .sb-pbadge {
            margin-top: 3px;
            display: inline-block;
            padding: 2px 8px;
            background: rgba(8, 131, 149, .28);
            border: 1px solid rgba(122, 178, 178, .22);
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            color: var(--teal-lt);
        }

        .sb-nav {
            padding: 14px 12px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-lbl {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .2);
            padding: 4px 10px 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 10px;
            cursor: pointer;
            color: rgba(255, 255, 255, .46);
            font-size: 13.5px;
            font-weight: 500;
            transition: all .2s;
            margin-bottom: 2px;
            position: relative;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: none;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, .05);
            color: rgba(255, 255, 255, .82);
        }

        .nav-item.active {
            background: rgba(8, 131, 149, .22);
            color: var(--teal-lt);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 6px;
            bottom: 6px;
            width: 3px;
            background: var(--teal);
            border-radius: 0 4px 4px 0;
        }

        .nav-svg {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
            opacity: .55;
        }

        .nav-item:hover .nav-svg,
        .nav-item.active .nav-svg {
            opacity: 1;
        }

        .sb-footer {
            padding: 14px 12px;
            border-top: 1px solid rgba(255, 255, 255, .07);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            color: rgba(255, 255, 255, .36);
            font-size: 13.5px;
            font-weight: 500;
            transition: all .2s;
            border: none;
            background: none;
            width: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .logout-btn:hover {
            background: rgba(220, 38, 38, .14);
            color: #fca5a5;
        }

        /* MAIN */
        .main {
            margin-left: 252px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-y: auto;
            max-height: 100vh;
        }

        .topbar {
            padding: 13px 26px;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: var(--sh-sm);
        }

        .tb-left h1 {
            font-size: 18px;
            font-weight: 800;
            color: var(--txt);
        }

        .tb-sub {
            font-size: 12px;
            color: var(--txt-g);
            margin-top: 1px;
        }

        .tb-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .clock-t {
            font-family: 'DM Mono', monospace;
            font-size: 18px;
            font-weight: 500;
            color: var(--teal-dk);
        }

        .clock-d {
            font-size: 11px;
            color: var(--txt-lt);
            text-align: right;
        }

        .content {
            padding: 22px 26px;
            flex: 1;
        }

        .flash-ok {
            margin-bottom: 16px;
            padding: 12px 16px;
            background: var(--gbg);
            border: 1px solid #6ee7b7;
            color: var(--green);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
        }

        .flash-err {
            margin-bottom: 16px;
            padding: 12px 16px;
            background: var(--rbg);
            border: 1px solid #fca5a5;
            color: var(--red);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
        }

        .main::-webkit-scrollbar,
        .sb-nav::-webkit-scrollbar {
            width: 4px;
        }

        .main::-webkit-scrollbar-thumb,
        .sb-nav::-webkit-scrollbar-thumb {
            background: rgba(8, 131, 149, .2);
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="sb-logo">
            <div class="sb-logo-row">
                <div class="sb-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff"
                        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="1" y1="6" x2="1" y2="18" />
                        <line x1="6" y1="3" x2="6" y2="18" />
                        <line x1="11" y1="8" x2="11" y2="18" />
                        <line x1="16" y1="5" x2="16" y2="18" />
                        <line x1="21" y1="1" x2="21" y2="18" />
                    </svg>
                </div>
                <span class="sb-name">Presensi<span>App</span></span>
            </div>
            <div class="sb-tag">Sistem Presensi Digital</div>
        </div>
        <div class="sb-profile">
            <div class="sb-av">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.6)"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
            </div>
            <div>
                <div class="sb-pname">{{ auth()->user()->nama }}</div>
                <div class="sb-pnip">{{ auth()->user()->nidn ?? '—' }}</div>
            </div>
        </div>
        <nav class="sb-nav">
            <div class="nav-lbl">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('reports.index') }}"
                class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
                Laporan Presensi
            </a>

            <div class="nav-lbl" style="margin-top: 14px;">Pembelajaran</div>
            <a href="{{ route('assignments.index') }}"
                class="nav-item {{ request()->routeIs('assignments.*') ? 'active' : '' }}">
                <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>
                Manajemen Tugas
            </a>
            <a href="{{ route('gradebook.index') }}"
                class="nav-item {{ request()->routeIs('gradebook.*') ? 'active' : '' }}">
                <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                    <line x1="9" y1="7" x2="15" y2="7" />
                    <line x1="9" y1="11" x2="15" y2="11" />
                </svg>
                Buku Nilai (Gradebook)
            </a>
        </nav>
        <div class="sb-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="tb-left">
                <h1>@yield('header', 'Dashboard')</h1>
            </div>
            <div class="tb-right">
                <div>
                    <div class="tb-sub">@yield('subheader', now()->locale('id')->isoFormat('dddd, D MMMM YYYY'))</div>
                </div>
            </div>
        </div>
        <div class="content">
            @if (session('success'))
                <div class="flash-ok">✓ {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="flash-err">✕ {{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </div>

    <script>
        function tick() {
            const n = new Date(),
                p = v => String(v).padStart(2, '0');
            document.getElementById('clock').textContent = `${p(n.getHours())}:${p(n.getMinutes())}:${p(n.getSeconds())}`;
        }
        setInterval(tick, 1000);
    </script>
    @stack('scripts')
</body>

</html>
