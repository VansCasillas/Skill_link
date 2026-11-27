@extends('layouts.app')

@section('title', 'Susunan Portofolio – Skill Link')

@section('content')
<style>
/* ═══════════════════════════════════════════════════════════════════
   SUSUNAN PORTOFOLIO – Page Styles
═══════════════════════════════════════════════════════════════════ */

/* Page Background */
.sp-page {
    background: #CBDDF5;
    min-height: calc(100vh - 142px);
    padding: 28px 20px 48px;
}

/* Main container */
.sp-main {
    width: 100%;
    max-width: 1136px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* Header row */
.sp-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.sp-header-judul h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 26px;
    color: #0F172A;
    margin: 0 0 4px;
}
.sp-header-judul p {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 400;
    font-size: 12px;
    color: #64748B;
    margin: 0;
}
.btn-simpan-susunan {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 22px;
    background: #2563EB;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 11px;
    color: #FFFFFF;
    transition: background .2s;
    text-decoration: none;
    gap: 6px;
}
.btn-simpan-susunan:hover { background: #1D4ED8; }

/* Two-column layout */
.sp-body {
    display: flex;
    gap: 24px;
    align-items: flex-start;
}
.sp-list-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.sp-preview-col {
    width: 328px;
    flex-shrink: 0;
}

/* Tab bar */
.sp-tabs {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2px;
}
.tab-pills {
    display: flex;
    gap: 6px;
    background: #E8F0FB;
    border-radius: 20px;
    padding: 4px;
}
.tab-pill {
    padding: 6px 18px;
    border-radius: 20px;
    border: none;
    background: transparent;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 13px;
    color: #64748B;
    cursor: pointer;
    transition: all .2s;
}
.tab-pill.active {
    background: #2563EB;
    color: #fff;
}
.tab-pill:hover:not(.active) {
    background: #DBEAFE;
    color: #1D4ED8;
}
.btn-tambah {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #2563EB;
    border-radius: 8px;
    border: none;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 12px;
    color: #fff;
    cursor: pointer;
    transition: background .2s;
}
.btn-tambah:hover { background: #1D4ED8; }

/* Portfolio card */
.porto-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    box-shadow: 0 2px 8px rgba(10,51,102,0.06);
    transition: box-shadow .2s;
    cursor: grab;
    position: relative;
}
.porto-card:hover { box-shadow: 0 4px 16px rgba(37,99,235,0.12); }
.porto-card.dragging { opacity: 0.5; cursor: grabbing; }
#porto-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.porto-num {
    width: 28px; height: 28px;
    background: #F1F5F9;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 12px;
    color: #64748B;
    flex-shrink: 0;
    margin-top: 2px;
}
.porto-thumb {
    width: 56px; height: 56px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 22px;
    flex-shrink: 0;
}
.porto-body {
    flex: 1;
    min-width: 0;
}
.porto-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}
.porto-title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 14px;
    color: #0F172A;
    margin: 0;
}
.chip {
    padding: 2px 10px;
    border-radius: 20px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 11px;
}
.chip-proyek     { background: #DBEAFE; color: #1D4ED8; }
.chip-prestasi   { background: #FEF3C7; color: #B45309; }
.chip-sertifikat { background: #DCFCE7; color: #16A34A; }

.porto-desc {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 400;
    font-size: 12px;
    color: #64748B;
    margin: 0 0 8px;
    line-height: 1.5;
}
.porto-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 10px;
}
.tag-chip {
    padding: 3px 10px;
    background: #F1F5F9;
    border-radius: 20px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 400;
    font-size: 11px;
    color: #475569;
}
.porto-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
/* Toggle Switch */
.toggle-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
}
.toggle-switch {
    position: relative;
    width: 36px;
    height: 20px;
    cursor: pointer;
    display: inline-block;
}
.toggle-switch input { display: none; }
.toggle-slider {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    border-radius: 20px;
    background: #CBD5E1;
    transition: background .25s;
}
.toggle-slider::before {
    content: '';
    position: absolute;
    width: 14px; height: 14px;
    background: #fff;
    border-radius: 50%;
    top: 3px; left: 3px;
    transition: transform .25s;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.toggle-switch input:checked + .toggle-slider { background: #2563EB; }
.toggle-switch input:checked + .toggle-slider::before { transform: translateX(16px); }

.toggle-label {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 11px;
    color: #64748B;
    white-space: nowrap;
}
.toggle-label.on { color: #2563EB; }

.btn-edit {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 14px;
    border: 1px solid #2563EB;
    border-radius: 8px;
    background: transparent;
    color: #2563EB;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 12px;
    cursor: pointer;
    transition: all .2s;
}
.btn-edit:hover { background: #EFF6FF; }

.btn-hapus {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 14px;
    border: 1px solid #DC2626;
    border-radius: 8px;
    background: transparent;
    color: #DC2626;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 12px;
    cursor: pointer;
    transition: all .2s;
}
.btn-hapus:hover { background: #FEF2F2; }

/* Preview sidebar card */
.preview-card {
    background: #FFFFFF;
    border: 1px solid #E1E7EF;
    border-radius: 17px;
    overflow: hidden;
    padding: 20px 18px;
}
.preview-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
}
.preview-avatar {
    width: 44px; height: 44px;
    background: #7C3AED;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 18px;
    flex-shrink: 0;
}
.preview-name {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 14px;
    color: #0F172A;
    margin: 0 0 2px;
}
.preview-sub {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 400;
    font-size: 11px;
    color: #64748B;
    margin: 0;
}
.preview-section-label {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 11px;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin: 0 0 10px;
}
.preview-items {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
    list-style: none;
    padding: 0;
}
.preview-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #F6F9FE;
    border-radius: 10px;
    padding: 10px 12px;
}
.preview-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}
.preview-item-name {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 500;
    font-size: 12px;
    color: #1E293B;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.btn-lihat-semua {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    padding: 9px;
    border: 1px solid #2563EB;
    border-radius: 8px;
    background: transparent;
    color: #2563EB;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 12px;
    cursor: pointer;
    text-decoration: none;
    transition: background .2s;
}
.btn-lihat-semua:hover { background: #EFF6FF; }

/* ── Empty state ── */
.sp-empty {
    text-align: center;
    padding: 48px 20px;
    color: #94A3B8;
}
.sp-empty svg { margin-bottom: 12px; opacity: .6; }
.sp-empty p { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; }

/* ── Toast / alert ── */
.sp-toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    background: #0F172A;
    color: #fff;
    padding: 12px 22px;
    border-radius: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 500;
    box-shadow: 0 4px 16px rgba(0,0,0,.2);
    display: none;
    z-index: 9000;
    animation: slideUp .3s ease;
}
.sp-toast.success { background: #16A34A; }
.sp-toast.error   { background: #DC2626; }
@keyframes slideUp {
    from { opacity:0; transform: translateY(16px); }
    to   { opacity:1; transform: translateY(0);    }
}

/* ═══════════════════════════════════════════════════════════
   MODALS
═══════════════════════════════════════════════════════════ */
.sp-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 8000;
    display: none;
}
.sp-modal-overlay.open { display: flex; }

.sp-modal {
    background: #fff;
    border-radius: 16px;
    width: 700px;
    max-width: 95vw;
    max-height: 90vh;
    overflow-y: auto;
    padding: 32px 36px;
    position: relative;
}
.modal-close {
    position: absolute;
    top: 18px; right: 22px;
    background: none; border: none;
    font-size: 22px; color: #64748B;
    cursor: pointer; line-height: 1;
}
.modal-close:hover { color: #0F172A; }

.modal-title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 20px;
    color: #0F172A;
    margin: 0 0 24px;
}
.form-group {
    margin-bottom: 18px;
}
.form-label {
    display: block;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 13px;
    color: #374151;
    margin-bottom: 6px;
}
.form-label span.req { color: #DC2626; }
.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #CBD5E1;
    border-radius: 8px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    color: #0F172A;
    background: #F8FAFC;
    outline: none;
    transition: border-color .2s;
    box-sizing: border-box;
}
.form-control:focus { border-color: #2563EB; background: #fff; }

textarea.form-control { resize: vertical; min-height: 80px; }

/* Tipe radio */
.tipe-radio-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.tipe-radio-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border: 1.5px solid #E2E8F0;
    border-radius: 8px;
    cursor: pointer;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 13px;
    color: #374151;
    transition: all .2s;
    user-select: none;
}
.tipe-radio-btn input { display: none; }
.tipe-radio-btn.selected { border-color: #2563EB; background: #EFF6FF; color: #2563EB; }

/* Toggle inside modal */
.modal-toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    background: #F8FAFC;
    border-radius: 10px;
    border: 1px solid #E2E8F0;
}
.modal-toggle-label {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 13px;
    color: #374151;
}
.modal-toggle-label small {
    display: block;
    font-weight: 400;
    font-size: 11px;
    color: #94A3B8;
}

/* Modal footer */
.modal-footer {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid #F1F5F9;
}
.btn-cancel {
    padding: 10px 24px;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    background: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 13px;
    color: #374151;
    cursor: pointer;
    transition: background .2s;
}
.btn-cancel:hover { background: #F8FAFC; }

.btn-submit-modal {
    padding: 10px 28px;
    border: none;
    border-radius: 8px;
    background: #2563EB;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 13px;
    color: #fff;
    cursor: pointer;
    transition: background .2s;
}
.btn-submit-modal:hover { background: #1D4ED8; }

/* Confirm dialog */
.sp-confirm {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.45);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9000;
}
.sp-confirm.open { display: flex; }
.sp-confirm-box {
    background: #fff;
    border-radius: 14px;
    padding: 32px 36px;
    width: 400px;
    max-width: 95vw;
    text-align: center;
}
.sp-confirm-box h3 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 18px;
    color: #0F172A;
    margin: 0 0 10px;
}
.sp-confirm-box p {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    color: #64748B;
    margin: 0 0 24px;
}
.sp-confirm-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}
.btn-confirm-cancel {
    padding: 9px 22px;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    background: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
}
.btn-confirm-delete {
    padding: 9px 22px;
    border: none;
    border-radius: 8px;
    background: #DC2626;
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
}
.btn-confirm-delete:hover { background: #B91C1C; }

/* Responsive */
@media (max-width: 1200px) {
    .sp-main { width: 95%; }
}
@media (max-width: 760px) {
    .sp-header { align-items: flex-start; gap: 12px; }
    .sp-tabs { align-items: flex-start; flex-direction: column; gap: 12px; }
    .tab-pills { max-width: 100%; overflow-x: auto; }
    .porto-card { display: grid; grid-template-columns: 28px 56px minmax(0, 1fr); }
    .porto-body { grid-column: 2 / -1; }
    .porto-num { grid-row: 1; }
    .porto-thumb { grid-row: 1; }
}
@media (max-width: 900px) {
    .sp-body { flex-direction: column; }
    .sp-preview-col { width: 100%; }
}
</style>

{{-- ── Page body ── --}}
<div class="sp-page">
    <div class="sp-main">

        {{-- Flash messages --}}
        @if(session('success'))
        <div id="flash-msg" data-msg="{{ session('success') }}" data-type="success"></div>
        @endif

        {{-- ── Header ── --}}
        <div class="sp-header">
            <div class="sp-header-judul">
                <h1>Susunan Portofolio</h1>
                <p>Atur urutan dan tampilan portofolio Anda agar menarik bagi tim lomba.</p>
            </div>
            <button class="btn-simpan-susunan" onclick="simpanSusunan()">
                <i data-lucide="save" style="width:14px;height:14px;"></i>
                Simpan Susunan
            </button>
        </div>

        {{-- ── Body: list + preview ── --}}
        <div class="sp-body">
            {{-- ── Left: list ── --}}
            <div class="sp-list-col">

                {{-- Tab bar --}}
                <div class="sp-tabs">
                    <div class="tab-pills">
                        <button class="tab-pill active" data-tab="Semua" onclick="switchTab(this,'Semua')">Semua</button>
                        <button class="tab-pill" data-tab="Proyek" onclick="switchTab(this,'Proyek')">Proyek</button>
                        <button class="tab-pill" data-tab="Prestasi" onclick="switchTab(this,'Prestasi')">Prestasi</button>
                        <button class="tab-pill" data-tab="Sertifikat" onclick="switchTab(this,'Sertifikat')">Sertifikat</button>
                    </div>
                    <button class="btn-tambah" onclick="openTambah()">
                        <i data-lucide="plus" style="width:14px;height:14px;"></i>
                        Tambah Portofolio
                    </button>
                </div>

                {{-- Portfolio list --}}
                <div id="porto-list">
                    @forelse($portfolios as $p)
                    <div class="porto-card"
                         data-id="{{ $p['id'] }}"
                         data-tipe="{{ $p['tipe'] }}"
                         draggable="true"
                         ondragstart="dragStart(event)"
                         ondragover="dragOver(event)"
                         ondrop="drop(event)"
                         ondragend="dragEnd(event)">

                        {{-- Number badge --}}
                        <div class="porto-num">{{ $p['urutan'] }}</div>

                        {{-- Thumbnail --}}
                        <div class="porto-thumb" style="background:{{ $p['warna'] }};">
                            @if (!empty($p['bukti_url']))
                                <img src="{{ $p['bukti_url'] }}" alt="Bukti {{ $p['judul'] }}" style="width:100%;height:100%;object-fit:cover;border-radius:10px">
                            @else
                                {{ $p['inisial'] }}
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="porto-body">
                            <div class="porto-title-row">
                                <p class="porto-title">{{ $p['judul'] }}</p>
                                <span class="chip chip-{{ strtolower($p['tipe']) }}">{{ $p['tipe'] }}</span>
                                <span class="chip" style="background:{{ $p['status'] === 'approved' ? '#DCFCE7' : ($p['status'] === 'rejected' ? '#FEE2E2' : '#FEF3C7') }};color:{{ $p['status'] === 'approved' ? '#166534' : ($p['status'] === 'rejected' ? '#991B1B' : '#92400E') }}">
                                    {{ $p['status'] === 'approved' ? 'Divalidasi' : ($p['status'] === 'rejected' ? 'Ditolak' : 'Menunggu validasi') }}
                                </span>
                            </div>
                            <p class="porto-desc">{{ $p['deskripsi'] }}</p>
                            <div class="porto-tags">
                                @foreach($p['tags'] as $tag)
                                <span class="tag-chip">{{ $tag }}</span>
                                @endforeach
                            </div>

                            {{-- Actions row --}}
                            <div class="porto-actions">
                                {{-- Toggle --}}
                                <div class="toggle-wrap">
                                    <form method="POST"
                                          action="{{ route('portofolio.toggle', $p['id']) }}"
                                          id="toggle-form-{{ $p['id'] }}"
                                          style="display:inline;">
                                        @csrf
                                        <label class="toggle-switch" title="{{ $p['ditampilkan'] ? 'Sembunyikan' : 'Tampilkan' }}">
                                            <input type="checkbox"
                                                   id="toggle-{{ $p['id'] }}"
                                                   {{ $p['ditampilkan'] ? 'checked' : '' }}
                                                   onchange="toggleItem({{ $p['id'] }}, this)">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </form>
                                    <span class="toggle-label {{ $p['ditampilkan'] ? 'on' : '' }}"
                                          id="toggle-lbl-{{ $p['id'] }}">
                                        {{ $p['ditampilkan'] ? 'Ditampilkan' : 'Draft' }}
                                    </span>
                                </div>

                                {{-- Edit --}}
                                <button class="btn-edit"
                                        onclick='openEdit({{ json_encode($p) }})'>
                                    <i data-lucide="pencil" style="width:12px;height:12px;"></i>
                                    Edit
                                </button>

                                {{-- Hapus --}}
                                <button class="btn-hapus"
                                        onclick="openHapus({{ $p['id'] }}, '{{ addslashes($p['judul']) }}')">
                                    <i data-lucide="trash-2" style="width:12px;height:12px;"></i>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="sp-empty">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg>
                        <p>Belum ada portofolio. Tambahkan karya pertamamu!</p>
                    </div>
                    @endforelse
                </div>

            </div>{{-- /sp-list-col --}}

            {{-- ── Right: Preview sidebar ── --}}
            @php
                $topPortofolios = collect($portfolios)->where('ditampilkan', true)->take(3)->values()->all();
                $userName = $user['name'] ?? 'Dzaky';
                $userInitial = strtoupper(mb_substr($userName, 0, 1));
                $userProdi = $user['prodi'] ?? 'TRPL';
                $userAngkatan = $user['angkatan'] ?? '2025';
            @endphp
            <div class="sp-preview-col">
                <div class="preview-card">
                    <div class="preview-card-header">
                        <div class="preview-avatar">{{ $userInitial }}</div>
                        <div>
                            <p class="preview-name">{{ $userName }}</p>
                            <p class="preview-sub">Mahasiswa {{ $userProdi }}, Angkatan {{ $userAngkatan }}</p>
                        </div>
                    </div>
                    <p class="preview-section-label">3 portofolio teratas</p>
                    <ul class="preview-items" id="preview-list">
                        @forelse($topPortofolios as $tp)
                        <li class="preview-item">
                            <div class="preview-dot" style="background:{{ $tp['warna'] }};"></div>
                            <span class="preview-item-name">{{ $tp['judul'] }}</span>
                        </li>
                        @empty
                        <li style="font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;color:#94A3B8;text-align:center;padding:16px 0;">
                            Belum ada portofolio ditampilkan.
                        </li>
                        @endforelse
                    </ul>
                    <a href="{{ route('portofolio.public') }}" class="btn-lihat-semua">
                        <i data-lucide="external-link" style="width:13px;height:13px;"></i>
                        Lihat Semua Portofolio
                    </a>
                </div>
            </div>

        </div>{{-- /sp-body --}}
    </div>{{-- /sp-main --}}
</div>{{-- /sp-page --}}

{{-- ════════════════════════════════════
     MODAL: Tambah Portofolio
════════════════════════════════════ --}}
<div class="sp-modal-overlay" id="modal-tambah">
    <div class="sp-modal">
        <button class="modal-close" onclick="closeTambah()">×</button>
        <h2 class="modal-title">Tambah Portofolio</h2>
        <form method="POST" action="{{ route('portofolio.store') }}" id="form-tambah" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Judul <span class="req">*</span></label>
                <input type="text" name="judul" class="form-control" placeholder="cth. Aplikasi Skill Link" required>
            </div>

            <div class="form-group">
                <label class="form-label">Tipe <span class="req">*</span></label>
                <div class="tipe-radio-group" id="tipe-radio-tambah">
                    @foreach(['Proyek','Prestasi','Sertifikat'] as $t)
                    <label class="tipe-radio-btn {{ $t === 'Proyek' ? 'selected' : '' }}">
                        <input type="radio" name="tipe" value="{{ $t }}" {{ $t === 'Proyek' ? 'checked' : '' }}
                               onchange="selectTipe(this,'tipe-radio-tambah')">
                        {{ $t }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" placeholder="Deskripsi singkat karya Anda..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Tags <small style="font-weight:400;color:#94A3B8;">(pisahkan dengan koma)</small></label>
                <input type="text" name="tags" class="form-control" placeholder="cth. Flutter, Firebase, UI/UX">
            </div>

            <div class="form-group">
                <label class="form-label">Foto Bukti <span class="req">*</span></label>
                <input type="file" name="bukti" class="form-control" accept="image/jpeg,image/png,image/webp" required>
                <small style="color:#64748B;">Unggah sertifikat, piagam, tangkapan proyek, atau bukti prestasi (maks. 5 MB).</small>
            </div>

            <div class="form-group">
                <div class="modal-toggle-row">
                    <div class="modal-toggle-label">
                        Tampilkan di Profil
                        <small>Karya ini akan muncul di profil publik Anda</small>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="ditampilkan" value="1" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeTambah()">Batal</button>
                <button type="submit" class="btn-submit-modal">
                    <i data-lucide="plus" style="width:14px;height:14px;vertical-align:middle;"></i>
                    Tambahkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════
     MODAL: Edit Portofolio
════════════════════════════════════ --}}
<div class="sp-modal-overlay" id="modal-edit">
    <div class="sp-modal">
        <button class="modal-close" onclick="closeEdit()">×</button>
        <h2 class="modal-title">Edit Portofolio</h2>
        <form method="POST" id="form-edit" action="" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Judul <span class="req">*</span></label>
                <input type="text" name="judul" id="edit-judul" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Tipe <span class="req">*</span></label>
                <div class="tipe-radio-group" id="tipe-radio-edit">
                    @foreach(['Proyek','Prestasi','Sertifikat'] as $t)
                    <label class="tipe-radio-btn">
                        <input type="radio" name="tipe" value="{{ $t }}"
                               onchange="selectTipe(this,'tipe-radio-edit')">
                        {{ $t }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" id="edit-deskripsi" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Tags <small style="font-weight:400;color:#94A3B8;">(pisahkan dengan koma)</small></label>
                <input type="text" name="tags" id="edit-tags" class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Foto Bukti Baru</label>
                <input type="file" name="bukti" class="form-control" accept="image/jpeg,image/png,image/webp">
                <small style="color:#64748B;">Kosongkan jika bukti lama masih berlaku.</small>
            </div>

            <div class="form-group">
                <div class="modal-toggle-row">
                    <div class="modal-toggle-label">
                        Tampilkan di Profil
                        <small>Karya ini akan muncul di profil publik Anda</small>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="ditampilkan" id="edit-ditampilkan" value="1">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEdit()">Batal</button>
                <button type="submit" class="btn-submit-modal">
                    <i data-lucide="save" style="width:14px;height:14px;vertical-align:middle;"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════
     CONFIRM: Hapus Portofolio
════════════════════════════════════ --}}
<div class="sp-confirm" id="confirm-hapus">
    <div class="sp-confirm-box">
        <h3>Hapus Portofolio?</h3>
        <p id="confirm-hapus-msg">Karya ini akan dihapus secara permanen.</p>
        <div class="sp-confirm-actions">
            <button class="btn-confirm-cancel" onclick="closeHapus()">Batal</button>
            <form method="POST" id="form-hapus" action="" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

{{-- Toast --}}
<div class="sp-toast" id="sp-toast"></div>

<script>
/* ══════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════ */
function showToast(msg, type = 'success') {
    const t = document.getElementById('sp-toast');
    t.textContent = msg;
    t.className = 'sp-toast ' + type;
    t.style.display = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3200);
}

/* Flash message from server */
(function(){
    const el = document.getElementById('flash-msg');
    if (el) showToast(el.dataset.msg, el.dataset.type || 'success');
})();

/* ══════════════════════════════════════════════════════
   TABS
══════════════════════════════════════════════════════ */
function switchTab(btn, tipe) {
    document.querySelectorAll('.tab-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('.porto-card').forEach(card => {
        if (tipe === 'Semua' || card.dataset.tipe === tipe) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

/* ══════════════════════════════════════════════════════
   TOGGLE VISIBILITY
══════════════════════════════════════════════════════ */
function toggleItem(id, checkbox) {
    const lbl = document.getElementById('toggle-lbl-' + id);
    // Optimistic UI update
    if (checkbox.checked) {
        lbl.textContent = 'Ditampilkan';
        lbl.classList.add('on');
    } else {
        lbl.textContent = 'Draft';
        lbl.classList.remove('on');
    }

    // POST via fetch
    const url = '{{ url("/susunan-portofolio") }}/' + id + '/toggle';
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.ditampilkan ? 'Portofolio ditampilkan.' : 'Portofolio dijadikan draft.');
        updatePreviewSidebar();
    })
    .catch(() => showToast('Gagal memperbarui status.', 'error'));
}

/* Update preview sidebar dinamis */
function updatePreviewSidebar() {
    const previewList = document.getElementById('preview-list');
    if (! previewList) return;

    const visibleCards = Array.from(document.querySelectorAll('.porto-card'))
        .filter(card => document.getElementById('toggle-' + card.dataset.id)?.checked)
        .slice(0, 3);

    previewList.innerHTML = visibleCards.length
        ? visibleCards.map(card => {
            const title = card.querySelector('.porto-title')?.textContent || '';
            const color = getComputedStyle(card.querySelector('.porto-thumb')).backgroundColor;
            return '<li class="preview-item"><div class="preview-dot" style="background:' + color + '"></div><span class="preview-item-name"></span></li>';
        }).join('')
        : '<li style="font-family:\'Plus Jakarta Sans\',sans-serif;font-size:12px;color:#94A3B8;text-align:center;padding:16px 0;">Belum ada portofolio ditampilkan.</li>';

    Array.from(previewList.querySelectorAll('.preview-item')).forEach((item, index) => {
        item.querySelector('.preview-item-name').textContent =
            visibleCards[index].querySelector('.porto-title')?.textContent || '';
    });
}

/* ══════════════════════════════════════════════════════
   MODAL TAMBAH
══════════════════════════════════════════════════════ */
function openTambah() {
    document.getElementById('modal-tambah').classList.add('open');
    lucide.createIcons();
}
function closeTambah() {
    document.getElementById('modal-tambah').classList.remove('open');
    document.getElementById('form-tambah').reset();
    // reset tipe selection
    document.querySelectorAll('#tipe-radio-tambah .tipe-radio-btn').forEach((b,i) => {
        b.classList.toggle('selected', i===0);
    });
}

/* ══════════════════════════════════════════════════════
   MODAL EDIT
══════════════════════════════════════════════════════ */
function openEdit(p) {
    const form = document.getElementById('form-edit');
    form.action = '{{ url("/susunan-portofolio") }}/' + p.id;

    document.getElementById('edit-judul').value = p.judul;
    document.getElementById('edit-deskripsi').value = p.deskripsi;
    document.getElementById('edit-tags').value = Array.isArray(p.tags) ? p.tags.join(', ') : p.tags;
    document.getElementById('edit-ditampilkan').checked = p.ditampilkan;

    // Set tipe
    document.querySelectorAll('#tipe-radio-edit .tipe-radio-btn').forEach(btn => {
        const inp = btn.querySelector('input');
        const isMatch = inp.value === p.tipe;
        inp.checked = isMatch;
        btn.classList.toggle('selected', isMatch);
    });

    document.getElementById('modal-edit').classList.add('open');
    lucide.createIcons();
}
function closeEdit() {
    document.getElementById('modal-edit').classList.remove('open');
}

/* ══════════════════════════════════════════════════════
   CONFIRM HAPUS
══════════════════════════════════════════════════════ */
function openHapus(id, judul) {
    document.getElementById('confirm-hapus-msg').textContent =
        `"${judul}" akan dihapus secara permanen.`;
    document.getElementById('form-hapus').action =
        '{{ url("/susunan-portofolio") }}/' + id;
    document.getElementById('confirm-hapus').classList.add('open');
}
function closeHapus() {
    document.getElementById('confirm-hapus').classList.remove('open');
}

/* ══════════════════════════════════════════════════════
   TIPE RADIO (visual only)
══════════════════════════════════════════════════════ */
function selectTipe(input, groupId) {
    document.querySelectorAll('#' + groupId + ' .tipe-radio-btn').forEach(btn => {
        btn.classList.remove('selected');
    });
    input.closest('.tipe-radio-btn').classList.add('selected');
}

/* ══════════════════════════════════════════════════════
   SIMPAN SUSUNAN (toast feedback)
══════════════════════════════════════════════════════ */
function simpanSusunan() {
    // Kirim urutan saat ini ke server
    const cards = document.querySelectorAll('#porto-list .porto-card');
    const order = Array.from(cards).map(c => parseInt(c.dataset.id));

    fetch('{{ route("portofolio.reorder") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ order })
    })
    .then(r => r.json())
    .then(() => showToast('Susunan berhasil disimpan! ✓'))
    .catch(() => showToast('Gagal menyimpan susunan.', 'error'));
}

/* ══════════════════════════════════════════════════════
   DRAG & DROP REORDER
══════════════════════════════════════════════════════ */
let dragSrc = null;

function dragStart(e) {
    dragSrc = e.currentTarget;
    dragSrc.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', '');
}

function dragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    const target = e.currentTarget;
    if (target !== dragSrc) {
        const list   = target.parentNode;
        const cards  = [...list.querySelectorAll('.porto-card')];
        const srcIdx = cards.indexOf(dragSrc);
        const tgtIdx = cards.indexOf(target);
        if (srcIdx < tgtIdx) {
            list.insertBefore(dragSrc, target.nextSibling);
        } else {
            list.insertBefore(dragSrc, target);
        }
        // Update number badges
        [...list.querySelectorAll('.porto-card')].forEach((c, i) => {
            c.querySelector('.porto-num').textContent = i + 1;
        });
    }
}

function drop(e) { e.preventDefault(); }

function dragEnd(e) {
    if (dragSrc) dragSrc.classList.remove('dragging');
    dragSrc = null;
}

/* ══════════════════════════════════════════════════════
   CLOSE MODALS ON BACKDROP CLICK
══════════════════════════════════════════════════════ */
document.getElementById('modal-tambah').addEventListener('click', function(e) {
    if (e.target === this) closeTambah();
});
document.getElementById('modal-edit').addEventListener('click', function(e) {
    if (e.target === this) closeEdit();
});
document.getElementById('confirm-hapus').addEventListener('click', function(e) {
    if (e.target === this) closeHapus();
});

/* ESC key */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTambah(); closeEdit(); closeHapus();
    }
});

/* Init Lucide icons */
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
@endsection
