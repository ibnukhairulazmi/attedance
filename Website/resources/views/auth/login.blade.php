<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHJlY3Qgd2lkdGg9IjI0IiBoZWlnaHQ9IjI0IiByeD0iNiIgZmlsbD0iIzA4ODM5NSIvPjxnIGZpbGw9Im5vbmUiIHN0cm9rZT0iI2ZmZiIgc3Ryb2tlLXdpZHRoPSIyLjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PGxpbmUgeDE9IjQiIHkxPSI5IiB4Mj0iNCIgeTI9IjE5Ii8+PGxpbmUgeDE9IjkiIHkxPSI2IiB4Mj0iOSIgeTI9IjE5Ii8+PGxpbmUgeDE9IjE0IiB5MT0iMTEiIHgyPSIxNCIgeTI9IjE5Ii8+PGxpbmUgeDE9IjE5IiB5MT0iOCIgeDI9IjE5IiB5Mj0iMTkiLz48L2c+PC9zdmc+">
    <title>Login — PresensiApp</title>
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
            --border: #bcd9e2;
            --txt: #111827;
            --txt-g: #6B7280;
            --txt-lt: #9CA3AF;
            --red: #dc2626;
            --rbg: #fee2e2;
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(8, 131, 149, .15) 0%, transparent 60%);
        }

        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 24px;
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            box-shadow: var(--sh-lg);
            position: relative;
            z-index: 1;
        }

        .card-top {
            background: linear-gradient(135deg, var(--teal-dk) 0%, var(--teal) 100%);
            padding: 32px 32px 28px;
            text-align: center;
        }

        .logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .logo-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, .15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-name {
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.5px;
        }

        .logo-name span {
            color: rgba(255, 255, 255, .6);
        }

        .card-top p {
            font-size: 13px;
            color: rgba(255, 255, 255, .65);
        }

        .card-body {
            padding: 28px 32px 28px;
        }

        .card-body h2 {
            font-size: 17px;
            font-weight: 800;
            color: var(--txt);
            margin-bottom: 4px;
        }

        .card-body .sub {
            font-size: 13px;
            color: var(--txt-g);
            margin-bottom: 24px;
        }

        .err-box {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 11px 14px;
            background: var(--rbg);
            border: 1px solid #fca5a5;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 13px;
            color: var(--red);
        }

        .fld {
            margin-bottom: 16px;
        }

        .fld label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--txt-g);
            margin-bottom: 6px;
        }

        .fld input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--txt);
            background: var(--surf);
            transition: .2s;
        }

        .fld input:focus {
            outline: none;
            border-color: var(--teal);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(8, 131, 149, .08);
        }

        .fld input::placeholder {
            color: var(--txt-lt);
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--teal-dk), var(--teal));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: .2s;
            box-shadow: 0 4px 14px rgba(8, 131, 149, .35);
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(8, 131, 149, .4);
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-top">
            <div class="logo-row">
                <div class="logo-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff"
                        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="1" y1="6" x2="1" y2="18" />
                        <line x1="6" y1="3" x2="6" y2="18" />
                        <line x1="11" y1="8" x2="11" y2="18" />
                        <line x1="16" y1="5" x2="16" y2="18" />
                        <line x1="21" y1="1" x2="21" y2="18" />
                    </svg>
                </div>
                <span class="logo-name">Presensi<span>App</span></span>
            </div>
            <p>Sistem Presensi Digital</p>
        </div>
        <div class="card-body">
            <h2>Masuk ke Sistem</h2>
            <p class="sub">Masukkan NIDN dan password akun Anda</p>
            @if ($errors->any())
                <div class="err-box">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                        style="flex-shrink:0;margin-top:1px">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="fld">
                    <label>NIDN</label>
                    <input type="nidn" name="nidn" value="{{ old('nidn') }}" required autofocus
                        placeholder="NIDN">
                </div>
                <div class="fld">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn-login">Masuk</button>
            </form>
        </div>
    </div>
</body>

</html>
