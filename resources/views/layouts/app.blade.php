<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') · Skill Link</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #CBDDF5;
            --blue: #0A87D9;
            --ink: #0F172A;
            --muted: #475569;
            --soft: #F3F8FE;
            --chip: #E0F0FC;
            --chip-ink: #0A6DB0;
            --warn: #B54708;
            --ok: #067647;
            --track: #DCE8F7;
            --overlay: rgba(11, 27, 51, .55);
            --shadow: 0 4px 16px rgba(10, 51, 102, .08);
            --top-h: 60px;
            --sb-w: 240px;
            color-scheme: light;
            box-sizing: border-box;
        }

        *,
        *::before,
        *::after {
            box-sizing: inherit
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;
            min-height: 100vh
        }

        body.sb-open {
            overflow: hidden
        }

        a {
            color: inherit;
            text-decoration: none
        }

        button {
            font-family: inherit;
            cursor: pointer
        }

        svg.i,
        i.i {
            width: 20px;
            height: 20px;
            flex: none;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .logo {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: #fff;
            display: grid;
            place-items: center;
            flex: none
        }

        .logo img {
            width: 28px;
            height: 28px;
            object-fit: contain
        }

        .burger {
            width: 36px;
            height: 36px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            display: grid;
            place-content: center;
            gap: 4.5px;
            padding: 0;
            flex: none
        }

        .burger:hover {
            background: rgba(255, 255, 255, .14)
        }

        .burger span {
            display: block;
            width: 18px;
            height: 2.5px;
            background: #fff;
            border-radius: 2px
        }

        .brand-name {
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            line-height: 20px;
            white-space: nowrap
        }

        /* ===== Topbar (full width) ===== */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--top-h);
            z-index: 20;
            background: var(--blue);
            box-shadow: 0 4px 4px rgba(0, 0, 0, .25);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 24px 0 12px
        }

        .topbar .brand-name {
            margin-left: 0
        }

        .search {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 480px;
            max-width: calc(100% - 520px);
            height: 38px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 16px;
            background: rgba(255, 255, 255, .2);
            border: 1px solid rgba(255, 255, 255, .45);
            border-radius: 19px;
            color: #fff
        }

        .search svg,
        .search i {
            width: 16px;
            height: 16px
        }

        .search input {
            flex: 1;
            min-width: 0;
            border: 0;
            outline: 0;
            background: transparent;
            color: #fff;
            font-family: inherit;
            font-size: 12px
        }

        .search input::placeholder {
            color: rgba(255, 255, 255, .9)
        }

        .tb-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 14px
        }

        .icon-btn {
            position: relative;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 0;
            background: rgba(255, 255, 255, .2);
            color: #fff;
            display: grid;
            place-items: center;
            padding: 0
        }

        .icon-btn .dot {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #F04438;
            border: 2px solid var(--blue)
        }

        .me {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .avatar {
            border-radius: 50%;
            border: 2px solid #fff;
            display: grid;
            place-items: center;
            flex: none;
            color: #fff;
            font-weight: 700
        }

        .me .avatar {
            width: 38px;
            height: 38px;
            background: #14B8A6;
            color: var(--ink)
        }

        .me-text {
            display: flex;
            flex-direction: column;
            gap: 2px
        }

        .me-name {
            color: #fff;
            font-weight: 700;
            font-size: 12px;
            line-height: 15px
        }

        .me-sub {
            color: rgba(255, 255, 255, .85);
            font-size: 10px;
            line-height: 13px
        }

        /* ===== Sidebar (overlay) + scrim gelap ===== */
        .scrim {
            position: fixed;
            inset: 0;
            z-index: 30;
            background: var(--overlay);
            opacity: 0;
            visibility: hidden;
            transition: opacity .25s ease, visibility .25s
        }

        body.sb-open .scrim {
            opacity: 1;
            visibility: visible
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: var(--sb-w);
            max-width: 86vw;
            z-index: 40;
            background: var(--blue);
            box-shadow: 6px 0 24px rgba(0, 0, 0, .25);
            padding: 12px 12px 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            overflow-y: auto;
            transform: translateX(-105%);
            visibility: hidden;
            transition: transform .25s ease, visibility .25s
        }

        body.sb-open .sidebar {
            transform: none;
            visibility: visible
        }

        .sb-head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 0 12px;
            min-height: 48px
        }

        .sb-head .brand-name {
            flex: 1
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 42px;
            padding: 11px 12px;
            border-radius: 10px;
            color: #fff;
            font-weight: 500;
            font-size: 13px;
            line-height: 16px
        }

        .menu a:hover {
            background: rgba(255, 255, 255, .14)
        }

        .menu a.active {
            background: #fff;
            color: #000
        }

        /* ===== Konten ===== */
        main {
            padding: calc(var(--top-h) + 36px) 24px 48px
        }

        .content {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 18px
        }

        .page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 59px
        }

        .page-head h1 {
            margin: 0 0 4px;
            font-weight: 700;
            font-size: 32px;
            line-height: 40px
        }

        .page-head p {
            margin: 0;
            font-size: 12px;
            line-height: 15px;
            color: var(--muted)
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid transparent;
            font-weight: 500;
            font-size: 12px;
            line-height: 15px;
            white-space: nowrap
        }

        .btn.primary {
            background: var(--blue);
            color: #fff;
            padding: 11px 22px
        }

        .btn.white {
            background: #fff;
            color: var(--blue);
            padding: 10px 22px
        }

        .btn.ghost {
            background: rgba(255, 255, 255, .15);
            border-color: #fff;
            color: #fff;
            padding: 10px 22px
        }

        .btn.outline {
            background: #fff;
            border-color: var(--blue);
            color: var(--blue);
            padding: 7px 14px
        }

        .btn.sm {
            padding: 7px 18px
        }

        .btn:hover {
            filter: brightness(.95)
        }

        .btn:focus-visible,
        a:focus-visible,
        button:focus-visible {
            outline: 3px solid rgba(10, 135, 217, .45);
            outline-offset: 2px
        }

        .banner {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 26px 32px;
            background: var(--blue);
            border-radius: 20px;
            color: #fff
        }

        .banner .txt {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        .banner h2 {
            margin: 0;
            font-weight: 700;
            font-size: 22px;
            line-height: 28px
        }

        .banner p {
            margin: 0;
            font-size: 12px;
            line-height: 15px;
            color: rgba(255, 255, 255, .92)
        }

        .banner .actions {
            display: flex;
            gap: 10px;
            flex: none
        }

        .stack {
            display: flex;
            align-items: center;
            flex: none
        }

        .stack .avatar {
            width: 56px;
            height: 56px;
            margin: 0 -14px
        }

        .stack .avatar:first-child {
            margin-left: 0
        }

        .stack .avatar:last-child {
            margin-right: 0
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px
        }

        .stat,
        .card {
            background: #fff;
            box-shadow: var(--shadow);
            border-radius: 16px
        }

        .stat {
            padding: 18px 22px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-height: 111px
        }

        .stat .k {
            font-weight: 500;
            font-size: 11px;
            line-height: 14px;
            color: var(--muted)
        }

        .stat .v {
            font-weight: 700;
            font-size: 28px;
            line-height: 35px
        }

        .stat .h {
            font-weight: 500;
            font-size: 11px;
            line-height: 14px
        }

        .cols {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 336px;
            gap: 24px;
            align-items: start
        }

        .col {
            display: flex;
            flex-direction: column;
            gap: 18px;
            min-width: 0
        }

        .card {
            padding: 20px 22px;
            display: flex;
            flex-direction: column;
            gap: 14px
        }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px
        }

        .card-head h3 {
            margin: 0;
            font-weight: 700;
            font-size: 15px;
            line-height: 19px
        }

        .card-head a {
            font-weight: 500;
            font-size: 11px;
            line-height: 14px;
            color: var(--blue)
        }

        .card-head a:hover {
            text-decoration: underline
        }

        .row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 12px;
            background: var(--soft);
            border-radius: 12px
        }

        .row .ic {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            flex: none;
            color: #fff
        }

        .row .ic.ring {
            border: 1px solid #fff
        }

        .row .av {
            width: 44px;
            height: 44px
        }

        .row .tx {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 3px
        }

        .row .nm {
            font-weight: 700;
            font-size: 13px;
            line-height: 16px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .row .ds {
            font-weight: 400;
            font-size: 11px;
            line-height: 14px;
            color: rgba(15, 23, 42, .5);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .row .tx.chips {
            gap: 6px
        }

        .chips-line {
            display: flex;
            flex-wrap: wrap;
            gap: 6px
        }

        .chip {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            background: var(--chip);
            color: var(--chip-ink);
            font-weight: 500;
            font-size: 10px;
            line-height: 13px
        }

        .chip.need {
            background: #fff;
            border: 1px solid var(--blue);
            color: var(--warn)
        }

        .empty {
            margin: 0;
            font-size: 12px;
            color: var(--muted)
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 12px
        }

        .profile .avatar {
            width: 52px;
            height: 52px;
            background: #14B8A6;
            font-size: 19.76px;
            line-height: 25px
        }

        .profile .nm {
            font-weight: 700;
            font-size: 14px;
            line-height: 18px
        }

        .profile .sub {
            font-size: 11px;
            line-height: 14px;
            color: var(--muted)
        }

        .prog {
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .prog .lab {
            display: flex;
            justify-content: space-between;
            font-weight: 500;
            font-size: 11px;
            line-height: 14px
        }

        .prog .lab b {
            color: var(--blue);
            font-weight: 700
        }

        .track {
            height: 8px;
            background: var(--track);
            border-radius: 4px;
            overflow: hidden
        }

        .track i {
            display: block;
            height: 100%;
            background: var(--blue);
            border-radius: 4px
        }

        .card .btn.block {
            width: 100%;
            padding: 9px 16px
        }

        .dl {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 46px
        }

        .dl .date {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--chip);
            color: var(--chip-ink);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: none
        }

        .dl .date b {
            font-weight: 700;
            font-size: 15px;
            line-height: 17px
        }

        .dl .date span {
            font-weight: 500;
            font-size: 9px;
            line-height: 11px;
            letter-spacing: .04em
        }

        .dl .nm {
            flex: 1;
            min-width: 0;
            font-weight: 500;
            font-size: 12px;
            line-height: 15px
        }

        .notif {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12px;
            line-height: 15px
        }

        .notif i {
            flex: none;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--blue);
            margin-top: 4px
        }

        /* ===== Responsif ===== */
        @media (max-width:1180px) {
            .cols {
                grid-template-columns: 1fr
            }

            .stats {
                grid-template-columns: repeat(2, 1fr)
            }

            .stack {
                display: none
            }
        }

        @media (max-width:900px) {
            .search {
                display: none
            }

            .topbar {
                padding: 0 16px 0 12px
            }

            main {
                padding: calc(var(--top-h) + 20px) 16px 32px
            }
        }

        @media (max-width:640px) {
            .page-head {
                flex-direction: column;
                align-items: flex-start
            }

            .page-head h1 {
                font-size: 26px;
                line-height: 32px
            }

            .banner {
                flex-direction: column;
                align-items: flex-start;
                padding: 22px 20px
            }

            .banner h2 {
                font-size: 19px;
                line-height: 25px
            }

            .stats {
                gap: 12px
            }

            .stat {
                padding: 14px 16px
            }

            .me-text {
                display: none
            }

            .row {
                flex-wrap: wrap
            }

            .row .btn {
                margin-left: 58px
            }
        }

        @media (max-width:420px) {
            .stats {
                grid-template-columns: 1fr
            }

            .topbar .brand-name {
                display: none
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .sidebar,
            .scrim {
                transition: none
            }
        }
    </style>
</head>

<body>
    {{-- Topbar --}}
    <header class="topbar">
        <span class="logo">
            <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Skill Link">
        </span>

        <button class="burger" type="button" id="open-sb"
            aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <span class="brand-name">Skill Link</span>

        @if (request()->routeIs('competition.manage.index'))
            <form class="search" method="GET" action="{{ route('competition.manage.index') }}" role="search">
                <i data-lucide="search" class="i" aria-hidden="true"></i>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari lomba, tim, atau skill..." aria-label="Cari lomba">
                @foreach (request()->query() as $queryKey => $queryValue)
                    @if ($queryKey !== 'q' && ! is_array($queryValue))
                        <input type="hidden" name="{{ $queryKey }}" value="{{ $queryValue }}">
                    @endif
                @endforeach
            </form>
        @else
            <label class="search">
                <i data-lucide="search" class="i" aria-hidden="true"></i>
                <input type="search" placeholder="Cari lomba, tim, atau skill..." aria-label="Cari">
            </label>
        @endif

        <div class="tb-right">
            <button class="icon-btn" type="button" aria-label="Notifikasi">
                <i data-lucide="bell" class="i" aria-hidden="true"></i>
                @if(!empty($notifs))<span class="dot"></span>@endif
            </button>
            <div class="me">
                <span class="avatar" aria-hidden="true">
                    <i data-lucide="user" class="i" style="width:20px;height:20px"></i>
                </span>
                <span class="me-text">
                    <span class="me-name">{{ $user['name'] }}</span>
                    <span class="me-sub">{{ $user['subline'] ?? (($user['prodi'] ?? 'TRPL') . ' · Angkatan ' . ($user['angkatan'] ?? '')) }}</span>
                </span>
            </div>
        </div>
    </header>

    {{-- Overlay gelap (klik untuk menutup) --}}
    <div class="scrim" id="scrim"></div>

    {{-- Sidebar (terbuka sebagai overlay) --}}
    <aside class="sidebar" id="sidebar" aria-label="Menu utama">
        <div class="sb-head">
            <span class="logo">
                <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Skill Link">
            </span>
            <span class="brand-name">Skill Link</span>
            <button class="burger" type="button" id="close-sb" aria-label="Tutup menu">
                <span></span><span></span><span></span>
            </button>
        </div>

        <nav class="menu">
            @foreach($menu as [$label, $href, $icon, $pattern])
            <a href="{{ $href }}"
                class="{{ request()->is($pattern) ? 'active' : '' }}"
                title="{{ $label }}">
                <i data-lucide="{{ $icon }}" class="i"></i>
                <span class="lbl">{{ $label }}</span>
            </a>
            @endforeach
        </nav>
    </aside>

    <main>
        @yield('content')
    </main>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        (function() {
            var body = document.body,
                openBtn = document.getElementById('open-sb'),
                closeBtn = document.getElementById('close-sb'),
                scrim = document.getElementById('scrim');

            function setOpen(open) {
                body.classList.toggle('sb-open', open);
                openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                (open ? closeBtn : openBtn).focus({ preventScroll: true });
            }

            openBtn.addEventListener('click', function() { setOpen(true); });
            closeBtn.addEventListener('click', function() { setOpen(false); });
            scrim.addEventListener('click', function() { setOpen(false); });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && body.classList.contains('sb-open')) setOpen(false);
            });
        })();

        if (window.lucide) lucide.createIcons();
    </script>
</body>

</html>
