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

    $avatarColors = [
        'G' => '#0B88DB',
        'K' => '#0E9F6E',
        'H' => '#E05D38',
        'B' => '#7C3AED',
        'D' => '#84CC16',
    ];
@endphp

<style>
    :root,
    body,
    .kil-page,
    .kil-dialog,
    .kil-confirm-dialog {
        --kil-blue: #0B88DB;
        --kil-blue-hover: #0877bf;
        --kil-ink: #0F172A;
        --kil-muted: #64748B;
        --kil-border: #E2E8F0;
    }

    .kil-page {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
        color: var(--kil-ink);
        width: 100%;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }

    .kil-page *, .kil-dialog *, .kil-confirm-dialog * { box-sizing: border-box; }
    .kil-page button, .kil-dialog button, .kil-confirm-dialog button,
    .kil-page input, .kil-page select, .kil-page textarea, .kil-dialog input,
    .kil-dialog select, .kil-dialog textarea { font-family: inherit; }

    /* Header */
    .kil-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }
    .kil-heading h1 {
        margin: 0 0 4px;
        font-size: 24px;
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: -0.015em;
        color: #0F172A;
    }
    .kil-heading p {
        margin: 0;
        color: var(--kil-muted);
        font-size: 13px;
        line-height: 1.5;
        font-weight: 400;
    }
    
    .kil-header-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }

    /* Buttons */
    .kil-btn {
        min-height: 38px;
        padding: 8px 18px;
        border: 1px solid transparent;
        border-radius: 8px;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all .15s ease;
        white-space: nowrap;
        box-sizing: border-box;
    }
    .kil-btn:hover { transform: translateY(-1px); }
    .kil-btn:active { transform: translateY(0); }

    .kil-btn-primary {
        background-color: #0B88DB !important;
        color: #FFFFFF !important;
        border: 1px solid #0B88DB !important;
    }
    .kil-btn-primary:hover {
        background-color: #0877bf !important;
        border-color: #0877bf !important;
        color: #FFFFFF !important;
    }

    .kil-btn-outline {
        background-color: #FFFFFF !important;
        color: #0B88DB !important;
        border: 1px solid #CBD5E1 !important;
    }
    .kil-btn-outline:hover {
        border-color: #0B88DB !important;
        background-color: #F0F7FF !important;
        color: #0877bf !important;
    }

    .kil-btn-secondary {
        background-color: #FFFFFF !important;
        color: #334155 !important;
        border: 1px solid #CBD5E1 !important;
    }
    .kil-btn-secondary:hover {
        background-color: #F8FAFC !important;
        border-color: #94A3B8 !important;
        color: #0F172A !important;
    }

    .kil-btn-danger-solid {
        background-color: #EF4444 !important;
        color: #FFFFFF !important;
        border: 1px solid #EF4444 !important;
    }
    .kil-btn-danger-solid:hover {
        background-color: #DC2626 !important;
        border-color: #DC2626 !important;
        color: #FFFFFF !important;
    }

    .kil-btn-danger {
        background-color: #FFFFFF !important;
        color: #DC2626 !important;
        border: 1px solid #FECACA !important;
    }
    .kil-btn-danger:hover {
        background-color: #FEF2F2 !important;
        border-color: #F87171 !important;
        color: #B91C1C !important;
    }

    .kil-btn-sm {
        min-height: 32px;
        padding: 5px 14px;
        font-size: 12px;
        border-radius: 7px;
    }

    /* Toast notification matching Figma Image 9 */
    .kil-toast-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 9999px;
        background: #10B981;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        animation: kilFadeIn .25s ease;
    }
    .kil-toast-pill-icon {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #fff;
        color: #10B981;
        display: grid;
        place-items: center;
        font-size: 10px;
        font-weight: 900;
        flex: none;
    }

    .kil-flash-error { padding: 12px 18px; border-radius: 12px; background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; font-size: 13px; }
    .kil-flash-error ul { margin: 6px 0 0; padding-left: 18px; }

    /* 4 Stat Cards */
    .kil-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
    .kil-stat {
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        gap: 3px;
        background: #fff;
        border-radius: 14px;
        border: 1px solid var(--kil-border);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        text-decoration: none;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .kil-stat:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(10, 51, 102, .06); }
    .kil-stat-label { color: var(--kil-muted); font-size: 13px; font-weight: 500; }
    .kil-stat-value { font-size: 32px; line-height: 1.15; font-weight: 700; color: #0F172A; }
    .kil-stat-hint { color: var(--kil-muted); font-size: 11px; margin-top: 1px; font-weight: 400; }

    /* Filter Card */
    .kil-filter-card {
        padding: 16px 20px;
        background: #fff;
        border: 1px solid var(--kil-border);
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .kil-filter-top { display: flex; gap: 14px; }
    .kil-search-box {
        flex: 1;
        min-width: 0;
        min-height: 42px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0 14px;
        background: #fff;
        border: 1px solid #CBD5E1;
        border-radius: 9px;
    }
    .kil-search-box input { min-width: 0; width: 100%; border: 0; outline: 0; background: transparent; color: var(--kil-ink); font-size: 13px; font-weight: 400; }
    .kil-search-box input::placeholder { color: #94A3B8; }
    .kil-search-box svg { width: 17px; height: 17px; color: #94A3B8; flex: none; }
    
    .kil-sort-box {
        width: 220px;
        flex: none;
        min-height: 42px;
        display: flex;
        align-items: center;
        padding: 0 12px;
        background: #fff;
        border: 1px solid #CBD5E1;
        border-radius: 9px;
    }
    .kil-sort-box select { width: 100%; border: 0; outline: 0; background: transparent; color: #334155; font-size: 12px; font-weight: 500; cursor: pointer; }

    .kil-filter-bottom { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .kil-filter-chips { display: flex; gap: 8px; flex-wrap: wrap; }
    .kil-filter-chip {
        min-height: 32px;
        padding: 6px 16px;
        display: inline-flex;
        align-items: center;
        border-radius: 20px;
        background: #F1F5F9;
        color: #475569;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        transition: all .15s ease;
    }
    .kil-filter-chip:hover { background: #E2E8F0; }
    .kil-filter-chip.is-active { background: var(--kil-blue); color: #fff; font-weight: 600; }
    .kil-count-text { color: var(--kil-muted); font-size: 12px; font-weight: 500; }

    /* Columns Layout */
    .kil-columns { display: grid; grid-template-columns: minmax(0, 1fr) 280px; gap: 20px; align-items: start; }
    .kil-left-col { display: flex; flex-direction: column; gap: 12px; }
    .kil-col-title { margin: 0 0 2px; font-size: 16px; font-weight: 700; color: #0F172A; }

    /* Competition Item Card */
    .kil-card {
        background: #fff;
        border: 1px solid var(--kil-border);
        border-radius: 14px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        transition: border-color .15s ease;
    }
    .kil-card:hover { border-color: #B9D3F2; }
    
    .kil-card-left { display: flex; align-items: center; gap: 16px; min-width: 0; flex: 1; }
    
    .kil-avatar-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        flex: none;
    }
    .kil-avatar-box img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; }

    .kil-card-info { min-width: 0; display: flex; flex-direction: column; gap: 3px; }
    .kil-card-name { margin: 0; font-size: 15px; font-weight: 700; color: #0F172A; line-height: 1.3; }
    .kil-card-sub { margin: 0; font-size: 12px; color: var(--kil-muted); font-weight: 400; }
    .kil-card-chips { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 3px; }
    .kil-chip {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        background: #F1F5F9;
        color: #475569;
        border: 1px solid var(--kil-border);
    }
    .kil-chip-deadline { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }
    .kil-chip-ended { background: #F1F5F9; color: #64748B; border-color: var(--kil-border); }

    .kil-card-right { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex: none; }
    
    .kil-status-badge {
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.2;
    }
    .kil-status-active { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .kil-status-draft { background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A; }
    .kil-status-ended { background: #F1F5F9; color: #64748B; border: 1px solid var(--kil-border); }

    .kil-card-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }

    /* Right Sidebar Cards */
    .kil-right-col { display: flex; flex-direction: column; gap: 16px; }
    .kil-side-card {
        background: #fff;
        border: 1px solid var(--kil-border);
        border-radius: 14px;
        padding: 18px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .kil-side-card h3 { margin: 0; font-size: 15px; font-weight: 700; color: #0F172A; }
    .kil-side-list { display: flex; flex-direction: column; gap: 12px; }
    .kil-side-item { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .kil-side-color-box { width: 28px; height: 28px; border-radius: 7px; flex: none; }
    .kil-side-item-text { min-width: 0; display: flex; flex-direction: column; }
    .kil-side-item-name { font-size: 13px; font-weight: 600; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .kil-side-item-date { font-size: 11px; color: var(--kil-muted); font-weight: 400; margin-top: 1px; }

    .kil-side-note { margin: 0; font-size: 12px; color: #475569; line-height: 1.5; font-weight: 400; }

    /* Modals & Dialogs (Fixed & Compact: All buttons always visible) */
    .kil-dialog {
        width: min(540px, calc(100% - 32px));
        max-height: 90vh;
        margin: auto;
        padding: 0;
        border: 0;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.3);
        overflow: hidden;
    }
    .kil-dialog::backdrop, .kil-confirm-dialog::backdrop {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(2px);
    }
    .kil-dialog-inner {
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        max-height: 90vh;
        overflow: hidden;
    }
    .kil-dialog-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
        flex: none;
    }
    .kil-dialog-header h2 { margin: 0 0 2px; font-size: 18px; font-weight: 700; color: #0F172A; }
    .kil-dialog-header p { margin: 0; font-size: 11px; color: var(--kil-muted); font-weight: 400; }
    .kil-dialog-close {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #F1F5F9;
        border: 0;
        display: grid;
        place-items: center;
        color: #64748B;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        transition: background .15s ease;
        flex: none;
    }
    .kil-dialog-close:hover { background: #E2E8F0; }

    #form-lomba {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }
    .kil-form-body {
        overflow-y: auto;
        flex: 1;
        min-height: 0;
        padding-right: 4px;
        display: flex;
        flex-direction: column;
        gap: 9px;
    }
    .kil-form-body::-webkit-scrollbar { width: 5px; }
    .kil-form-body::-webkit-scrollbar-track { background: transparent; }
    .kil-form-body::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }

    .kil-form-group { display: flex; flex-direction: column; gap: 4px; }
    .kil-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .kil-label { font-size: 11px; font-weight: 600; color: #1E293B; }
    .kil-input, .kil-select, .kil-textarea {
        width: 100%;
        min-height: 36px;
        height: 36px;
        padding: 6px 11px;
        border: 1px solid #CBD5E1;
        border-radius: 8px;
        background: #fff;
        color: #0F172A;
        font-size: 12px;
        outline: none;
        transition: border-color .15s ease;
    }
    .kil-input:focus, .kil-select:focus, .kil-textarea:focus { border-color: var(--kil-blue); }
    .kil-textarea { min-height: 58px; height: 58px; resize: none; line-height: 1.4; padding: 7px 11px; }
    .kil-field-hint { display: flex; justify-content: flex-end; font-size: 10px; color: var(--kil-muted); margin-top: 2px; font-weight: 400; }

    /* Sticky Footer: Action buttons always visible */
    .kil-dialog-footer {
        flex: none;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 12px !important;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #E2E8F0;
        background: #fff;
    }

    /* Confirmation Modal */
    .kil-confirm-dialog {
        width: min(400px, calc(100% - 32px));
        margin: auto;
        padding: 0;
        border: 0;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.35);
        text-align: center;
        overflow: hidden;
    }
    .kil-confirm-body { padding: 28px 24px 22px; }
    .kil-confirm-icon-box {
        width: 54px;
        height: 54px;
        margin: 0 auto 16px;
        border-radius: 50%;
        display: grid;
        place-items: center;
    }
    .kil-confirm-icon-blue { background: #0B88DB; color: #fff; }
    .kil-confirm-icon-green { background: #10B981; color: #fff; }
    .kil-confirm-icon-red { background: #EF4444; color: #fff; }
    
    .kil-confirm-title { margin: 0 0 8px; font-size: 17px; font-weight: 700; color: #0F172A; }
    .kil-confirm-desc { margin: 0; font-size: 13px; color: var(--kil-muted); line-height: 1.5; font-weight: 400; }
    .kil-confirm-actions {
        margin-top: 22px;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 12px !important;
    }

    @media (max-width: 900px) {
        .kil-columns { grid-template-columns: 1fr; }
        .kil-stats { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .kil-stats { grid-template-columns: 1fr; }
        .kil-filter-top { flex-direction: column; }
        .kil-sort-box { width: 100%; }
        .kil-card { flex-direction: column; align-items: flex-start; }
        .kil-card-right { align-items: flex-start; width: 100%; }
        .kil-card-actions { width: 100%; justify-content: flex-start; }
        .kil-form-row { grid-template-columns: 1fr; }
    }
    @keyframes kilFadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="kil-page">
    {{-- Header --}}
    <header class="kil-header">
        <div class="kil-heading">
            <h1>Kelola Pusat Informasi Lomba</h1>
            <p>Tambah, ubah, dan terbitkan informasi lomba untuk mahasiswa TRPL.</p>
        </div>
        <div class="kil-header-right">
            @if (session('success'))
                <div class="kil-toast-pill" role="status" id="kil-toast">
                    <span class="kil-toast-pill-icon">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            <button class="kil-btn kil-btn-primary" type="button" id="btn-tambah-lomba">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex:none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Tambah Lomba</span>
            </button>
        </div>
    </header>

    @if ($errors->any())
        <div class="kil-flash-error" role="alert">
            <strong>Periksa kembali data yang dimasukkan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 4 Stat Cards --}}
    <section class="kil-stats" aria-label="Ringkasan Statistik Lomba">
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

    {{-- Search & Filter Bar --}}
    <section class="kil-filter-card" aria-label="Pencarian dan Filter">
        <form id="kil-filter-form" method="GET" action="{{ route('competition.manage.index') }}">
            <div class="kil-filter-top">
                <div class="kil-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="search" name="q" value="{{ $filters['search'] }}" placeholder="Cari nama lomba atau penyelenggara..." aria-label="Cari lomba">
                </div>
                <div class="kil-sort-box">
                    <select name="sort" id="kil-sort-select" onchange="document.getElementById('kil-filter-form').submit()">
                        <option value="nearest" @selected($filters['sort'] === 'nearest')>Urutkan: Deadline terdekat</option>
                        <option value="farthest" @selected($filters['sort'] === 'farthest')>Urutkan: Deadline terjauh</option>
                        <option value="recent" @selected($filters['sort'] === 'recent')>Urutkan: Terbaru</option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="status" id="kil-status-filter-input" value="{{ $filters['status'] }}">
        </form>
        <div class="kil-filter-bottom">
            <nav class="kil-filter-chips" aria-label="Filter status">
                @foreach ($statusOptions as $statusKey => $statusLabel)
                    <a class="kil-filter-chip {{ $filters['status'] === $statusKey ? 'is-active' : '' }}"
                        href="{{ route('competition.manage.index', ['q' => $filters['search'] ?: null, 'sort' => $filters['sort'], 'status' => $statusKey]) }}">
                        {{ $statusLabel }}
                    </a>
                @endforeach
            </nav>
            <span class="kil-count-text">{{ $competitions->total() }} lomba ditemukan</span>
        </div>
    </section>

    {{-- Two Columns --}}
    <div class="kil-columns">
        {{-- Left: Daftar Lomba --}}
        <section class="kil-left-col" aria-label="Daftar Lomba">
            <h2 class="kil-col-title">Daftar Lomba</h2>

            @if ($competitions->isNotEmpty())
                @foreach ($competitions as $competition)
                    @php
                        $effectiveStatus = $competition->effective_status;
                        $statusBadgeClass = match($effectiveStatus) {
                            'active' => 'kil-status-active',
                            'draft' => 'kil-status-draft',
                            default => 'kil-status-ended',
                        };
                        $statusText = match($effectiveStatus) {
                            'active' => 'Aktif',
                            'draft' => 'Draf',
                            default => 'Berakhir',
                        };
                        $firstChar = mb_strtoupper(mb_substr($competition->name, 0, 1));
                        $avatarColor = $avatarColors[$firstChar] ?? '#0B88DB';
                        $daysLeft = today()->diffInDays($competition->deadline, false);

                        $compData = [
                            'id' => $competition->id,
                            'name' => $competition->name,
                            'organizer' => $competition->organizer,
                            'deadline' => $competition->deadline->format('Y-m-d'),
                            'deadline_display' => $competition->deadline->format('d/m/Y'),
                            'category' => $competition->category,
                            'level' => $competition->level,
                            'fee' => $competition->fee == 0 ? 'Gratis' : 'Rp'.number_format($competition->fee, 0, ',', '.'),
                            'raw_fee' => $competition->fee,
                            'status' => $competition->status,
                            'registration_url' => $competition->registration_url,
                            'description' => $competition->description,
                        ];
                    @endphp

                    <article class="kil-card">
                        <div class="kil-card-left">
                            <div class="kil-avatar-box" style="background: {{ $avatarColor }}">
                                {{ $firstChar }}
                            </div>
                            <div class="kil-card-info">
                                <h3 class="kil-card-name">{{ $competition->name }}</h3>
                                <p class="kil-card-sub">{{ $competition->organizer }} · {{ $competition->level }}</p>
                                <div class="kil-card-chips">
                                    <span class="kil-chip">{{ $competition->category }}</span>
                                    <span class="kil-chip {{ $effectiveStatus === 'ended' ? 'kil-chip-ended' : 'kil-chip-deadline' }}">
                                        @if ($effectiveStatus === 'ended')
                                            Deadline {{ $competition->deadline->format('d M Y') }}
                                        @else
                                            Deadline {{ $competition->deadline->format('d M Y') }} · {{ $daysLeft <= 0 ? 'hari ini' : $daysLeft.' hari lagi' }}
                                        @endif
                                    </span>
                                    <span class="kil-chip">{{ $competition->fee > 0 ? 'Rp'.number_format($competition->fee, 0, ',', '.') : 'Gratis' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="kil-card-right">
                            <span class="kil-status-badge {{ $statusBadgeClass }}">{{ $statusText }}</span>
                            <div class="kil-card-actions">
                                {{-- Tombol Edit --}}
                                <button class="kil-btn kil-btn-outline kil-btn-sm" type="button"
                                    data-edit-competition='@json($compData)'>
                                    Edit
                                </button>

                                {{-- Tombol Terbitkan jika status Draf --}}
                                @if ($competition->status === 'draft')
                                    <button class="kil-btn kil-btn-primary kil-btn-sm" type="button"
                                        data-publish-id="{{ $competition->id }}"
                                        data-publish-name="{{ $competition->name }}"
                                        data-publish-action="{{ route('competition.manage.publish', $competition) }}">
                                        Terbitkan
                                    </button>
                                @endif

                                {{-- Tombol Hapus (Dapat menghapus lomba apa pun: Aktif, Draf, Berakhir jika tidak sesuai) --}}
                                <button class="kil-btn kil-btn-outline kil-btn-sm kil-btn-danger" type="button"
                                    data-delete-id="{{ $competition->id }}"
                                    data-delete-name="{{ $competition->name }}"
                                    data-delete-action="{{ route('competition.manage.destroy', $competition) }}">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach

                @if ($competitions->lastPage() > 1)
                    <div style="margin-top: 14px;">
                        {{ $competitions->links() }}
                    </div>
                @endif
            @else
                <div class="kil-card" style="justify-content: center; text-align: center; padding: 40px 20px;">
                    <div>
                        <p style="margin: 0; font-size: 14px; font-weight: 700; color: #0F172A;">Tidak ada lomba ditemukan</p>
                        <p style="margin: 4px 0 0; font-size: 12px; color: var(--kil-muted);">Coba ubah kata kunci pencarian atau ganti filter status.</p>
                    </div>
                </div>
            @endif
        </section>

        {{-- Right: Sidebar Cards --}}
        <aside class="kil-right-col" aria-label="Informasi Tambahan">
            {{-- Card Deadline Terdekat --}}
            <section class="kil-side-card">
                <h3>Deadline Terdekat</h3>
                @if ($upcomingDeadlines->isNotEmpty())
                    <div class="kil-side-list">
                        @foreach ($upcomingDeadlines as $upcoming)
                            @php
                                $firstC = mb_strtoupper(mb_substr($upcoming->name, 0, 1));
                                $boxColor = $avatarColors[$firstC] ?? '#0B88DB';
                            @endphp
                            <div class="kil-side-item">
                                <div class="kil-side-color-box" style="background: {{ $boxColor }}"></div>
                                <div class="kil-side-item-text">
                                    <span class="kil-side-item-name">{{ $upcoming->name }}</span>
                                    <span class="kil-side-item-date">{{ $upcoming->deadline->format('d M Y') }}{{ $upcoming->status === 'draft' ? ' (draf)' : '' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="kil-side-note">Belum ada deadline dalam waktu dekat.</p>
                @endif
            </section>

            {{-- Card Perlu Tindakan --}}
            <section class="kil-side-card">
                <h3>Perlu Tindakan</h3>
                <p class="kil-side-note">
                    {{ $draftCount }} lomba masih berstatus draf dan belum tampil ke mahasiswa.
                </p>
                <a class="kil-btn kil-btn-outline" style="width: 100%; text-align: center;" href="{{ route('competition.manage.index', ['status' => 'draft']) }}">
                    Lihat draf
                </a>
            </section>
        </aside>
    </div>
</div>

{{-- MODAL TAMBAH / EDIT LOMBA (Fixed & Compact: Action buttons always 100% visible) --}}
<dialog class="kil-dialog" id="modal-form-lomba" aria-labelledby="modal-form-title">
    <div class="kil-dialog-inner">
        <div class="kil-dialog-header">
            <div>
                <h2 id="modal-form-title">Tambah Lomba</h2>
                <p id="modal-form-subtitle">Isi data sesuai informasi resmi lomba. Tanda * wajib diisi.</p>
            </div>
            <button class="kil-dialog-close" type="button" onclick="document.getElementById('modal-form-lomba').close()" aria-label="Tutup form">×</button>
        </div>

        <form id="form-lomba" method="POST" action="{{ route('competition.manage.store') }}">
            @csrf
            <input type="hidden" name="_method" id="form-lomba-method" value="POST">

            {{-- Scrollable fields container --}}
            <div class="kil-form-body">
                {{-- Nama Lomba --}}
                <div class="kil-form-group">
                    <label class="kil-label" for="inp-nama">Nama Lomba *</label>
                    <input class="kil-input" type="text" id="inp-nama" name="name" placeholder="Masukkan nama lomba" required>
                </div>

                {{-- Penyelenggara & Deadline --}}
                <div class="kil-form-row">
                    <div class="kil-form-group">
                        <label class="kil-label" for="inp-penyelenggara">Penyelenggara *</label>
                        <input class="kil-input" type="text" id="inp-penyelenggara" name="organizer" placeholder="Masukkan penyelenggara" required>
                    </div>
                    <div class="kil-form-group">
                        <label class="kil-label" for="inp-deadline">Deadline Pendaftaran *</label>
                        <input class="kil-input" type="date" id="inp-deadline" name="deadline" required>
                    </div>
                </div>

                {{-- Kategori & Tingkat --}}
                <div class="kil-form-row">
                    <div class="kil-form-group">
                        <label class="kil-label" for="inp-kategori">Kategori *</label>
                        <select class="kil-select" id="inp-kategori" name="category" required>
                            <option value="">Pilih kategori</option>
                            <option value="Hackathon">Hackathon</option>
                            <option value="UI/UX Design">UI/UX Design</option>
                            <option value="Business Case">Business Case</option>
                            <option value="Data Science">Data Science</option>
                            <option value="Pemrograman">Pemrograman</option>
                            <option value="Karya Tulis Ilmiah">Karya Tulis Ilmiah</option>
                            <option value="Esai">Esai</option>
                            <option value="Desain Grafis">Desain Grafis</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="kil-form-group">
                        <label class="kil-label" for="inp-tingkat">Tingkat *</label>
                        <select class="kil-select" id="inp-tingkat" name="level" required>
                            <option value="">Pilih tingkat</option>
                            <option value="Regional">Regional</option>
                            <option value="Nasional">Nasional</option>
                            <option value="Internasional">Internasional</option>
                        </select>
                    </div>
                </div>

                {{-- Biaya & Status --}}
                <div class="kil-form-row">
                    <div class="kil-form-group">
                        <label class="kil-label" for="inp-biaya">Biaya Pendaftaran</label>
                        <input class="kil-input" type="text" id="inp-biaya" name="fee" placeholder="Gratis/Rp50.000">
                    </div>
                    <div class="kil-form-group">
                        <label class="kil-label" for="inp-status">Status</label>
                        <select class="kil-select" id="inp-status" name="status" required>
                            <option value="draft">Draf</option>
                            <option value="active">Aktif</option>
                        </select>
                    </div>
                </div>

                {{-- Tautan Pendaftaran --}}
                <div class="kil-form-group">
                    <label class="kil-label" for="inp-tautan">Tautan Pendaftaran</label>
                    <input class="kil-input" type="url" id="inp-tautan" name="registration_url" placeholder="https://" required>
                </div>

                {{-- Deskripsi Singkat --}}
                <div class="kil-form-group">
                    <label class="kil-label" for="inp-deskripsi">Deskripsi Singkat</label>
                    <textarea class="kil-textarea" id="inp-deskripsi" name="description" maxlength="300" placeholder="Maksimal 300 kata" required></textarea>
                    <span class="kil-field-hint"><span id="desc-count">0</span>/300 karakter</span>
                </div>
            </div>

            {{-- Footer Buttons (Always Pinned & 100% Visible) --}}
            <div class="kil-dialog-footer">
                <button class="kil-btn kil-btn-secondary" style="min-width: 90px; height: 38px; background-color: #FFFFFF !important; color: #334155 !important; border: 1px solid #CBD5E1 !important;" type="button" onclick="document.getElementById('modal-form-lomba').close()">Batal</button>
                <button class="kil-btn kil-btn-primary" style="min-width: 140px; height: 38px; background-color: #0B88DB !important; color: #FFFFFF !important; border: 1px solid #0B88DB !important;" type="button" id="btn-submit-form-trigger">Simpan Lomba</button>
            </div>
        </form>
    </div>
</dialog>

{{-- MODAL CONFIRM: SIMPAN DATA LOMBA INI? (Matching Image 4 & 6) --}}
<dialog class="kil-confirm-dialog" id="modal-confirm-save">
    <div class="kil-confirm-body">
        <div class="kil-confirm-icon-box kil-confirm-icon-blue">
            <span style="font-size: 24px; font-weight: 700; line-height: 1;">?</span>
        </div>
        <h3 class="kil-confirm-title" id="save-confirm-title">Simpan data lomba ini?</h3>
        <p class="kil-confirm-desc" id="save-confirm-desc">Informasi lomba akan diperbarui di daftar lomba.</p>
        <div class="kil-confirm-actions">
            <button class="kil-btn kil-btn-secondary" style="min-width: 100px; height: 38px; background-color: #FFFFFF !important; color: #334155 !important; border: 1px solid #CBD5E1 !important;" type="button" onclick="document.getElementById('modal-confirm-save').close()">Tidak</button>
            <button class="kil-btn kil-btn-primary" style="min-width: 100px; height: 38px; background-color: #0B88DB !important; color: #FFFFFF !important; border: 1px solid #0B88DB !important;" type="button" id="btn-save-confirm-yes">Ya</button>
        </div>
    </div>
</dialog>

{{-- MODAL CONFIRM: TERBITKAN LOMBA INI? (Matching Image 7) --}}
<dialog class="kil-confirm-dialog" id="modal-confirm-publish">
    <div class="kil-confirm-body">
        <div class="kil-confirm-icon-box kil-confirm-icon-green">
            <svg style="width:24px;height:24px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round" viewBox="0 0 24 24">
                <path d="M12 19V5M5 12l7-7 7 7"/>
            </svg>
        </div>
        <h3 class="kil-confirm-title">Terbitkan lomba ini?</h3>
        <p class="kil-confirm-desc" id="publish-confirm-desc">Lomba akan tampil ke seluruh mahasiswa.</p>
        <div class="kil-confirm-actions">
            <button class="kil-btn kil-btn-secondary" style="min-width: 100px; height: 38px; background-color: #FFFFFF !important; color: #334155 !important; border: 1px solid #CBD5E1 !important;" type="button" onclick="document.getElementById('modal-confirm-publish').close()">Batal</button>
            <button class="kil-btn kil-btn-primary" style="min-width: 130px; height: 38px; background-color: #0B88DB !important; color: #FFFFFF !important; border: 1px solid #0B88DB !important;" type="button" id="btn-publish-confirm-yes">Ya, Terbitkan</button>
        </div>
    </div>
</dialog>

{{-- MODAL CONFIRM: HAPUS LOMBA INI? (Matching Image 8) --}}
<dialog class="kil-confirm-dialog" id="modal-confirm-delete">
    <div class="kil-confirm-body">
        <div class="kil-confirm-icon-box kil-confirm-icon-red">
            <svg style="width:22px;height:22px;fill:none;stroke:currentColor;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round" viewBox="0 0 24 24">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </div>
        <h3 class="kil-confirm-title">Hapus lomba ini?</h3>
        <p class="kil-confirm-desc" id="delete-confirm-desc">Lomba akan dihapus permanen dan tidak dapat dikembalikan.</p>
        <div class="kil-confirm-actions">
            <button class="kil-btn kil-btn-secondary" style="min-width: 100px; height: 38px; background-color: #FFFFFF !important; color: #334155 !important; border: 1px solid #CBD5E1 !important;" type="button" onclick="document.getElementById('modal-confirm-delete').close()">Batal</button>
            <button class="kil-btn kil-btn-danger-solid" style="min-width: 120px; height: 38px; background-color: #EF4444 !important; color: #FFFFFF !important; border: 1px solid #EF4444 !important;" type="button" id="btn-delete-confirm-yes">Ya, Hapus</button>
        </div>
    </div>
</dialog>

{{-- Hidden Form for Publish & Delete Actions --}}
<form id="action-publish-form" method="POST" style="display:none">
    @csrf
</form>

<form id="action-delete-form" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalForm = document.getElementById('modal-form-lomba');
        var form = document.getElementById('form-lomba');
        var formMethod = document.getElementById('form-lomba-method');
        var modalTitle = document.getElementById('modal-form-title');
        var modalSubtitle = document.getElementById('modal-form-subtitle');
        var btnSubmitTrigger = document.getElementById('btn-submit-form-trigger');
        
        var modalConfirmSave = document.getElementById('modal-confirm-save');
        var saveConfirmTitle = document.getElementById('save-confirm-title');
        var saveConfirmDesc = document.getElementById('save-confirm-desc');
        var btnSaveConfirmYes = document.getElementById('btn-save-confirm-yes');

        var modalConfirmPublish = document.getElementById('modal-confirm-publish');
        var publishConfirmDesc = document.getElementById('publish-confirm-desc');
        var btnPublishConfirmYes = document.getElementById('btn-publish-confirm-yes');
        var publishForm = document.getElementById('action-publish-form');

        var modalConfirmDelete = document.getElementById('modal-confirm-delete');
        var deleteConfirmDesc = document.getElementById('delete-confirm-desc');
        var btnDeleteConfirmYes = document.getElementById('btn-delete-confirm-yes');
        var deleteForm = document.getElementById('action-delete-form');

        var descTextarea = document.getElementById('inp-deskripsi');
        var descCount = document.getElementById('desc-count');

        var isEditMode = false;
        var currentCompName = '';
        var storeUrl = @json(route('competition.manage.store'));
        var updateUrlTemplate = @json(route('competition.manage.update', ['competition' => ':id']));

        // Character count
        function updateDescCount() {
            descCount.textContent = String(descTextarea.value.length);
        }
        descTextarea.addEventListener('input', updateDescCount);

        // Buka Tambah Lomba (Image 3)
        document.getElementById('btn-tambah-lomba').addEventListener('click', function () {
            isEditMode = false;
            currentCompName = '';
            form.reset();
            form.action = storeUrl;
            formMethod.value = 'POST';
            modalTitle.textContent = 'Tambah Lomba';
            modalSubtitle.textContent = 'Isi data sesuai informasi resmi lomba. Tanda * wajib diisi.';
            btnSubmitTrigger.textContent = 'Simpan Lomba';
            saveConfirmTitle.textContent = 'Simpan data lomba ini?';
            saveConfirmDesc.textContent = 'Informasi lomba akan diperbarui di daftar lomba.';
            document.getElementById('inp-status').value = 'draft';
            updateDescCount();
            modalForm.showModal();
        });

        // Buka Edit Lomba (Image 5)
        document.querySelectorAll('[data-edit-competition]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                isEditMode = true;
                var comp = JSON.parse(btn.dataset.editCompetition);
                currentCompName = comp.name;
                form.action = updateUrlTemplate.replace(':id', comp.id);
                formMethod.value = 'PUT';
                modalTitle.textContent = 'Edit Lomba';
                modalSubtitle.textContent = 'Perbarui data sesuai informasi resmi lomba. Tanda * wajib diisi.';
                btnSubmitTrigger.textContent = 'Simpan Perubahan';
                saveConfirmTitle.textContent = 'Simpan perubahan lomba?';
                saveConfirmDesc.textContent = 'Perubahan data lomba "' + comp.name + '" akan disimpan.';

                document.getElementById('inp-nama').value = comp.name || '';
                document.getElementById('inp-penyelenggara').value = comp.organizer || '';
                document.getElementById('inp-deadline').value = comp.deadline || '';
                document.getElementById('inp-kategori').value = comp.category || '';
                document.getElementById('inp-tingkat').value = comp.level || '';
                document.getElementById('inp-biaya').value = comp.fee || '';
                document.getElementById('inp-status').value = comp.status === 'ended' ? 'draft' : comp.status;
                document.getElementById('inp-tautan').value = comp.registration_url || '';
                document.getElementById('inp-deskripsi').value = comp.description || '';
                
                updateDescCount();
                modalForm.showModal();
            });
        });

        // Validasi dan Konfirmasi Simpan (Image 4 & 6)
        btnSubmitTrigger.addEventListener('click', function () {
            if (!form.reportValidity()) {
                return;
            }
            if (!isEditMode) {
                saveConfirmTitle.textContent = 'Simpan data lomba ini?';
                saveConfirmDesc.textContent = 'Informasi lomba akan diperbarui di daftar lomba.';
            } else {
                saveConfirmTitle.textContent = 'Simpan perubahan lomba?';
                saveConfirmDesc.textContent = 'Perubahan data lomba "' + (document.getElementById('inp-nama').value || currentCompName) + '" akan disimpan.';
            }
            modalConfirmSave.showModal();
        });

        btnSaveConfirmYes.addEventListener('click', function () {
            modalConfirmSave.close();
            form.submit();
        });

        // Terbitkan Lomba (Image 7)
        document.querySelectorAll('[data-publish-action]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                publishForm.action = btn.dataset.publishAction;
                var name = btn.dataset.publishName || '';
                publishConfirmDesc.textContent = 'Lomba "' + name + '" akan tampil ke seluruh mahasiswa.';
                modalConfirmPublish.showModal();
            });
        });

        btnPublishConfirmYes.addEventListener('click', function () {
            modalConfirmPublish.close();
            publishForm.submit();
        });

        // Hapus Lomba (Image 8 - dapat menghapus lomba apa pun yang tidak sesuai)
        document.querySelectorAll('[data-delete-action]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                deleteForm.action = btn.dataset.deleteAction;
                var name = btn.dataset.deleteName || '';
                deleteConfirmDesc.textContent = 'Lomba "' + name + '" akan dihapus permanen dan tidak dapat dikembalikan.';
                modalConfirmDelete.showModal();
            });
        });

        btnDeleteConfirmYes.addEventListener('click', function () {
            modalConfirmDelete.close();
            deleteForm.submit();
        });
    });
</script>
@endsection
