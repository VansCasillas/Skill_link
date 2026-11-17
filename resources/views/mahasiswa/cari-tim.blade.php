@extends('layouts.app')

@section('title', 'Cari Tim · Skill Link')

@section('content')
<style>
    /* ══════════════════════════════════════════════════════════════════════════
       CARI TIM - STYLES SESUAI FIGMA
    ══════════════════════════════════════════════════════════════════════════ */
    .ct-page {
        max-width: 1100px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 18px;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: #0F172A;
    }

    .ct-page *,
    .ct-page *::before,
    .ct-page *::after {
        box-sizing: border-box;
    }

    /* ── Header Judul & Tombol Buat Tim ───────────────────────────────────── */
    .ct-header {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        min-height: 59px;
    }

    .ct-title-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .ct-title-group h1 {
        margin: 0;
        font-weight: 700;
        font-size: 32px;
        line-height: 40px;
        color: #0F172A;
    }

    .ct-title-group p {
        margin: 0;
        font-weight: 400;
        font-size: 12px;
        line-height: 15px;
        color: #475569;
    }

    .ct-btn-create {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 11px 22px;
        height: 37px;
        background: #0A87D9;
        border-radius: 10px;
        color: #FFFFFF;
        font-weight: 500;
        font-size: 12px;
        line-height: 15px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: filter .15s ease, transform .05s;
        white-space: nowrap;
    }

    .ct-btn-create:hover {
        filter: brightness(.92);
        color: #FFFFFF;
    }

    .ct-btn-create:active {
        transform: translateY(1px);
    }

    /* ── Bar Pencarian & Urutkan ─────────────────────────────────────────── */
    .ct-search-bar {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 12px;
        width: 100%;
        height: 46px;
    }

    .ct-search-box {
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 0 16px;
        gap: 10px;
        flex: 1;
        height: 46px;
        background: #FFFFFF;
        border: 1px solid #0A87D9;
        border-radius: 12px;
        transition: box-shadow .15s ease;
    }

    .ct-search-box:focus-within {
        box-shadow: 0 0 0 3px rgba(10, 135, 217, 0.2);
    }

    .ct-search-box svg {
        width: 16px;
        height: 16px;
        color: #64748B;
        flex: none;
    }

    .ct-search-box input {
        flex: 1;
        min-width: 0;
        border: none;
        outline: none;
        background: transparent;
        font-family: inherit;
        font-weight: 400;
        font-size: 13px;
        line-height: 16px;
        color: #0F172A;
    }

    .ct-search-box input::placeholder {
        color: #64748B;
    }

    .ct-sort-box {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding: 0 14px 0 16px;
        gap: 8px;
        width: 190px;
        height: 46px;
        background: #FFFFFF;
        border: 1px solid #0A87D9;
        border-radius: 12px;
        position: relative;
        cursor: pointer;
        user-select: none;
        flex: none;
    }

    .ct-sort-select {
        width: 100%;
        height: 100%;
        border: none;
        outline: none;
        background: transparent;
        font-family: inherit;
        font-weight: 500;
        font-size: 12px;
        line-height: 15px;
        color: #0F172A;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
    }

    .ct-sort-arrow {
        position: absolute;
        right: 14px;
        pointer-events: none;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
    }

    /* ── Filter Chips ────────────────────────────────────────────────────── */
    .ct-chips-bar {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        width: 100%;
        height: 28px;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .ct-chips-bar::-webkit-scrollbar {
        display: none;
    }

    .ct-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 14px;
        height: 28px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 11px;
        line-height: 14px;
        cursor: pointer;
        border: none;
        white-space: nowrap;
        transition: all .15s ease;
        background: #E6EFFB;
        color: #0F172A;
    }

    .ct-chip:hover {
        filter: brightness(.95);
    }

    .ct-chip.active {
        background: #0A87D9;
        color: #FFFFFF;
    }

    .ct-spacer {
        flex: 1;
    }

    .ct-count {
        font-weight: 500;
        font-size: 11px;
        line-height: 14px;
        color: #475569;
        white-space: nowrap;
        margin-left: auto;
    }

    /* ── Dua Kolom Utama (Left 740px + Right 336px) ───────────────────────── */
    .ct-cols {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 336px;
        gap: 24px;
        align-items: start;
        width: 100%;
    }

    /* ── Kolom Kiri ──────────────────────────────────────────────────────── */
    .ct-left {
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
    }

    .ct-left-title {
        font-weight: 700;
        font-size: 17px;
        line-height: 21px;
        color: #0F172A;
        margin: 0;
    }

    .ct-team-cards {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* ── Kartu Tim ───────────────────────────────────────────────────────── */
    .ct-card {
        background: #FFFFFF;
        border: 1px solid #5B9BF0;
        border-radius: 20px;
        padding: 18px 22px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: box-shadow .2s ease, transform .15s ease;
    }

    .ct-card:hover {
        box-shadow: 0 8px 24px rgba(10, 87, 217, 0.12);
        transform: translateY(-2px);
    }

    /* Bagian Atas Kartu */
    .ct-card-top {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 12px;
        width: 100%;
        min-height: 46px;
    }

    .ct-avatar-team {
        width: 46px;
        height: 46px;
        border-radius: 23px;
        border: 2px solid #FFFFFF;
        display: grid;
        place-items: center;
        flex: none;
        color: #FFFFFF;
        font-weight: 700;
        font-size: 17.5px;
        line-height: 22px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .ct-card-title-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
        flex: 1;
        min-width: 0;
    }

    .ct-card-team-name {
        font-weight: 700;
        font-size: 15px;
        line-height: 19px;
        color: #0F172A;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ct-card-lomba-name {
        font-weight: 400;
        font-size: 11px;
        line-height: 14px;
        color: #475569;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ct-badge-need {
        padding: 4px 10px;
        background: #FEF0C7;
        border-radius: 20px;
        font-weight: 500;
        font-size: 10px;
        line-height: 13px;
        color: #B54708;
        white-space: nowrap;
        flex: none;
    }

    /* Deskripsi */
    .ct-card-desc {
        font-weight: 400;
        font-size: 12px;
        line-height: 16px;
        color: #0F172A;
        margin: 0;
    }

    /* Bagian Dibutuhkan */
    .ct-needed-row {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .ct-needed-label {
        font-weight: 500;
        font-size: 11px;
        line-height: 14px;
        color: #475569;
    }

    .ct-skill-tag {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        background: #E0F0FC;
        border-radius: 20px;
        font-weight: 500;
        font-size: 10px;
        line-height: 13px;
        color: #0A6DB0;
    }

    /* Garis Pemisah */
    .ct-card-divider {
        width: 100%;
        height: 1px;
        background: #E2E8F0;
    }

    /* Bagian Bawah Kartu */
    .ct-card-bottom {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 12px;
        width: 100%;
    }

    .ct-members-stack {
        display: flex;
        flex-direction: row;
        align-items: center;
        flex: none;
    }

    .ct-member-avatar {
        width: 26px;
        height: 26px;
        border-radius: 13px;
        border: 2px solid #FFFFFF;
        display: grid;
        place-items: center;
        color: #FFFFFF;
        font-weight: 500;
        font-size: 13px;
        line-height: 16px;
        margin-right: -6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    }

    .ct-member-avatar:first-child {
        margin-left: 0;
    }

    .ct-card-meta {
        display: flex;
        flex-direction: column;
        gap: 2px;
        flex: 1;
        min-width: 0;
        margin-left: 10px;
    }

    .ct-slot-text {
        font-weight: 700;
        font-size: 11px;
        line-height: 14px;
        color: #0F172A;
    }

    .ct-deadline-text {
        font-weight: 400;
        font-size: 10px;
        line-height: 13px;
        color: #475569;
    }

    .ct-btn-detail {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 16px;
        height: 33px;
        background: #FFFFFF;
        border: 1px solid #0A87D9;
        border-radius: 10px;
        color: #0A87D9;
        font-weight: 500;
        font-size: 12px;
        line-height: 15px;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        transition: all .15s ease;
    }

    .ct-btn-detail:hover {
        background: #F0F7FF;
        color: #0A87D9;
    }

    .ct-btn-join {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 22px;
        height: 31px;
        background: #0A87D9;
        border: none;
        border-radius: 10px;
        color: #FFFFFF;
        font-weight: 500;
        font-size: 12px;
        line-height: 15px;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        transition: filter .15s ease, transform .05s;
    }

    .ct-btn-join:hover {
        filter: brightness(.92);
        color: #FFFFFF;
    }

    .ct-btn-join:active {
        transform: translateY(1px);
    }

    .ct-btn-join.joined {
        background: #067647;
        pointer-events: none;
    }

    /* ── Kolom Kanan (Card Tim Saya) ─────────────────────────────────────── */
    .ct-right {
        display: flex;
        flex-direction: column;
        gap: 18px;
        min-width: 0;
    }

    .ct-card-myteam {
        background: #FFFFFF;
        box-shadow: 0px 4px 16px rgba(10, 51, 102, 0.08);
        border-radius: 16px;
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        width: 100%;
    }

    .ct-myteam-head {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .ct-myteam-head h3 {
        margin: 0;
        font-weight: 700;
        font-size: 15px;
        line-height: 19px;
        color: #0F172A;
    }

    .ct-myteam-chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        background: #E0F0FC;
        border-radius: 20px;
        font-weight: 500;
        font-size: 10px;
        line-height: 13px;
        color: #0A6DB0;
    }

    .ct-myteam-body {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 12px;
    }

    .ct-myteam-avatar {
        width: 44px;
        height: 44px;
        border-radius: 22px;
        border: 2px solid #FFFFFF;
        background: #FF7F50;
        display: grid;
        place-items: center;
        color: #FFFFFF;
        font-weight: 700;
        font-size: 16.7px;
        line-height: 21px;
        flex: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .ct-myteam-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .ct-myteam-name {
        font-weight: 700;
        font-size: 13px;
        line-height: 16px;
        color: #0F172A;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ct-myteam-sub {
        font-weight: 400;
        font-size: 10px;
        line-height: 13px;
        color: #475569;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Progress bar */
    .ct-progress-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        width: 100%;
    }

    .ct-progress-label {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .ct-progress-label span {
        font-weight: 500;
        font-size: 10px;
        line-height: 13px;
        color: #475569;
    }

    .ct-progress-label b {
        font-weight: 700;
        font-size: 10px;
        line-height: 13px;
        color: #0A87D9;
    }

    .ct-progress-track {
        width: 100%;
        height: 6px;
        background: #DCE8F7;
        border-radius: 3px;
        overflow: hidden;
    }

    .ct-progress-bar {
        height: 6px;
        background: #0A87D9;
        border-radius: 3px;
        transition: width .3s ease;
    }

    .ct-btn-manage {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 16px;
        height: 37px;
        width: 100%;
        background: #FFFFFF;
        border: 1px solid #0A87D9;
        border-radius: 10px;
        color: #0A87D9;
        font-weight: 500;
        font-size: 12px;
        line-height: 15px;
        text-decoration: none;
        cursor: pointer;
        transition: all .15s ease;
    }

    .ct-btn-manage:hover {
        background: #F0F7FF;
        color: #0A87D9;
    }

    /* ── Empty state ─────────────────────────────────────────────────────── */
    .ct-empty {
        background: #FFFFFF;
        border: 1.5px dashed #CBDDF5;
        border-radius: 20px;
        padding: 40px 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .ct-empty p {
        margin: 0;
        color: #475569;
        font-size: 13px;
    }

    /* ── Modal Popups ────────────────────────────────────────────────────── */
    .ct-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(11, 27, 51, 0.55);
        z-index: 50;
        display: grid;
        place-items: center;
        padding: 16px;
        opacity: 0;
        visibility: hidden;
        transition: opacity .2s ease, visibility .2s;
    }

    .ct-modal-backdrop.open {
        opacity: 1;
        visibility: visible;
    }

    .ct-modal-content {
        background: #FFFFFF;
        border-radius: 20px;
        max-width: 520px;
        width: 100%;
        padding: 24px 28px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        box-shadow: 0 16px 36px rgba(10, 51, 102, 0.2);
        transform: translateY(20px);
        transition: transform .2s ease;
    }

    .ct-modal-backdrop.open .ct-modal-content {
        transform: translateY(0);
    }

    .ct-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ct-modal-head h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #0F172A;
    }

    .ct-modal-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #64748B;
        padding: 4px;
        line-height: 1;
    }

    .ct-modal-close:hover {
        color: #0F172A;
    }

    .ct-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #067647;
        color: #FFFFFF;
        padding: 12px 20px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
        box-shadow: 0 8px 24px rgba(6, 118, 71, 0.25);
        display: flex;
        align-items: center;
        gap: 8px;
        z-index: 60;
        transform: translateY(80px);
        opacity: 0;
        transition: transform .25s ease, opacity .25s;
    }

    .ct-toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    /* ── Responsive Tablet & Mobile ──────────────────────────────────────── */
    @media (max-width: 960px) {
        .ct-cols {
            grid-template-columns: 1fr;
        }

        .ct-right {
            order: -1;
        }

        .ct-search-bar {
            flex-direction: column;
            height: auto;
        }

        .ct-search-box {
            width: 100%;
        }

        .ct-sort-box {
            width: 100%;
        }
    }

    @media (max-width: 640px) {
        .ct-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .ct-title-group h1 {
            font-size: 26px;
            line-height: 32px;
        }

        .ct-card-bottom {
            flex-wrap: wrap;
            gap: 8px;
        }

        .ct-btn-detail,
        .ct-btn-join {
            flex: 1;
            text-align: center;
        }
    }
</style>

<div class="ct-page">

    {{-- ── 1. Header Judul & Button + Buat Tim ────────────────────────────── --}}
    <div class="ct-header">
        <div class="ct-title-group">
            <h1>Cari Tim</h1>
            <p>Temukan tim lomba yang cocok dengan skill Anda atau rekrut anggota baru.</p>
        </div>
        <button class="ct-btn-create" type="button" id="btn-create-team">
            + Buat Tim
        </button>
    </div>

    {{-- ── 2. Pencarian & Urutkan ──────────────────────────────────────────── --}}
    <div class="ct-search-bar">
        <div class="ct-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text"
                   id="ct-search-input"
                   placeholder="Cari nama tim, lomba, atau skill..."
                   aria-label="Cari nama tim, lomba, atau skill">
        </div>

        <div class="ct-sort-box">
            <select class="ct-sort-select" id="ct-sort-select" aria-label="Urutkan">
                <option value="terbaru" selected>Urutkan: Terbaru</option>
                <option value="deadline">Urutkan: Deadline</option>
                <option value="kuota">Urutkan: Sisa Kuota</option>
            </select>
            <span class="ct-sort-arrow" aria-hidden="true">▾</span>
        </div>
    </div>

    {{-- ── 3. Filter Chips & Count ────────────────────────────────────────── --}}
    <div class="ct-chips-bar" role="tablist" aria-label="Filter Kategori Skill">
        <button class="ct-chip active" type="button" data-skill="semua">Semua</button>
        <button class="ct-chip" type="button" data-skill="frontend">Frontend</button>
        <button class="ct-chip" type="button" data-skill="backend">Backend</button>
        <button class="ct-chip" type="button" data-skill="ui/ux">UI/UX</button>
        <button class="ct-chip" type="button" data-skill="mobile">Mobile</button>
        <button class="ct-chip" type="button" data-skill="iot">IoT</button>
        <button class="ct-chip" type="button" data-skill="data">Data</button>

        <span class="ct-spacer"></span>
        <span class="ct-count" id="ct-count-text">{{ count($teams) }} tim ditemukan</span>
    </div>

    {{-- ── 4. Main Columns Layout ─────────────────────────────────────────── --}}
    <div class="ct-cols">

        {{-- Kolom Kiri: Tim yang Mencari Anggota --}}
        <div class="ct-left">
            <h2 class="ct-left-title">Tim yang Mencari Anggota</h2>

            <div class="ct-team-cards" id="ct-cards-container">
                @forelse($teams as $team)
                <article class="ct-card"
                         data-team-id="{{ $team['id'] }}"
                         data-name="{{ strtolower($team['nama']) }}"
                         data-lomba="{{ strtolower($team['lomba']) }}"
                         data-skills="{{ strtolower(implode(',', $team['skills'])) }}"
                         data-deadline="{{ $team['deadline'] }}"
                         data-quota="{{ $team['total'] - $team['terisi'] }}">

                    {{-- Baris Atas --}}
                    <div class="ct-card-top">
                        <div class="ct-avatar-team" style="background: {{ $team['warna'] }};">
                            {{ $team['inisial'] }}
                        </div>
                        <div class="ct-card-title-group">
                            <span class="ct-card-team-name">{{ $team['nama'] }}</span>
                            <span class="ct-card-lomba-name">{{ $team['lomba'] }}</span>
                        </div>
                        <span class="ct-badge-need">{{ $team['butuh_teks'] }}</span>
                    </div>

                    {{-- Deskripsi --}}
                    <p class="ct-card-desc">{{ $team['deskripsi'] }}</p>

                    {{-- Dibutuhkan --}}
                    <div class="ct-needed-row">
                        <span class="ct-needed-label">Dibutuhkan:</span>
                        @foreach($team['skills'] as $skill)
                        <span class="ct-skill-tag">{{ $skill }}</span>
                        @endforeach
                    </div>

                    {{-- Garis Pemisah --}}
                    <div class="ct-card-divider"></div>

                    {{-- Baris Bawah --}}
                    <div class="ct-card-bottom">
                        {{-- Stack Anggota --}}
                        <div class="ct-members-stack" aria-label="Anggota saat ini">
                            @foreach($team['anggota'] as $m)
                            <div class="ct-member-avatar" style="background: {{ $m['warna'] }};">
                                {{ $m['inisial'] }}
                            </div>
                            @endforeach
                        </div>

                        {{-- Info Kuota & Deadline --}}
                        <div class="ct-card-meta">
                            <span class="ct-slot-text">{{ $team['terisi'] }} / {{ $team['total'] }} anggota</span>
                            <span class="ct-deadline-text">Deadline {{ $team['deadline'] }}</span>
                        </div>

                        {{-- Tombol Aksi --}}
                        <button class="ct-btn-detail"
                                type="button"
                                data-detail-team='@json($team)'>
                            Lihat Detail
                        </button>
                        <button class="ct-btn-join"
                                type="button"
                                data-join-id="{{ $team['id'] }}"
                                data-team-name="{{ $team['nama'] }}">
                            Gabung
                        </button>
                    </div>
                </article>
                @empty
                <div class="ct-empty">
                    <p>Belum ada tim yang mencari anggota saat ini.</p>
                </div>
                @endforelse
            </div>

            <div class="ct-empty" id="ct-no-results" style="display: none;">
                <p>Tidak ada tim yang cocok dengan kriteria pencarian Anda.</p>
            </div>
        </div>

        {{-- Kolom Kanan: Card Tim Saya --}}
        <aside class="ct-right">
            <section class="ct-card-myteam">
                <div class="ct-myteam-head">
                    <h3>Tim Saya</h3>
                    <span class="ct-myteam-chip">{{ $myTeam['total_tim'] ?? 2 }} tim</span>
                </div>

                <div class="ct-myteam-body">
                    <div class="ct-myteam-avatar" style="background: {{ $myTeam['warna'] ?? '#FF7F50' }};">
                        {{ $myTeam['inisial'] ?? 'G' }}
                    </div>
                    <div class="ct-myteam-info">
                        <span class="ct-myteam-name">{{ $myTeam['nama'] ?? 'Tim Garuda Code' }}</span>
                        <span class="ct-myteam-sub">
                            {{ $myTeam['role'] ?? 'Ketua' }} · {{ $myTeam['anggota'] ?? 3 }} anggota · {{ $myTeam['lomba'] ?? 'Gemastik 2026' }}
                        </span>
                    </div>
                </div>

                <div class="ct-progress-group">
                    <div class="ct-progress-label">
                        <span>Slot terisi</span>
                        <b>{{ $myTeam['terisi'] ?? 3 }} / {{ $myTeam['total'] ?? 4 }}</b>
                    </div>
                    <div class="ct-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                         aria-valuenow="{{ round((($myTeam['terisi'] ?? 3) / ($myTeam['total'] ?? 4)) * 100) }}">
                        <div class="ct-progress-bar" style="width: {{ round((($myTeam['terisi'] ?? 3) / ($myTeam['total'] ?? 4)) * 100) }}%;"></div>
                    </div>
                </div>

                <a class="ct-btn-manage" href="{{ route('tim.index') }}">
                    Kelola Tim
                </a>
            </section>
        </aside>

    </div>
</div>

{{-- ── Modal Detail Tim ─────────────────────────────────────────────────── --}}
<div class="ct-modal-backdrop" id="modal-detail" role="dialog" aria-modal="true" aria-labelledby="modal-detail-title">
    <div class="ct-modal-content">
        <div class="ct-modal-head">
            <h2 id="modal-detail-title">Detail Tim</h2>
            <button class="ct-modal-close" type="button" id="close-modal-detail" aria-label="Tutup modal">&times;</button>
        </div>
        <div id="modal-detail-body">
            <!-- Diisi secara dinamis via JS -->
        </div>
        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 8px;">
            <button class="ct-btn-detail" type="button" id="modal-btn-cancel">Tutup</button>
            <button class="ct-btn-join" type="button" id="modal-btn-join-action">Ajukan Bergabung</button>
        </div>
    </div>
</div>

{{-- ── Modal Buat Tim ───────────────────────────────────────────────────── --}}
<div class="ct-modal-backdrop" id="modal-create" role="dialog" aria-modal="true" aria-labelledby="modal-create-title">
    <div class="ct-modal-content">
        <div class="ct-modal-head">
            <h2 id="modal-create-title">Buat Tim Baru</h2>
            <button class="ct-modal-close" type="button" id="close-modal-create" aria-label="Tutup modal">&times;</button>
        </div>
        <form id="form-create-team" onsubmit="event.preventDefault(); handleCreateTeam();" style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 12px; font-weight: 500; color: #0F172A;" for="create-nama">Nama Tim</label>
                <input style="height: 38px; border: 1px solid #0A87D9; border-radius: 10px; padding: 0 12px; font-family: inherit; font-size: 13px;"
                       id="create-nama" required placeholder="Contoh: Tim Garuda Code">
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 12px; font-weight: 500; color: #0F172A;" for="create-lomba">Lomba / Kompetisi</label>
                <input style="height: 38px; border: 1px solid #0A87D9; border-radius: 10px; padding: 0 12px; font-family: inherit; font-size: 13px;"
                       id="create-lomba" required placeholder="Contoh: Gemastik 2026 - PPL">
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 12px; font-weight: 500; color: #0F172A;" for="create-desc">Deskripsi & Peran yang Dicari</label>
                <textarea style="height: 70px; border: 1px solid #0A87D9; border-radius: 10px; padding: 8px 12px; font-family: inherit; font-size: 13px; resize: none;"
                          id="create-desc" required placeholder="Jelaskan tujuan tim dan skill anggota yang dicari..."></textarea>
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 12px; font-weight: 500; color: #0F172A;" for="create-skills">Skill yang Dibutuhkan (pisahkan koma)</label>
                <input style="height: 38px; border: 1px solid #0A87D9; border-radius: 10px; padding: 0 12px; font-family: inherit; font-size: 13px;"
                       id="create-skills" placeholder="Contoh: Flutter, UI/UX, Backend">
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
                <button class="ct-btn-detail" type="button" id="btn-cancel-create">Batal</button>
                <button class="ct-btn-join" type="submit">Publikasikan Tim</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Toast Feedback ──────────────────────────────────────────────────── --}}
<div class="ct-toast" id="ct-toast" role="status" aria-live="polite">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M20 6L9 17l-5-5"/>
    </svg>
    <span id="ct-toast-msg">Permintaan berhasil dikirim!</span>
</div>

{{-- ── JavaScript Interaktivitas ────────────────────────────────────────── --}}
<script>
    (function () {
        'use strict';

        var searchInput = document.getElementById('ct-search-input');
        var sortSelect  = document.getElementById('ct-sort-select');
        var chips       = document.querySelectorAll('.ct-chip');
        var cards       = Array.from(document.querySelectorAll('.ct-card'));
        var countText   = document.getElementById('ct-count-text');
        var noResults   = document.getElementById('ct-no-results');
        var toast       = document.getElementById('ct-toast');
        var toastMsg    = document.getElementById('ct-toast-msg');

        var activeSkill = 'semua';

        // ── Toast Helper ───────────────────────────────────────────────────
        function showToast(message) {
            toastMsg.textContent = message;
            toast.classList.add('show');
            setTimeout(function () {
                toast.classList.remove('show');
            }, 3000);
        }

        // ── Filter & Search Logic ──────────────────────────────────────────
        function filterCards() {
            var query = (searchInput.value || '').trim().toLowerCase();
            var visibleCount = 0;

            cards.forEach(function (card) {
                var name     = card.getAttribute('data-name') || '';
                var lomba    = card.getAttribute('data-lomba') || '';
                var skills   = card.getAttribute('data-skills') || '';

                var matchQuery = !query ||
                    name.indexOf(query) !== -1 ||
                    lomba.indexOf(query) !== -1 ||
                    skills.indexOf(query) !== -1;

                var matchSkill = activeSkill === 'semua' || skills.indexOf(activeSkill) !== -1;

                if (matchQuery && matchSkill) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            countText.textContent = visibleCount + ' tim ditemukan';
            if (noResults) {
                noResults.style.display = visibleCount === 0 ? 'flex' : 'none';
            }
        }

        // ── Filter Chips Click ─────────────────────────────────────────────
        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                chips.forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                activeSkill = chip.getAttribute('data-skill') || 'semua';
                filterCards();
            });
        });

        // ── Search Input Event ─────────────────────────────────────────────
        if (searchInput) {
            searchInput.addEventListener('input', filterCards);
        }

        // ── Sort Event ─────────────────────────────────────────────────────
        if (sortSelect) {
            sortSelect.addEventListener('change', function () {
                var container = document.getElementById('ct-cards-container');
                var val = sortSelect.value;

                var sorted = cards.slice().sort(function (a, b) {
                    if (val === 'kuota') {
                        var qA = parseInt(a.getAttribute('data-quota') || '0', 10);
                        var qB = parseInt(b.getAttribute('data-quota') || '0', 10);
                        return qB - qA;
                    }
                    if (val === 'deadline') {
                        var dA = a.getAttribute('data-deadline') || '';
                        var dB = b.getAttribute('data-deadline') || '';
                        return dA.localeCompare(dB);
                    }
                    // default: terbaru (urutan ID asli)
                    var idA = parseInt(a.getAttribute('data-team-id') || '0', 10);
                    var idB = parseInt(b.getAttribute('data-team-id') || '0', 10);
                    return idA - idB;
                });

                sorted.forEach(function (c) { container.appendChild(c); });
            });
        }

        // ── Tombol Gabung ──────────────────────────────────────────────────
        document.querySelectorAll('.ct-btn-join').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var teamName = btn.getAttribute('data-team-name') || 'tim';
                btn.classList.add('joined');
                btn.textContent = 'Diajukan ✓';
                showToast('Permintaan bergabung ke ' + teamName + ' berhasil dikirim!');
            });
        });

        // ── Modal Detail Tim ───────────────────────────────────────────────
        var modalDetail    = document.getElementById('modal-detail');
        var closeDetail    = document.getElementById('close-modal-detail');
        var cancelDetail   = document.getElementById('modal-btn-cancel');
        var detailBody     = document.getElementById('modal-detail-body');
        var joinActionBtn  = document.getElementById('modal-btn-join-action');
        var currentDetailTeam = null;

        function openModalDetail(team) {
            currentDetailTeam = team;
            detailBody.innerHTML =
                '<div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">' +
                    '<div class="ct-avatar-team" style="background:' + team.warna + ';">' + team.inisial + '</div>' +
                    '<div>' +
                        '<div style="font-weight:700; font-size:16px; color:#0F172A;">' + team.nama + '</div>' +
                        '<div style="font-size:12px; color:#475569;">' + team.lomba + '</div>' +
                    '</div>' +
                '</div>' +
                '<p style="font-size:13px; line-height:18px; color:#0F172A; margin:0 0 14px 0;">' + team.deskripsi + '</p>' +
                '<div style="margin-bottom:14px;">' +
                    '<strong style="font-size:12px; color:#475569; display:block; margin-bottom:6px;">Skill yang Dibutuhkan:</strong>' +
                    '<div style="display:flex; gap:6px; flex-wrap:wrap;">' +
                        team.skills.map(function(s) {
                            return '<span class="ct-skill-tag">' + s + '</span>';
                        }).join('') +
                    '</div>' +
                '</div>' +
                '<div style="background:#F8FAFC; border-radius:12px; padding:12px 16px; display:flex; justify-content:space-between; font-size:12px;">' +
                    '<span><strong>Status Kuota:</strong> ' + team.terisi + ' / ' + team.total + ' Anggota</span>' +
                    '<span style="color:#B54708;"><strong>Deadline:</strong> ' + team.deadline + '</span>' +
                '</div>';

            modalDetail.classList.add('open');
        }

        document.querySelectorAll('[data-detail-team]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var data = JSON.parse(btn.getAttribute('data-detail-team'));
                openModalDetail(data);
            });
        });

        if (closeDetail) closeDetail.addEventListener('click', function () { modalDetail.classList.remove('open'); });
        if (cancelDetail) cancelDetail.addEventListener('click', function () { modalDetail.classList.remove('open'); });

        if (joinActionBtn) {
            joinActionBtn.addEventListener('click', function () {
                modalDetail.classList.remove('open');
                if (currentDetailTeam) {
                    showToast('Permintaan bergabung ke ' + currentDetailTeam.nama + ' berhasil dikirim!');
                    var cardBtn = document.querySelector('.ct-btn-join[data-join-id="' + currentDetailTeam.id + '"]');
                    if (cardBtn) {
                        cardBtn.classList.add('joined');
                        cardBtn.textContent = 'Diajukan ✓';
                    }
                }
            });
        }

        // ── Modal Buat Tim ─────────────────────────────────────────────────
        var modalCreate  = document.getElementById('modal-create');
        var btnCreate    = document.getElementById('btn-create-team');
        var closeCreate  = document.getElementById('close-modal-create');
        var cancelCreate = document.getElementById('btn-cancel-create');

        if (btnCreate) {
            btnCreate.addEventListener('click', function () {
                modalCreate.classList.add('open');
            });
        }

        if (closeCreate) closeCreate.addEventListener('click', function () { modalCreate.classList.remove('open'); });
        if (cancelCreate) cancelCreate.addEventListener('click', function () { modalCreate.classList.remove('open'); });

        window.handleCreateTeam = function () {
            var nama   = document.getElementById('create-nama').value;
            modalCreate.classList.remove('open');
            document.getElementById('form-create-team').reset();
            showToast('Tim "' + nama + '" berhasil dibuat & rekrutmen dipublikasikan!');
        };

        // Close on escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                modalDetail.classList.remove('open');
                modalCreate.classList.remove('open');
            }
        });
    })();
</script>
@endsection
