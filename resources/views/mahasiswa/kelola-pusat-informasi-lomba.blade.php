@extends('layouts.app')

@section('title', 'Kelola Pusat Informasi Lomba · Skill Link')

@section('content')
@php
    $statusOptions = [
        'all' => 'Semua',
        'active' => 'Aktif',
        'draft' => 'Draf',
        'ended' => 'Berakhir',
    ];
@endphp

<style>
    .kil-page {
        --kil-blue: #0B88DB;
        --kil-ink: #0F1A2E;
        --kil-muted: #5B6B82;
        --kil-border: #B9D3F2;
        --kil-soft: #F3F7FE;
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
        color: var(--kil-ink);
    }

    .kil-page *, .kil-dialog *, .kil-confirm-dialog * { box-sizing: border-box; }
    .kil-page button, .kil-dialog button, .kil-confirm-dialog button,
    .kil-page input, .kil-page select, .kil-page textarea, .kil-dialog input,
    .kil-dialog select, .kil-dialog textarea { font: inherit; }
    .kil-page .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }

    .kil-header { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
    .kil-heading h1 { margin: 0 0 5px; font-size: clamp(24px, 3vw, 32px); line-height: 1.25; font-weight: 800; }
    .kil-heading p { margin: 0; color: var(--kil-muted); font-size: 13px; line-height: 1.5; }
    .kil-button { min-height: 38px; padding: 9px 16px; border: 1px solid transparent; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-size: 12px; font-weight: 700; text-decoration: none; cursor: pointer; transition: background .15s ease, border-color .15s ease, transform .15s ease; }
    .kil-button:hover { transform: translateY(-1px); }
    .kil-button:focus-visible, .kil-page a:focus-visible, .kil-page input:focus-visible,
    .kil-page select:focus-visible, .kil-dialog button:focus-visible, .kil-dialog input:focus-visible,
    .kil-dialog select:focus-visible, .kil-dialog textarea:focus-visible,
    .kil-confirm-dialog button:focus-visible { outline: 3px solid rgba(11, 136, 219, .28); outline-offset: 2px; }
    .kil-button-primary { background: var(--kil-blue); color: #fff; }
    .kil-button-primary:hover { background: #0877bf; }
    .kil-button-outline { background: #fff; color: var(--kil-blue); border-color: var(--kil-blue); }
    .kil-button-quiet { background: #EFF6FF; color: var(--kil-ink); border-color: #D9E8F8; }
    .kil-button-danger { background: #FFF5F4; color: #B42318; border-color: #FECACA; }
    .kil-button-small { min-height: 31px; padding: 6px 10px; font-size: 11px; }

    .kil-flash { padding: 12px 15px; border-radius: 11px; font-size: 13px; line-height: 1.5; }
    .kil-flash-success { background: #EAF8F0; border: 1px solid #A9E5C2; color: #067647; }
    .kil-flash-error { background: #FFF4F2; border: 1px solid #FECACA; color: #B42318; }
    .kil-flash ul { margin: 7px 0 0; padding-left: 20px; }

    .kil-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
    .kil-stat { min-height: 112px; padding: 17px 20px; display: flex; flex-direction: column; gap: 5px; background: #fff; border-radius: 15px; box-shadow: 0 4px 16px rgba(10, 51, 102, .08); text-decoration: none; transition: transform .15s ease, box-shadow .15s ease; }
    .kil-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(10, 51, 102, .12); }
    .kil-stat-label { color: var(--kil-muted); font-size: 12px; }
    .kil-stat-value { font-size: 30px; line-height: 1.15; font-weight: 800; }
    .kil-stat-hint { color: var(--kil-blue); font-size: 11px; }

    .kil-filter-card { padding: 16px; background: #fff; border: 1px solid #D8E7F8; border-radius: 15px; box-shadow: 0 4px 16px rgba(10, 51, 102, .05); }
    .kil-filter-form { display: flex; gap: 12px; }
    .kil-search-box, .kil-sort-box { min-height: 44px; display: flex; align-items: center; gap: 10px; padding: 0 13px; background: #fff; border: 1px solid #B9D3F2; border-radius: 11px; }
    .kil-search-box { flex: 1; min-width: 0; }
    .kil-search-box svg { width: 17px; height: 17px; color: #64748B; flex: none; }
    .kil-search-box input { min-width: 0; width: 100%; border: 0; outline: 0; background: transparent; color: var(--kil-ink); font-size: 13px; }
    .kil-search-box input::placeholder { color: #78869A; }
    .kil-sort-box { width: 205px; flex: none; }
    .kil-sort-box select { width: 100%; border: 0; outline: 0; background: transparent; color: var(--kil-ink); cursor: pointer; font-size: 12px; }
    .kil-filter-footer { margin-top: 14px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .kil-filter-chips { display: flex; flex-wrap: wrap; gap: 8px; }
    .kil-filter-chip { min-height: 29px; padding: 6px 13px; display: inline-flex; align-items: center; border-radius: 20px; background: #F0F5FC; color: var(--kil-ink); font-size: 11px; text-decoration: none; transition: background .15s ease, color .15s ease; }
    .kil-filter-chip:hover { background: #DDECFB; }
    .kil-filter-chip.is-active { background: var(--kil-blue); color: #fff; }
    .kil-result-count { color: var(--kil-muted); font-size: 11px; white-space: nowrap; }

    .kil-columns { display: grid; grid-template-columns: minmax(0, 1fr) 290px; gap: 18px; align-items: start; }
    .kil-list-panel, .kil-side-card { background: #fff; border: 1px solid #D8E7F8; border-radius: 15px; box-shadow: 0 4px 16px rgba(10, 51, 102, .06); }
    .kil-list-panel { padding: 18px; }
    .kil-panel-heading { margin: 0 0 14px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .kil-panel-heading h2, .kil-side-heading { margin: 0; font-size: 16px; line-height: 1.4; font-weight: 800; }
    .kil-panel-heading span { color: var(--kil-muted); font-size: 11px; }
    .kil-competition-list { display: flex; flex-direction: column; gap: 11px; }
    .kil-competition { display: grid; grid-template-columns: 54px minmax(0, 1fr) auto; gap: 13px; align-items: start; padding: 14px; border: 1px solid var(--kil-border); border-radius: 13px; }
    .kil-poster { width: 54px; height: 54px; display: grid; place-items: center; border-radius: 12px; overflow: hidden; background: var(--kil-soft); color: var(--kil-blue); font-size: 18px; font-weight: 800; }
    .kil-poster img { width: 100%; height: 100%; object-fit: cover; }
    .kil-competition-main { min-width: 0; }
    .kil-competition-title { margin: 0; overflow-wrap: anywhere; font-size: 14px; line-height: 1.4; font-weight: 800; }
    .kil-competition-organizer { margin: 3px 0 9px; color: var(--kil-muted); font-size: 11px; line-height: 1.5; }
    .kil-tags { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
    .kil-tag { min-height: 22px; padding: 4px 8px; display: inline-flex; align-items: center; border: 1px solid #D7E4F3; border-radius: 20px; background: #F4F8FD; color: #41627E; font-size: 10px; line-height: 1.3; }
    .kil-tag-deadline { background: #FFF7E8; border-color: #FDE2AE; color: #9A5700; }
    .kil-tag-ended { background: #FFF2F0; border-color: #FBC8C3; color: #B42318; }
    .kil-status { min-height: 23px; padding: 4px 9px; border-radius: 20px; white-space: nowrap; font-size: 10px; line-height: 1.3; font-weight: 700; }
    .kil-status-active { background: #E3F8EE; color: #0E8A5F; }
    .kil-status-draft { background: #FFF1DC; color: #9A5700; }
    .kil-status-ended { background: #F1F4F8; color: #5B6B82; }
    .kil-competition-actions { grid-column: 2 / -1; display: flex; flex-wrap: wrap; gap: 7px; margin-top: 1px; }
    .kil-inline-form { display: inline-flex; margin: 0; }
    .kil-open-link { align-self: center; color: var(--kil-blue); font-size: 11px; font-weight: 700; text-decoration: none; }
    .kil-open-link:hover { text-decoration: underline; }
    .kil-empty { padding: 34px 18px; text-align: center; }
    .kil-empty-icon { width: 48px; height: 48px; margin: 0 auto 12px; display: grid; place-items: center; border-radius: 14px; background: #EAF4FD; color: var(--kil-blue); }
    .kil-empty-icon svg { width: 23px; height: 23px; }
    .kil-empty h3 { margin: 0 0 5px; font-size: 14px; }
    .kil-empty p { margin: 0; color: var(--kil-muted); font-size: 12px; line-height: 1.5; }
    .kil-empty .kil-button { margin-top: 14px; }

    .kil-pagination { margin-top: 16px; padding-top: 13px; border-top: 1px solid #E3ECF6; display: flex; justify-content: space-between; align-items: center; gap: 10px; color: var(--kil-muted); font-size: 11px; }
    .kil-page-links { display: flex; align-items: center; gap: 7px; }
    .kil-page-links a, .kil-page-links span { min-width: 30px; min-height: 30px; padding: 7px 9px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #D5E4F3; border-radius: 8px; background: #fff; color: var(--kil-ink); font-size: 11px; text-decoration: none; }
    .kil-page-links a:hover { border-color: var(--kil-blue); color: var(--kil-blue); }
    .kil-page-links .is-current { background: var(--kil-blue); border-color: var(--kil-blue); color: #fff; }
    .kil-page-links .is-disabled { color: #9AA8B8; background: #F6F8FB; }

    .kil-aside { display: flex; flex-direction: column; gap: 14px; }
    .kil-side-card { padding: 16px; }
    .kil-side-heading { margin-bottom: 13px; font-size: 14px; }
    .kil-side-list { display: flex; flex-direction: column; gap: 11px; }
    .kil-side-item { display: flex; gap: 10px; align-items: center; min-width: 0; }
    .kil-date-box { width: 42px; height: 42px; display: flex; flex-direction: column; align-items: center; justify-content: center; flex: none; border-radius: 11px; background: #EAF4FD; color: #0A6DB0; }
    .kil-date-box strong { font-size: 14px; line-height: 1.1; }
    .kil-date-box span { margin-top: 2px; font-size: 8px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
    .kil-side-copy { min-width: 0; }
    .kil-side-copy strong { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 11px; line-height: 1.4; }
    .kil-side-copy span { display: block; margin-top: 2px; color: var(--kil-muted); font-size: 10px; line-height: 1.4; }
    .kil-side-empty { margin: 0; color: var(--kil-muted); font-size: 11px; line-height: 1.5; }
    .kil-action-item { padding: 9px 0; border-top: 1px solid #EAF0F7; }
    .kil-action-item:first-of-type { padding-top: 0; border-top: 0; }
    .kil-action-item a { color: var(--kil-ink); font-size: 11px; font-weight: 700; text-decoration: none; }
    .kil-action-item a:hover { color: var(--kil-blue); }
    .kil-action-item p { margin: 3px 0 0; color: var(--kil-muted); font-size: 10px; line-height: 1.45; }

    .kil-dialog { width: min(720px, calc(100% - 28px)); max-height: min(90vh, 850px); padding: 0; border: 0; border-radius: 18px; background: #fff; color: var(--kil-ink); box-shadow: 0 22px 65px rgba(15, 26, 46, .28); }
    .kil-dialog::backdrop, .kil-confirm-dialog::backdrop { background: rgba(15, 26, 46, .55); backdrop-filter: blur(2px); }
    .kil-dialog-inner { padding: 22px; }
    .kil-dialog-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 17px; }
    .kil-dialog-header h2 { margin: 0 0 4px; font-size: 20px; line-height: 1.3; }
    .kil-dialog-header p { margin: 0; color: var(--kil-muted); font-size: 11px; line-height: 1.5; }
    .kil-dialog-close { width: 34px; height: 34px; flex: none; display: grid; place-items: center; border: 0; border-radius: 9px; background: #F1F5F9; color: var(--kil-ink); cursor: pointer; }
    .kil-dialog-close svg { width: 17px; height: 17px; }
    .kil-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 13px 14px; }
    .kil-field { min-width: 0; display: flex; flex-direction: column; gap: 6px; }
    .kil-field-full { grid-column: 1 / -1; }
    .kil-field label { color: var(--kil-ink); font-size: 11px; font-weight: 700; }
    .kil-field input, .kil-field select, .kil-field textarea { width: 100%; min-height: 40px; padding: 9px 11px; border: 1px solid #C7D6E7; border-radius: 9px; background: #fff; color: var(--kil-ink); font-size: 12px; }
    .kil-field textarea { min-height: 94px; resize: vertical; line-height: 1.5; }
    .kil-field small { color: var(--kil-muted); font-size: 10px; line-height: 1.4; }
    .kil-field .kil-field-error { color: #B42318; }
    .kil-dialog-note { margin: 13px 0 0; padding: 10px 12px; border-radius: 9px; background: #F1F7FE; color: #41627E; font-size: 10px; line-height: 1.5; }
    .kil-dialog-actions { margin-top: 17px; padding-top: 14px; border-top: 1px solid #E4ECF5; display: flex; justify-content: flex-end; gap: 9px; }
    .kil-confirm-dialog { width: min(400px, calc(100% - 28px)); padding: 0; border: 0; border-radius: 17px; background: #fff; color: var(--kil-ink); box-shadow: 0 22px 65px rgba(15, 26, 46, .28); text-align: center; }
    .kil-confirm-content { padding: 26px 24px 22px; }
    .kil-confirm-icon { width: 52px; height: 52px; margin: 0 auto 14px; display: grid; place-items: center; border-radius: 50%; background: #DCEEFC; color: var(--kil-blue); }
    .kil-confirm-icon svg { width: 25px; height: 25px; }
    .kil-confirm-content h2 { margin: 0 0 7px; font-size: 18px; }
    .kil-confirm-content p { margin: 0; color: var(--kil-muted); font-size: 12px; line-height: 1.55; }
    .kil-confirm-actions { margin-top: 20px; display: flex; justify-content: center; gap: 9px; }

    @media (max-width: 1050px) {
        .kil-columns { grid-template-columns: minmax(0, 1fr) 260px; }
    }
    @media (max-width: 850px) {
        .kil-columns { grid-template-columns: 1fr; }
        .kil-aside { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 640px) {
        .kil-page { gap: 15px; }
        .kil-header { align-items: flex-start; flex-direction: column; }
        .kil-header .kil-button { width: 100%; }
        .kil-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .kil-stat { min-height: 100px; padding: 14px; }
        .kil-stat-value { font-size: 26px; }
        .kil-filter-form { flex-direction: column; }
        .kil-sort-box { width: 100%; }
        .kil-filter-footer { align-items: flex-start; flex-direction: column; }
        .kil-result-count { align-self: flex-end; }
        .kil-list-panel { padding: 13px; }
        .kil-competition { grid-template-columns: 44px minmax(0, 1fr); gap: 10px; padding: 11px; }
        .kil-poster { width: 44px; height: 44px; }
        .kil-competition-status { grid-column: 2; grid-row: 1; justify-self: start; margin-bottom: -4px; }
        .kil-competition-main { grid-column: 2; }
        .kil-competition-actions { grid-column: 2; }
        .kil-open-link { grid-column: 2; }
        .kil-aside { grid-template-columns: 1fr; }
        .kil-pagination { align-items: flex-start; flex-direction: column; }
        .kil-pagination > span { order: 2; }
        .kil-page-links { width: 100%; justify-content: space-between; }
        .kil-form-grid { grid-template-columns: 1fr; }
        .kil-field-full { grid-column: auto; }
        .kil-dialog-inner { padding: 17px; }
        .kil-dialog-actions .kil-button { flex: 1; }
    }
    @media (prefers-reduced-motion: reduce) {
        .kil-button, .kil-stat { transition: none; }
    }
</style>

<div class="kil-page">
    <header class="kil-header">
        <div class="kil-heading">
            <h1>Kelola Pusat Informasi Lomba</h1>
            <p>Tambah, ubah, dan terbitkan informasi lomba untuk mahasiswa TRPL.</p>
        </div>
        <button class="kil-button kil-button-primary" type="button" data-open-competition>
            <i data-lucide="plus" class="i" aria-hidden="true"></i>
            Tambah Lomba
        </button>
    </header>

    @if (session('success'))
        <div class="kil-flash kil-flash-success" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="kil-flash kil-flash-error" role="alert">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="kil-flash kil-flash-error" role="alert">
            Periksa kembali data lomba. Ada bagian yang perlu diperbaiki.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="kil-stats" aria-label="Ringkasan lomba">
        <a class="kil-stat" href="{{ route('competition.manage.index', ['status' => 'all']) }}">
            <span class="kil-stat-label">Total Lomba</span>
            <span class="kil-stat-value">{{ $stats['total'] }}</span>
            <span class="kil-stat-hint">Semua informasi</span>
        </a>
        <a class="kil-stat" href="{{ route('competition.manage.index', ['status' => 'active']) }}">
            <span class="kil-stat-label">Aktif</span>
            <span class="kil-stat-value">{{ $stats['active'] }}</span>
            <span class="kil-stat-hint">Tampil ke mahasiswa</span>
        </a>
        <a class="kil-stat" href="{{ route('competition.manage.index', ['status' => 'draft']) }}">
            <span class="kil-stat-label">Draf</span>
            <span class="kil-stat-value">{{ $stats['draft'] }}</span>
            <span class="kil-stat-hint">Belum diterbitkan</span>
        </a>
        <a class="kil-stat" href="{{ route('competition.manage.index', ['status' => 'ended']) }}">
            <span class="kil-stat-label">Berakhir</span>
            <span class="kil-stat-value">{{ $stats['ended'] }}</span>
            <span class="kil-stat-hint">Sudah lewat deadline</span>
        </a>
    </section>

    <section class="kil-filter-card" aria-label="Cari dan filter lomba">
        <form class="kil-filter-form" id="kil-filter-form" method="GET" action="{{ route('competition.manage.index') }}">
            <label class="kil-search-box">
                <i data-lucide="search" class="i" aria-hidden="true"></i>
                <input type="search" name="q" value="{{ $filters['search'] }}" placeholder="Cari nama lomba atau penyelenggara..." aria-label="Cari nama lomba atau penyelenggara">
            </label>
            <label class="kil-sort-box">
                <span class="sr-only">Urutkan lomba</span>
                <select name="sort" id="kil-sort-select" aria-label="Urutkan lomba">
                    <option value="nearest" @selected($filters['sort'] === 'nearest')>Deadline terdekat</option>
                    <option value="farthest" @selected($filters['sort'] === 'farthest')>Deadline terjauh</option>
                    <option value="recent" @selected($filters['sort'] === 'recent')>Paling baru ditambahkan</option>
                </select>
            </label>
            <input type="hidden" name="status" value="{{ $filters['status'] }}">
        </form>
        <div class="kil-filter-footer">
            <nav class="kil-filter-chips" aria-label="Filter status lomba">
                @foreach ($statusOptions as $statusKey => $statusLabel)
                    <a class="kil-filter-chip {{ $filters['status'] === $statusKey ? 'is-active' : '' }}"
                        href="{{ route('competition.manage.index', ['q' => $filters['search'] ?: null, 'sort' => $filters['sort'], 'status' => $statusKey]) }}"
                        @if ($filters['status'] === $statusKey) aria-current="page" @endif>{{ $statusLabel }}</a>
                @endforeach
            </nav>
            <span class="kil-result-count">{{ $competitions->total() }} lomba ditemukan</span>
        </div>
    </section>

    <div class="kil-columns">
        <section class="kil-list-panel" aria-labelledby="kil-list-title">
            <div class="kil-panel-heading">
                <h2 id="kil-list-title">Daftar Lomba</h2>
                <span>{{ $competitions->count() }} ditampilkan</span>
            </div>

            @if ($competitions->isNotEmpty())
                <div class="kil-competition-list">
                    @foreach ($competitions as $competition)
                        @php
                            $effectiveStatus = $competition->effective_status;
                            $statusLabel = $statusOptions[$effectiveStatus] ?? 'Draf';
                            $daysLeft = today()->diffInDays($competition->deadline, false);
                            $editCompetition = $competition->only([
                                'name',
                                'organizer',
                                'description',
                                'category',
                                'level',
                                'opens_at',
                                'deadline',
                                'fee',
                                'registration_url',
                                'status',
                            ]);
                        @endphp
                        <article class="kil-competition">
                            <div class="kil-poster" aria-hidden="true">
                                @if ($competition->poster_path)
                                    <img src="{{ asset('storage/'.$competition->poster_path) }}" alt="">
                                @else
                                    <i data-lucide="trophy" class="i" aria-hidden="true"></i>
                                @endif
                            </div>
                            <div class="kil-competition-main">
                                <h3 class="kil-competition-title">{{ $competition->name }}</h3>
                                <p class="kil-competition-organizer">{{ $competition->organizer }} · {{ $competition->level }}</p>
                                <div class="kil-tags">
                                    <span class="kil-tag">{{ $competition->category }}</span>
                                    <span class="kil-tag {{ $effectiveStatus === 'ended' ? 'kil-tag-ended' : 'kil-tag-deadline' }}">
                                        @if ($effectiveStatus === 'ended')
                                            Berakhir {{ $competition->deadline->format('d M Y') }}
                                        @else
                                            Deadline {{ $competition->deadline->format('d M Y') }} · {{ $daysLeft === 0 ? 'hari ini' : $daysLeft.' hari lagi' }}
                                        @endif
                                    </span>
                                    <span class="kil-tag">{{ $competition->fee > 0 ? 'Rp'.number_format($competition->fee, 0, ',', '.') : 'Gratis' }}</span>
                                </div>
                            </div>
                            <span class="kil-status kil-status-{{ $effectiveStatus }} kil-competition-status">{{ $statusLabel }}</span>
                            <div class="kil-competition-actions">
                                <button class="kil-button kil-button-outline kil-button-small" type="button"
                                    data-edit-competition
                                    data-id="{{ $competition->id }}"
                                    data-competition='@json($editCompetition)'>
                                    <i data-lucide="pencil" class="i" aria-hidden="true" style="width:13px;height:13px"></i>
                                    Edit
                                </button>
                                @if ($competition->status === 'draft')
                                    <form class="kil-inline-form" method="POST" action="{{ route('competition.manage.publish', $competition) }}"
                                        data-confirm="Terbitkan lomba ini? Lomba akan langsung terlihat oleh mahasiswa jika informasinya lengkap.">
                                        @csrf
                                        <button class="kil-button kil-button-quiet kil-button-small" type="submit">
                                            <i data-lucide="send" class="i" aria-hidden="true" style="width:13px;height:13px"></i>
                                            Terbitkan
                                        </button>
                                    </form>
                                @endif
                                <form class="kil-inline-form" method="POST" action="{{ route('competition.manage.destroy', $competition) }}"
                                    data-confirm="Hapus informasi lomba '{{ $competition->name }}'? Tindakan ini tidak dapat dibatalkan.">
                                    @csrf
                                    @method('DELETE')
                                    <button class="kil-button kil-button-danger kil-button-small" type="submit">
                                        <i data-lucide="trash-2" class="i" aria-hidden="true" style="width:13px;height:13px"></i>
                                        Hapus
                                    </button>
                                </form>
                                <a class="kil-open-link" href="{{ $competition->registration_url }}" target="_blank" rel="noopener noreferrer">Lihat pendaftaran ↗</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="kil-empty">
                    <span class="kil-empty-icon"><i data-lucide="search-x" aria-hidden="true"></i></span>
                    <h3>{{ $filters['search'] !== '' || $filters['status'] !== 'all' ? 'Lomba tidak ditemukan' : 'Belum ada informasi lomba' }}</h3>
                    <p>{{ $filters['search'] !== '' || $filters['status'] !== 'all' ? 'Coba ubah kata pencarian atau pilih filter status lainnya.' : 'Tambahkan informasi lomba agar mahasiswa dapat menemukan kesempatan baru.' }}</p>
                    @if ($filters['search'] !== '' || $filters['status'] !== 'all')
                        <a class="kil-button kil-button-outline" href="{{ route('competition.manage.index') }}">Hapus filter</a>
                    @else
                        <button class="kil-button kil-button-primary" type="button" data-open-competition>Tambah Lomba</button>
                    @endif
                </div>
            @endif

            @if ($competitions->lastPage() > 1)
                <div class="kil-pagination">
                    <span>Menampilkan {{ $competitions->firstItem() }}–{{ $competitions->lastItem() }} dari {{ $competitions->total() }} lomba</span>
                    <nav class="kil-page-links" aria-label="Pagination">
                        @if ($competitions->onFirstPage())
                            <span class="is-disabled" aria-disabled="true">Sebelumnya</span>
                        @else
                            <a href="{{ $competitions->previousPageUrl() }}" rel="prev">Sebelumnya</a>
                        @endif
                        <span class="is-current" aria-current="page">{{ $competitions->currentPage() }} / {{ $competitions->lastPage() }}</span>
                        @if ($competitions->hasMorePages())
                            <a href="{{ $competitions->nextPageUrl() }}" rel="next">Berikutnya</a>
                        @else
                            <span class="is-disabled" aria-disabled="true">Berikutnya</span>
                        @endif
                    </nav>
                </div>
            @endif
        </section>

        <aside class="kil-aside" aria-label="Ringkasan informasi lomba">
            <section class="kil-side-card">
                <h2 class="kil-side-heading">Deadline Terdekat</h2>
                @if ($upcomingDeadlines->isNotEmpty())
                    <div class="kil-side-list">
                        @foreach ($upcomingDeadlines as $upcomingCompetition)
                            <div class="kil-side-item">
                                <span class="kil-date-box">
                                    <strong>{{ $upcomingCompetition->deadline->format('d') }}</strong>
                                    <span>{{ $upcomingCompetition->deadline->translatedFormat('M') }}</span>
                                </span>
                                <span class="kil-side-copy">
                                    <strong>{{ $upcomingCompetition->name }}</strong>
                                    <span>{{ $upcomingCompetition->deadline->format('d M Y') }} · {{ $upcomingCompetition->status === 'draft' ? 'Draf' : 'Aktif' }}</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="kil-side-empty">Belum ada deadline yang akan datang.</p>
                @endif
            </section>

            <section class="kil-side-card">
                <h2 class="kil-side-heading">Perlu Perhatian</h2>
                @if ($needsAction->isNotEmpty())
                    @foreach ($needsAction as $actionCompetition)
                        <div class="kil-action-item">
                            <a href="{{ route('competition.manage.index', ['q' => $actionCompetition->name]) }}">{{ $actionCompetition->name }}</a>
                            <p>
                                @if ($actionCompetition->status === 'draft')
                                    Masih berupa draf. Lengkapi informasi lalu terbitkan.
                                @elseif (! $actionCompetition->isCompleteForPublication())
                                    Lengkapi informasi lomba sebelum diterbitkan.
                                @else
                                    Deadline tinggal {{ max(0, today()->diffInDays($actionCompetition->deadline, false)) }} hari.
                                @endif
                            </p>
                        </div>
                    @endforeach
                @else
                    <p class="kil-side-empty">Semua informasi lomba sudah tertata.</p>
                @endif
            </section>
        </aside>
    </div>
</div>

<dialog class="kil-dialog" id="kil-competition-dialog" aria-labelledby="kil-dialog-title">
    <div class="kil-dialog-inner">
        <div class="kil-dialog-header">
            <div>
                <h2 id="kil-dialog-title">Tambah Lomba</h2>
                <p>Isi data sesuai informasi resmi lomba. Tanda * wajib diisi.</p>
            </div>
            <button class="kil-dialog-close" type="button" data-close-competition aria-label="Tutup form">
                <i data-lucide="x" aria-hidden="true"></i>
            </button>
        </div>

        <form id="kil-competition-form" method="POST" action="{{ route('competition.manage.store') }}" enctype="multipart/form-data" data-confirm="Simpan data lomba ini? Informasi akan diperbarui di daftar lomba.">
            @csrf
            <input type="hidden" name="_method" id="kil-method" value="PUT" disabled>
            <input type="hidden" name="_form_mode" id="kil-form-mode" value="create">
            <input type="hidden" name="_competition_id" id="kil-competition-id" value="">
            <div class="kil-form-grid">
                <div class="kil-field kil-field-full">
                    <label for="kil-name">Nama Lomba *</label>
                    <input id="kil-name" name="name" type="text" maxlength="200" placeholder="Masukkan nama lomba" required>
                    @error('name') <small class="kil-field-error">{{ $message }}</small> @enderror
                </div>
                <div class="kil-field">
                    <label for="kil-organizer">Penyelenggara *</label>
                    <input id="kil-organizer" name="organizer" type="text" maxlength="200" placeholder="Masukkan penyelenggara" required>
                    @error('organizer') <small class="kil-field-error">{{ $message }}</small> @enderror
                </div>
                <div class="kil-field">
                    <label for="kil-opens-at">Mulai Pendaftaran *</label>
                    <input id="kil-opens-at" name="opens_at" type="date" required>
                    @error('opens_at') <small class="kil-field-error">{{ $message }}</small> @enderror
                </div>
                <div class="kil-field">
                    <label for="kil-deadline">Deadline Pendaftaran *</label>
                    <input id="kil-deadline" name="deadline" type="date" required>
                    @error('deadline') <small class="kil-field-error">{{ $message }}</small> @enderror
                </div>
                <div class="kil-field">
                    <label for="kil-category">Kategori *</label>
                    <input id="kil-category" name="category" type="text" list="kil-category-options" maxlength="100" placeholder="Pilih atau tulis kategori" required>
                    <datalist id="kil-category-options">
                        <option value="Hackathon"></option><option value="UI/UX"></option><option value="Esai"></option>
                        <option value="Karya Tulis Ilmiah"></option><option value="Business Plan"></option><option value="Pemrograman"></option>
                    </datalist>
                    @error('category') <small class="kil-field-error">{{ $message }}</small> @enderror
                </div>
                <div class="kil-field">
                    <label for="kil-level">Tingkat *</label>
                    <select id="kil-level" name="level" required>
                        <option value="">Pilih tingkat</option>
                        <option value="Regional">Regional</option>
                        <option value="Nasional">Nasional</option>
                        <option value="Internasional">Internasional</option>
                    </select>
                    @error('level') <small class="kil-field-error">{{ $message }}</small> @enderror
                </div>
                <div class="kil-field">
                    <label for="kil-fee">Biaya Pendaftaran (Rp) *</label>
                    <input id="kil-fee" name="fee" type="number" min="0" max="100000000" step="1000" placeholder="0 untuk gratis" required>
                    @error('fee') <small class="kil-field-error">{{ $message }}</small> @enderror
                </div>
                <div class="kil-field">
                    <label for="kil-status">Status *</label>
                    <select id="kil-status" name="status" required>
                        <option value="draft">Draf</option>
                        <option value="active">Aktif</option>
                    </select>
                    @error('status') <small class="kil-field-error">{{ $message }}</small> @enderror
                </div>
                <div class="kil-field kil-field-full">
                    <label for="kil-registration-url">Tautan Pendaftaran *</label>
                    <input id="kil-registration-url" name="registration_url" type="url" maxlength="2048" placeholder="https://" required>
                    @error('registration_url') <small class="kil-field-error">{{ $message }}</small> @enderror
                </div>
                <div class="kil-field kil-field-full">
                    <label for="kil-poster">Poster Lomba</label>
                    <input id="kil-poster" name="poster" type="file" accept="image/jpeg,image/png,image/webp">
                    <small>Format JPG, PNG, atau WebP. Maksimal 5 MB. Jika edit, pilih file baru untuk mengganti poster.</small>
                    @error('poster') <small class="kil-field-error">{{ $message }}</small> @enderror
                </div>
                <div class="kil-field kil-field-full">
                    <label for="kil-description">Deskripsi Singkat *</label>
                    <textarea id="kil-description" name="description" maxlength="300" placeholder="Maksimal 300 karakter" required></textarea>
                    <small><span id="kil-description-count">0</span> / 300 karakter</small>
                    @error('description') <small class="kil-field-error">{{ $message }}</small> @enderror
                </div>
            </div>
            <p class="kil-dialog-note" id="kil-create-note">Lomba baru akan disimpan sebagai draf. Anda dapat memeriksa kembali informasinya sebelum diterbitkan.</p>
            <div class="kil-dialog-actions">
                <button class="kil-button kil-button-outline" type="button" data-close-competition>Batal</button>
                <button class="kil-button kil-button-primary" type="submit">
                    <i data-lucide="save" class="i" aria-hidden="true" style="width:15px;height:15px"></i>
                    Simpan Lomba
                </button>
            </div>
        </form>
    </div>
</dialog>

<dialog class="kil-confirm-dialog" id="kil-confirm-dialog" aria-labelledby="kil-confirm-title" aria-describedby="kil-confirm-message">
    <div class="kil-confirm-content">
        <span class="kil-confirm-icon"><i data-lucide="help-circle" aria-hidden="true"></i></span>
        <h2 id="kil-confirm-title">Konfirmasi</h2>
        <p id="kil-confirm-message">Apakah Anda yakin ingin melanjutkan?</p>
        <div class="kil-confirm-actions">
            <button class="kil-button kil-button-outline" type="button" id="kil-confirm-cancel">Tidak</button>
            <button class="kil-button kil-button-primary" type="button" id="kil-confirm-accept">Ya, lanjutkan</button>
        </div>
    </div>
</dialog>

<dialog class="kil-dialog" id="kil-notifications-dialog" aria-labelledby="kil-notifications-title">
    <div class="kil-dialog-inner">
        <div class="kil-dialog-header">
            <div>
                <h2 id="kil-notifications-title">Notifikasi pengelolaan</h2>
                <p>Periksa informasi lomba yang membutuhkan tindak lanjut.</p>
            </div>
            <button class="kil-dialog-close" type="button" data-close-notifications aria-label="Tutup notifikasi">
                <i data-lucide="x" aria-hidden="true"></i>
            </button>
        </div>
        @if ($needsAction->isNotEmpty())
            <div class="kil-side-list">
                @foreach ($needsAction as $actionCompetition)
                    <div class="kil-side-item">
                        <span class="kil-date-box"><i data-lucide="bell" aria-hidden="true" style="width:16px;height:16px"></i></span>
                        <span class="kil-side-copy">
                            <strong>{{ $actionCompetition->name }}</strong>
                            <span>{{ $actionCompetition->status === 'draft' ? 'Draf menunggu dilengkapi atau diterbitkan' : 'Periksa deadline dan kelengkapan informasi' }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="kil-side-empty">Belum ada informasi yang membutuhkan tindak lanjut.</p>
        @endif
        <div class="kil-dialog-actions">
            <button class="kil-button kil-button-primary" type="button" data-close-notifications>Tutup</button>
        </div>
    </div>
</dialog>

<script>
    (function () {
        var competitionDialog = document.getElementById('kil-competition-dialog');
        var confirmDialog = document.getElementById('kil-confirm-dialog');
        var competitionForm = document.getElementById('kil-competition-form');
        var createUrl = @json(route('competition.manage.store'));
        var updateUrlTemplate = @json(route('competition.manage.update', ['competition' => '__COMPETITION_ID__']));
        var methodInput = document.getElementById('kil-method');
        var modeInput = document.getElementById('kil-form-mode');
        var competitionIdInput = document.getElementById('kil-competition-id');
        var createNote = document.getElementById('kil-create-note');
        var descriptionInput = document.getElementById('kil-description');
        var descriptionCount = document.getElementById('kil-description-count');
        var statusSelect = document.getElementById('kil-status');
        var activeStatusOption = statusSelect.querySelector('option[value="active"]');
        var pendingForm = null;
        var submitAfterConfirmation = false;

        function updateDescriptionCount() {
            descriptionCount.textContent = String(descriptionInput.value.length);
        }

        function setCreateMode() {
            competitionForm.reset();
            competitionForm.action = createUrl;
            methodInput.disabled = true;
            modeInput.value = 'create';
            competitionIdInput.value = '';
            document.getElementById('kil-dialog-title').textContent = 'Tambah Lomba';
            createNote.hidden = false;
            activeStatusOption.disabled = true;
            statusSelect.value = 'draft';
            updateDescriptionCount();
        }

        function setEditMode(button) {
            var competition = JSON.parse(button.dataset.competition);
            competitionForm.reset();
            competitionForm.action = updateUrlTemplate.replace('__COMPETITION_ID__', button.dataset.id);
            methodInput.disabled = false;
            modeInput.value = 'edit';
            competitionIdInput.value = button.dataset.id;
            document.getElementById('kil-dialog-title').textContent = 'Edit Informasi Lomba';
            createNote.hidden = true;
            activeStatusOption.disabled = false;
            ['name', 'organizer', 'category', 'level', 'fee', 'registration_url', 'status'].forEach(function (field) {
                var value = competition[field] == null ? '' : competition[field];
                competitionForm.elements[field].value = field === 'status' && value === 'ended' ? 'draft' : value;
            });
            ['opens_at', 'deadline'].forEach(function (field) {
                var value = competition[field];
                competitionForm.elements[field].value = value ? String(value).slice(0, 10) : '';
            });
            competitionForm.elements.description.value = competition.description || '';
            updateDescriptionCount();
        }

        document.querySelectorAll('[data-open-competition]').forEach(function (button) {
            button.addEventListener('click', function () {
                setCreateMode();
                competitionDialog.showModal();
                document.getElementById('kil-name').focus();
            });
        });

        document.querySelectorAll('[data-edit-competition]').forEach(function (button) {
            button.addEventListener('click', function () {
                setEditMode(button);
                competitionDialog.showModal();
                document.getElementById('kil-name').focus();
            });
        });

        document.querySelectorAll('[data-close-competition]').forEach(function (button) {
            button.addEventListener('click', function () {
                competitionDialog.close();
            });
        });

        descriptionInput.addEventListener('input', updateDescriptionCount);

        document.getElementById('kil-sort-select').addEventListener('change', function () {
            document.getElementById('kil-filter-form').submit();
        });

        var notificationsButton = document.querySelector('.topbar .icon-btn');
        var notificationsDialog = document.getElementById('kil-notifications-dialog');
        if (notificationsButton && notificationsDialog) {
            notificationsButton.setAttribute('aria-haspopup', 'dialog');
            notificationsButton.setAttribute('aria-controls', 'kil-notifications-dialog');
            notificationsButton.addEventListener('click', function () {
                notificationsDialog.showModal();
            });
            document.querySelectorAll('[data-close-notifications]').forEach(function (button) {
                button.addEventListener('click', function () {
                    notificationsDialog.close();
                });
            });
        }

        document.querySelectorAll('form[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (submitAfterConfirmation) {
                    submitAfterConfirmation = false;
                    return;
                }
                event.preventDefault();
                pendingForm = form;
                document.getElementById('kil-confirm-message').textContent = form.dataset.confirm;
                document.getElementById('kil-confirm-title').textContent = form.action.indexOf('/publish') !== -1 ? 'Terbitkan informasi?' : (form.method.toUpperCase() === 'POST' && form.querySelector('[name="_method"][value="DELETE"]') ? 'Hapus informasi lomba?' : 'Simpan data lomba ini?');
                document.getElementById('kil-confirm-accept').classList.toggle('kil-button-danger', Boolean(form.querySelector('[name="_method"][value="DELETE"]')));
                document.getElementById('kil-confirm-accept').classList.toggle('kil-button-primary', !form.querySelector('[name="_method"][value="DELETE"]'));
                confirmDialog.showModal();
            });
        });

        document.getElementById('kil-confirm-cancel').addEventListener('click', function () {
            pendingForm = null;
            confirmDialog.close();
        });

        document.getElementById('kil-confirm-accept').addEventListener('click', function () {
            if (!pendingForm) {
                confirmDialog.close();
                return;
            }
            var form = pendingForm;
            pendingForm = null;
            submitAfterConfirmation = true;
            confirmDialog.close();
            form.requestSubmit();
        });

        @if ($errors->any())
            var initialMode = @json(old('_form_mode', session('_form_mode', 'create')));
            var initialId = @json(old('_competition_id', session('_competition_id')));
            var oldValues = @json(old());
            if (initialMode === 'edit' && initialId) {
                var editButton = document.querySelector('[data-edit-competition][data-id="' + String(initialId).replace(/[^0-9]/g, '') + '"]');
                if (editButton) {
                    setEditMode(editButton);
                } else {
                    setCreateMode();
                }
            } else {
                setCreateMode();
            }
            Object.keys(oldValues).forEach(function (field) {
                if (field.charAt(0) === '_' || field === 'poster' || !competitionForm.elements[field]) {
                    return;
                }
                if (field === 'status' && initialMode !== 'edit') {
                    return;
                }
                competitionForm.elements[field].value = oldValues[field];
            });
            updateDescriptionCount();
            competitionDialog.showModal();
        @endif
    })();

    if (window.lucide) {
        window.lucide.createIcons();
    }
</script>
@endsection
