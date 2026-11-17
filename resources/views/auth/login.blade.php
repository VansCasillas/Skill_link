{{--
  resources/views/auth/login.blade.php

  Route:
    POST route('login.post')   → proses masuk  (email + password)
    POST route('register')     → proses daftar (username, email, npm, password)

  Flash dari server:
    Masuk gagal     : back()->withErrors([...], 'loginBag')
    Daftar gagal    : back()->withErrors([...], 'registerBag') ->withInput()
    Daftar berhasil : redirect()->route('login')->with('registerOk', true)
--}}
@php
$loginErrors = $errors->loginBag->any();
$registerErrors = $errors->registerBag->any();
$registerOk = session()->has('registerOk');

// Buka panel Daftar jika ada error validasi pendaftaran
$showRegister = $registerErrors;
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk · Skill Link</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --bg: #CBDEF6;
            --blue: #0989D8;
            --btn: #0A87D9;
            --ink: #0F172A;
            --muted: #475569;
            --ph: #64748B;
            --line: #475569;
            --white: #fff;
            --err: #D92D20;
            --err-bg: #FEF3F2;
            --err-line: #FDA29B;
            --ok: #067647;
            --ok-bg: #ECFDF3;
            --ok-line: #6CE9A6;
            --ok-field: #12B76A;
            box-sizing: border-box;
        }

        *,
        *::before,
        *::after {
            box-sizing: inherit
        }

        html {
            height: 100%
        }

        body {
            height: 100%;
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        /* ─── Layout ──────────────────────────────────────────────────── */
        .page {
            min-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(24px, 5vw, 118px) clamp(16px, 6vw, 154px);
        }

        .wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            width: 100%;
            max-width: 1164px;
        }

        /* ─── Kartu kiri ──────────────────────────────────────────────── */
        .hero {
            background: var(--blue);
            border: 1px solid #000;
            border-radius: 28px;
            box-shadow: 0 4px 4px rgba(0, 0, 0, .25);
            min-height: 476px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 44px 30px 32px;
        }

        .logobox {
            width: 155px;
            height: 140px;
            background: var(--white);
            border-radius: 30px;
            display: grid;
            place-items: center;
        }

        .logobox img {
            width: 114px;
            height: 114px;
            object-fit: contain;
            border-radius: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-top: 16px;
            color: var(--white);
            font-weight: 800;
            font-size: 30px;
            line-height: 38px;
        }

        .hero h2 {
            margin: 36px 0 0;
            max-width: 403px;
            color: var(--white);
            text-align: center;
            font-weight: 800;
            font-size: 44px;
            line-height: 52px;
            text-wrap: balance;
        }

        /* ─── Kartu form ──────────────────────────────────────────────── */
        .card {
            background: var(--white);
            border-radius: 24px;
            box-shadow: 0 12px 32px rgba(10, 51, 102, .14);
            padding: 32px 32px 28px;
            min-height: 476px;
            display: flex;
            flex-direction: column;
        }

        /* ─── View (panel Masuk / Daftar) ────────────────────────────── */
        .view {
            display: flex;
            flex-direction: column;
            gap: 14px;
            flex: 1;
        }

        .view[hidden] {
            display: none
        }

        .view h1 {
            margin: 0 0 2px;
            font-weight: 700;
            font-size: 28px;
            line-height: 35px;
            color: var(--ink);
        }

        /* ─── Banner ──────────────────────────────────────────────────── */
        .banner {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid;
            font-size: 12px;
            line-height: 15px;
            font-weight: 500;
        }

        .banner[hidden] {
            display: none
        }

        .banner .ico {
            flex: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: var(--white);
            font-weight: 700;
            font-size: 12px;
        }

        .banner.error {
            background: var(--err-bg);
            border-color: var(--err-line);
            color: var(--err)
        }

        .banner.error .ico {
            background: var(--err)
        }

        .banner.ok {
            background: var(--ok-bg);
            border-color: var(--ok-line);
            color: var(--ok)
        }

        .banner.ok .ico {
            background: var(--ok)
        }

        /* ─── Field ───────────────────────────────────────────────────── */
        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field label {
            font-weight: 500;
            font-size: 12px;
            line-height: 15px;
            color: var(--ink);
        }

        .input {
            display: flex;
            align-items: center;
            gap: 8px;
            height: 41px;
            padding: 0 14px;
            background: var(--white);
            border: 1.5px solid var(--line);
            border-radius: 12px;
            transition: border-color .15s, box-shadow .15s;
        }

        .input:focus-within {
            border-color: var(--btn);
            box-shadow: 0 0 0 3px rgba(9, 137, 216, .22);
        }

        .input input {
            flex: 1;
            min-width: 0;
            border: 0;
            outline: 0;
            background: transparent;
            font-family: inherit;
            font-size: 13px;
            line-height: 16px;
            color: var(--ink);
            padding: 0;
        }

        .input input::placeholder {
            color: var(--ph)
        }

        .input .mark {
            display: none;
            font-weight: 700;
            font-size: 12px;
            color: var(--err);
        }

        /* Toggle show/hide password */
        .toggle-pw {
            flex: none;
            display: grid;
            place-items: center;
            width: 28px;
            height: 28px;
            margin-right: -6px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: var(--ph);
            padding: 0;
            cursor: pointer;
        }

        .toggle-pw:hover {
            color: var(--ink)
        }

        .toggle-pw svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.6;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .toggle-pw .off {
            display: none
        }

        .toggle-pw[aria-pressed="true"] .on {
            display: none
        }

        .toggle-pw[aria-pressed="true"] .off {
            display: block
        }

        /* Error / ok states */
        .msg {
            display: none;
            margin: 0;
            font-weight: 500;
            font-size: 11px;
            line-height: 14px;
            color: var(--err);
        }

        .field.is-error label {
            color: var(--err)
        }

        .field.is-error .input {
            background: var(--err-bg);
            border: 1.5px solid var(--err)
        }

        .field.is-error .mark {
            display: block
        }

        .field.is-error .msg {
            display: block
        }

        .field.is-ok .input {
            border-color: var(--ok-field)
        }

        /* ─── Buttons ─────────────────────────────────────────────────── */
        .buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: auto;
            padding-top: 10px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 38px;
            padding: 11px 0;
            border: none;
            border-radius: 12px;
            background: var(--btn);
            color: var(--white);
            font-family: inherit;
            font-weight: 700;
            font-size: 13px;
            line-height: 16px;
            cursor: pointer;
            transition: filter .15s, transform .05s;
            text-decoration: none;
        }

        .btn:hover {
            filter: brightness(.92)
        }

        .btn:active {
            transform: translateY(1px)
        }

        .btn:focus-visible {
            outline: 3px solid rgba(9, 137, 216, .45);
            outline-offset: 2px;
        }

        /* Tombol Daftar (outline / secondary) */
        .btn-outline {
            background: var(--white);
            color: var(--btn);
            border: 1.5px solid var(--btn);
        }

        .btn-outline:hover {
            background: #f0f7ff
        }

        /* ─── Link teks ───────────────────────────────────────────────── */
        .back-link {
            align-self: flex-start;
            background: none;
            border: 0;
            padding: 0;
            color: var(--btn);
            font-family: inherit;
            font-weight: 700;
            font-size: 12px;
            line-height: 15px;
            cursor: pointer;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .back-link:hover {
            text-decoration: underline
        }

        /* ─── Note ────────────────────────────────────────────────────── */
        .note {
            margin: 8px 0 0;
            text-align: center;
            font-size: 11px;
            line-height: 14px;
            color: var(--muted);
        }

        /* ─── Responsive ──────────────────────────────────────────────── */
        @media (max-width: 900px) {
            .wrap {
                grid-template-columns: 1fr;
                max-width: 570px
            }

            .hero {
                min-height: 0;
                padding: 28px 20px 32px
            }

            .logobox {
                width: 116px;
                height: 105px;
                border-radius: 24px
            }

            .logobox img {
                width: 84px;
                height: 84px
            }

            .brand {
                font-size: 26px
            }

            .hero h2 {
                margin-top: 24px;
                font-size: clamp(28px, 7vw, 36px);
                line-height: 1.18;
            }

            .card {
                min-height: 0;
                padding: 24px 20px 24px
            }
        }
    </style>
</head>

<body>
    <main class="page">
        <div class="wrap">

            {{-- ─── Kartu kiri ───────────────────────────────────────── --}}
            <section class="hero" aria-label="Skill Link">
                <div class="logobox">
                    <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Logo Skill Link">
                </div>
                <div class="brand"><span>Skill Link</span></div>
                <h2>Kembangkan skill, raih peluangmu</h2>
            </section>

            {{-- ─── Kartu form ───────────────────────────────────────── --}}
            <section class="card">

                {{-- ════════════════════════════════════════════════════
                     VIEW: MASUK
                ════════════════════════════════════════════════════ --}}
                <form class="view"
                    id="view-login"
                    method="POST"
                    action="{{ route('login.post') }}"
                    novalidate
                    @if($showRegister) hidden @endif>
                    @csrf

                    <h1>Masuk</h1>

                    {{-- Banner sukses pendaftaran --}}
                    @if($registerOk)
                    <div class="banner ok" role="status">
                        <span class="ico" aria-hidden="true">✓</span>
                        <span>Akun berhasil dibuat! Silakan masuk dengan email dan kata sandi Anda.</span>
                    </div>
                    @endif

                    {{-- Banner error masuk --}}
                    @if($loginErrors)
                    <div class="banner error" role="alert">
                        <span class="ico" aria-hidden="true">!</span>
                        <span>Gagal masuk. Email atau kata sandi yang Anda masukkan salah.</span>
                    </div>
                    @endif

                    {{-- Field Email --}}
                    <div class="field {{ $loginErrors ? 'is-error' : '' }}">
                        <label for="login_email">Email</label>
                        <div class="input">
                            <input id="login_email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                placeholder="contoh@email.com"
                                required>
                            <span class="mark" aria-hidden="true">!</span>
                        </div>
                        <p class="msg" role="alert">
                            @if($errors->loginBag->has('email'))
                            {{ $errors->loginBag->first('email') }}
                            @else
                            Email tidak terdaftar atau salah
                            @endif
                        </p>
                    </div>

                    {{-- Field Kata sandi --}}
                    <div class="field {{ $loginErrors ? 'is-error' : '' }}">
                        <label for="login_password">Kata sandi</label>
                        <div class="input">
                            <input id="login_password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                placeholder="••••••••"
                                required>
                            <span class="mark" aria-hidden="true">!</span>
                            <button class="toggle-pw" type="button"
                                data-target="login_password"
                                aria-label="Tampilkan kata sandi"
                                aria-pressed="false">
                                <svg class="on" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg class="off" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M3 3l18 18M10.6 6.1A9.8 9.8 0 0112 5c6.4 0 10 7 10 7a17 17 0 01-3.1 4M6.5 6.9C3.6 8.8 2 12 2 12s3.6 7 10 7c1.6 0 3-.4 4.3-1M9.9 9.9a3 3 0 004.2 4.2" />
                                </svg>
                            </button>
                        </div>
                        <p class="msg" role="alert">Kata sandi salah, silakan coba lagi</p>
                    </div>

                    {{-- Dua tombol: Masuk + Daftar --}}
                    <div class="buttons">
                        <button class="btn" type="submit">Masuk</button>
                        <button class="btn btn-outline"
                            type="button"
                            id="go-to-register">Daftar</button>
                    </div>
                </form>

                {{-- ════════════════════════════════════════════════════
                     VIEW: DAFTAR
                ════════════════════════════════════════════════════ --}}
                <form class="view"
                    id="view-register"
                    method="POST"
                    action="{{ route('register') }}"
                    novalidate
                    @if(!$showRegister) hidden @endif>
                    @csrf

                    {{-- Tombol kembali --}}
                    <button class="back-link" type="button" id="go-to-login" aria-label="Kembali ke halaman masuk">
                        ← Kembali
                    </button>

                    <h1>Daftar Akun</h1>

                    {{-- Banner error pendaftaran --}}
                    @if($registerErrors)
                    <div class="banner error" role="alert">
                        <span class="ico" aria-hidden="true">!</span>
                        <span>Gagal mendaftar. Periksa kembali data yang Anda isi.</span>
                    </div>
                    @endif

                    {{-- Field Username --}}
                    <div class="field {{ $errors->registerBag->has('name') ? 'is-error' : '' }}">
                        <label for="reg_name">Username</label>
                        <div class="input">
                            <input id="reg_name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                autocomplete="username"
                                placeholder="Nama pengguna Anda"
                                required>
                            <span class="mark" aria-hidden="true">!</span>
                        </div>
                        <p class="msg" role="alert">
                            {{ $errors->registerBag->first('name') ?: 'Username wajib diisi' }}
                        </p>
                    </div>

                    {{-- Field Email --}}
                    <div class="field {{ $errors->registerBag->has('email') ? 'is-error' : '' }}">
                        <label for="reg_email">Email</label>
                        <div class="input">
                            <input id="reg_email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                placeholder="contoh@email.com"
                                required>
                            <span class="mark" aria-hidden="true">!</span>
                        </div>
                        <p class="msg" role="alert">
                            {{ $errors->registerBag->first('email') ?: 'Email tidak valid atau sudah terdaftar' }}
                        </p>
                    </div>

                    {{-- Field NPM --}}
                    <div class="field {{ $errors->registerBag->has('npm') ? 'is-error' : '' }}">
                        <label for="reg_npm">NPM</label>
                        <div class="input">
                            <input id="reg_npm"
                                name="npm"
                                type="text"
                                value="{{ old('npm') }}"
                                placeholder="Nomor Pokok Mahasiswa"
                                required>
                            <span class="mark" aria-hidden="true">!</span>
                        </div>
                        <p class="msg" role="alert">
                            {{ $errors->registerBag->first('npm') ?: 'NPM tidak valid atau sudah terdaftar' }}
                        </p>
                    </div>

                    {{-- Field Kata sandi --}}
                    <div class="field {{ $errors->registerBag->has('password') ? 'is-error' : '' }}">
                        <label for="reg_password">Kata sandi</label>
                        <div class="input">
                            <input id="reg_password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Min. 8 karakter"
                                required>
                            <span class="mark" aria-hidden="true">!</span>
                            <button class="toggle-pw" type="button"
                                data-target="reg_password"
                                aria-label="Tampilkan kata sandi"
                                aria-pressed="false">
                                <svg class="on" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg class="off" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M3 3l18 18M10.6 6.1A9.8 9.8 0 0112 5c6.4 0 10 7 10 7a17 17 0 01-3.1 4M6.5 6.9C3.6 8.8 2 12 2 12s3.6 7 10 7c1.6 0 3-.4 4.3-1M9.9 9.9a3 3 0 004.2 4.2" />
                                </svg>
                            </button>
                        </div>
                        <p class="msg" role="alert">
                            {{ $errors->registerBag->first('password') ?: 'Kata sandi minimal 8 karakter' }}
                        </p>
                    </div>

                    <div class="buttons">
                        <button class="btn" type="submit">Buat Akun</button>
                    </div>

                    <p class="note">Akun baru diverifikasi lewat email</p>
                </form>

            </section>
        </div>
    </main>

    <script>
        (function() {
            'use strict';

            var viewLogin = document.getElementById('view-login');
            var viewRegister = document.getElementById('view-register');
            var btnToReg = document.getElementById('go-to-register');
            var btnToLogin = document.getElementById('go-to-login');

            /* ── Pindah ke form Daftar ──────────────────────────────── */
            if (btnToReg) {
                btnToReg.addEventListener('click', function() {
                    viewLogin.hidden = true;
                    viewRegister.hidden = false;
                    var first = viewRegister.querySelector('input');
                    if (first) first.focus();
                });
            }

            /* ── Kembali ke form Masuk ──────────────────────────────── */
            if (btnToLogin) {
                btnToLogin.addEventListener('click', function() {
                    viewRegister.hidden = true;
                    viewLogin.hidden = false;
                    var first = viewLogin.querySelector('input');
                    if (first) first.focus();
                });
            }

            /* ── Toggle show/hide password ──────────────────────────── */
            document.querySelectorAll('.toggle-pw').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var inp = document.getElementById(btn.getAttribute('data-target'));
                    var show = inp.type === 'password';
                    inp.type = show ? 'text' : 'password';
                    btn.setAttribute('aria-pressed', show ? 'true' : 'false');
                    btn.setAttribute('aria-label',
                        show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                    inp.focus();
                });
            });

            /* ── Reset state error saat user mulai mengetik ──────────── */
            [viewLogin, viewRegister].forEach(function(form) {
                if (!form) return;
                form.addEventListener('input', function() {
                    form.querySelectorAll('.field').forEach(function(f) {
                        f.classList.remove('is-error', 'is-ok');
                    });
                    form.querySelectorAll('.banner').forEach(function(b) {
                        b.hidden = true;
                    });
                });
            });
        })();
    </script>
</body>

</html>