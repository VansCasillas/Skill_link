@extends('layouts.app')

@section('title', 'Lihat Detail · ' . $team->name)

@section('content')
@php
    $deadline = '30 Nov 2026';
    $createdDate = $team->created_at ? $team->created_at->translatedFormat('d M Y') : '12 Okt 2026';
    $ownerName = $team->owner->name ?? 'Rezka';
    $ownerInitial = mb_strtoupper(mb_substr($ownerName, 0, 1));

    $approvedCount = $approvedMembers->count();
    $pendingCount = $pendingMembers->count();

    // Pastikan angka awal antara anggota, kuota, dan permintaan bergabung konsisten
    $hasDbMembers = $approvedCount > 0;
    $hasDbPending = $pendingCount > 0;

    $initialMembers = $hasDbMembers ? ($approvedCount + 1) : 3;
    $initialCapacity = max($initialMembers + 2, (int) ($team->capacity ?: 5));
    $initialPending = $hasDbPending ? $pendingCount : 5;
@endphp

<style>
/* ── Container Utama Detail Tim ────────────────────────────────────────── */
.team-detail-container {
    width: 100%;
    max-width: 1136px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 18px;
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    color: #0F172A;
}

.team-detail-container *,
.team-detail-container *::before,
.team-detail-container *::after {
    box-sizing: border-box;
}

/* ── Top Header ────────────────────────────────────────────────────────── */
.team-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 48px;
}

.team-page-header h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    line-height: 32px;
    color: #0F172A;
}

.team-btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    background: #fff;
    border: 1px solid #CBD5E1;
    border-radius: 8px;
    color: #0F172A;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all .15s ease;
}

.team-btn-back:hover {
    background: #F8FAFC;
    border-color: #94A3B8;
}

/* ── Hero / Banner Tim ─────────────────────────────────────────────────── */
.team-hero-card {
    background: #fff;
    border-radius: 16px;
    padding: 22px 26px;
    box-shadow: 0 2px 10px rgba(10, 51, 102, .06);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.team-hero-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.team-avatar-stack {
    display: flex;
    align-items: center;
}

.team-stack-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    display: grid;
    place-items: center;
    border: 2.5px solid #fff;
    margin-left: -10px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    transition: all .2s ease;
}

.team-stack-avatar:first-child {
    margin-left: 0;
}

.team-hero-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.team-hero-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
}

.team-hero-title-row h2 {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: #0F172A;
}

.team-status-badge {
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
    transition: all .2s ease;
}

.team-status-badge.open {
    background: #DCFCE7;
    color: #16A34A;
}

.team-status-badge.closed {
    background: #FEE2E2;
    color: #DC2626;
}

.team-hero-info p {
    margin: 0;
    font-size: 12px;
    color: #64748B;
}

.team-hero-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-hero-edit {
    padding: 8px 18px;
    background: #fff;
    border: 1px solid #CBD5E1;
    border-radius: 8px;
    color: #1E293B;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s ease;
}

.btn-hero-edit:hover {
    background: #F8FAFC;
    border-color: #94A3B8;
}

.btn-hero-toggle-recruitment {
    padding: 8px 18px;
    background: #fff;
    border: 1px solid #FECDD3;
    border-radius: 8px;
    color: #E11D48;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s ease;
}

.btn-hero-toggle-recruitment:hover {
    background: #FFF1F2;
    border-color: #FDA4AF;
}

.btn-hero-toggle-recruitment.is-closed {
    border-color: #BBF7D0;
    color: #16A34A;
}

.btn-hero-toggle-recruitment.is-closed:hover {
    background: #F0FDF4;
}

/* ── Content Grid 2 Kolom ──────────────────────────────────────────────── */
.team-content-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.8fr) minmax(0, 1fr);
    gap: 20px;
    align-items: start;
}

.team-col {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* ── Kartu Standar ─────────────────────────────────────────────────────── */
.team-card {
    background: #fff;
    border-radius: 16px;
    padding: 22px 24px;
    box-shadow: 0 2px 10px rgba(10, 51, 102, .06);
}

.team-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
}

.team-card-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #0F172A;
}

.team-slot-badge {
    color: #64748B;
    font-size: 12px;
    font-weight: 600;
}

.team-waiting-badge {
    background: #FEF3C7;
    color: #D97706;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    transition: all .2s ease;
}

.team-waiting-badge.empty {
    background: #F1F5F9;
    color: #64748B;
}

/* ── List Anggota Tim ──────────────────────────────────────────────────── */
.member-list {
    display: flex;
    flex-direction: column;
}

.member-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 0;
    border-bottom: 1px solid #F1F5F9;
    transition: all .25s ease;
}

.member-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.member-item:first-child {
    padding-top: 4px;
}

.member-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.member-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}

.member-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.member-name {
    font-size: 13.5px;
    font-weight: 700;
    color: #0F172A;
}

.member-role {
    font-size: 11.5px;
    color: #64748B;
}

.member-badge-leader {
    padding: 4px 14px;
    border-radius: 20px;
    background: #EFF6FF;
    color: #2563EB;
    font-size: 11px;
    font-weight: 600;
}

.btn-member-remove {
    padding: 6px 14px;
    border: 1px solid #FECDD3;
    border-radius: 8px;
    background: #fff;
    color: #EF4444;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s ease;
}

.btn-member-remove:hover {
    background: #FFF1F2;
    border-color: #FDA4AF;
}

/* ── List Permintaan Bergabung ─────────────────────────────────────────── */
.applicant-list {
    display: flex;
    flex-direction: column;
}

.applicant-item {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid #F1F5F9;
    transition: all .25s ease;
}

.applicant-item:last-child {
    border-bottom: none;
    padding-bottom: 4px;
}

.applicant-item:first-child {
    padding-top: 4px;
}

.applicant-left {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    flex: 1;
}

.applicant-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    margin-top: 2px;
}

.applicant-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.applicant-header-line {
    display: flex;
    align-items: center;
    gap: 8px;
}

.applicant-name {
    font-size: 13.5px;
    font-weight: 700;
    color: #0F172A;
}

.applicant-time {
    font-size: 11px;
    color: #94A3B8;
}

.applicant-apply-role {
    font-size: 12px;
    color: #64748B;
}

.applicant-skills {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 4px;
}

.applicant-skill-tag {
    padding: 2px 10px;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    font-size: 11px;
    color: #475569;
}

.applicant-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    margin-top: 4px;
}

.btn-applicant-accept {
    padding: 6px 16px;
    background: #16A34A;
    border: 1px solid #16A34A;
    border-radius: 8px;
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s ease;
}

.btn-applicant-accept:hover {
    background: #15803D;
}

.btn-applicant-reject {
    padding: 6px 16px;
    background: #fff;
    border: 1px solid #CBD5E1;
    border-radius: 8px;
    color: #334155;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s ease;
}

.btn-applicant-reject:hover {
    background: #F8FAFC;
    border-color: #94A3B8;
}

.empty-requests-msg {
    text-align: center;
    padding: 24px 12px;
    color: #94A3B8;
    font-size: 13px;
}

.link-all-requests {
    display: inline-block;
    margin-top: 14px;
    color: #2563EB;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
}

.link-all-requests:hover {
    text-decoration: underline;
}

/* ── Card Slot & Posisi ────────────────────────────────────────────────── */
.slot-metric-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12.5px;
    color: #64748B;
    margin-top: 6px;
}

.slot-metric-value {
    font-size: 13.5px;
    font-weight: 700;
    color: #0F172A;
}

.slot-progress-bar {
    width: 100%;
    height: 6px;
    background: #E2E8F0;
    border-radius: 99px;
    overflow: hidden;
    margin-top: 8px;
}

.slot-progress-fill {
    height: 100%;
    background: #2563EB;
    border-radius: 99px;
    transition: width .35s ease;
}

.positions-subheading {
    margin: 18px 0 8px;
    font-size: 13px;
    font-weight: 700;
    color: #0F172A;
}

.positions-list {
    display: flex;
    flex-direction: column;
}

.position-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 9px 0;
    font-size: 13px;
    color: #0F172A;
}

.position-slot-text {
    font-size: 11.5px;
    color: #64748B;
}

.btn-add-position {
    width: 100%;
    padding: 10px;
    background: #fff;
    border: 1px solid #93C5FD;
    border-radius: 8px;
    color: #2563EB;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all .15s ease;
}

.btn-add-position:hover {
    background: #EFF6FF;
    border-color: #60A5FA;
}

/* ── Card Status Rekrutmen ─────────────────────────────────────────────── */
.recruitment-toggle-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid #F1F5F9;
}

.recruitment-toggle-text strong {
    display: block;
    font-size: 13.5px;
    font-weight: 700;
    color: #0F172A;
}

.recruitment-toggle-text span {
    display: block;
    font-size: 11px;
    color: #64748B;
    margin-top: 2px;
}

/* Switch Toggle Styling */
.switch-label {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
}

.switch-label input {
    opacity: 0;
    width: 0;
    height: 0;
}

.switch-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #CBD5E1;
    transition: .25s ease;
    border-radius: 24px;
}

.switch-slider::before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .25s ease;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
}

.switch-label input:checked + .switch-slider {
    background-color: #2563EB;
}

.switch-label input:checked + .switch-slider::before {
    transform: translateX(20px);
}

.recruitment-meta-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.recruitment-meta-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12.5px;
}

.recruitment-meta-label {
    color: #64748B;
}

.recruitment-meta-val {
    font-weight: 700;
    color: #0F172A;
}

/* ── Modals / Dialogs ──────────────────────────────────────────────────── */
.app-modal {
    border: 0;
    border-radius: 20px;
    padding: 0;
    background: #fff;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,.15), 0 8px 10px -6px rgba(0,0,0,.1);
    max-width: 520px;
    width: min(520px, calc(100% - 32px));
}

.app-modal::backdrop {
    background: rgba(15, 23, 42, .45);
    backdrop-filter: blur(2px);
}

.app-modal-inner {
    padding: 24px 28px;
}

.app-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    position: relative;
}

.app-modal-header h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #0F172A;
}

.app-modal-header p {
    margin: 4px 0 0;
    font-size: 12px;
    color: #64748B;
}

.app-modal-close {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 0;
    background: #F1F5F9;
    color: #64748B;
    font-size: 16px;
    display: grid;
    place-items: center;
    cursor: pointer;
    position: absolute;
    top: 0;
    right: 0;
    transition: all .15s ease;
}

.app-modal-close:hover {
    background: #E2E8F0;
    color: #0F172A;
}

.modal-form-group {
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.modal-form-group label {
    font-size: 12px;
    font-weight: 700;
    color: #334155;
}

.modal-input,
.modal-select,
.modal-textarea {
    width: 100%;
    border: 1px solid #CBD5E1;
    border-radius: 9px;
    padding: 10px 14px;
    font-family: inherit;
    font-size: 13px;
    color: #0F172A;
    outline: none;
    transition: border-color .15s ease;
}

.modal-input:focus,
.modal-select:focus,
.modal-textarea:focus {
    border-color: #2563EB;
}

.modal-textarea {
    min-height: 86px;
    resize: vertical;
}

/* Stepper Control */
.stepper-control {
    display: inline-flex;
    align-items: center;
    border: 1px solid #CBD5E1;
    border-radius: 8px;
    overflow: hidden;
    width: fit-content;
}

.btn-stepper {
    width: 36px;
    height: 36px;
    background: #fff;
    border: 0;
    color: #334155;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    display: grid;
    place-items: center;
    transition: background .15s ease;
}

.btn-stepper:hover {
    background: #F1F5F9;
}

.stepper-val {
    width: 44px;
    height: 36px;
    border: 0;
    border-left: 1px solid #E2E8F0;
    border-right: 1px solid #E2E8F0;
    text-align: center;
    font-weight: 700;
    font-size: 13px;
    color: #0F172A;
    outline: none;
}

/* Skill Chips Selection */
.modal-skill-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 4px;
}

.skill-chip-btn {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    background: #fff;
    border: 1px solid #CBD5E1;
    color: #334155;
    transition: all .15s ease;
    user-select: none;
}

.skill-chip-btn.selected {
    background: #2563EB;
    border-color: #2563EB;
    color: #fff;
    font-weight: 600;
}

.skill-chip-add {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    cursor: pointer;
    background: #fff;
    border: 1px dashed #94A3B8;
    color: #475569;
    transition: all .15s ease;
}

.skill-chip-add:hover {
    border-color: #2563EB;
    color: #2563EB;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;
    margin-top: 24px;
}

.btn-modal-cancel {
    padding: 10px 22px;
    background: #fff;
    border: 1px solid #CBD5E1;
    border-radius: 9px;
    color: #334155;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}

.btn-modal-cancel:hover {
    background: #F8FAFC;
}

.btn-modal-submit {
    padding: 10px 22px;
    background: #2563EB;
    border: 1px solid #2563EB;
    border-radius: 9px;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s ease;
}

.btn-modal-submit:hover {
    background: #1D4ED8;
}

/* ── Success Modal ─────────────────────────────────────────────────────── */
.modal-success-box {
    text-align: center;
    padding: 32px 28px 28px;
}

.modal-success-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #22C55E;
    color: #fff;
    display: grid;
    place-items: center;
    margin: 0 auto 16px;
    box-shadow: 0 4px 12px rgba(34, 197, 94, .3);
}

.modal-success-icon svg {
    width: 30px;
    height: 30px;
}

.modal-success-box h2 {
    margin: 0;
    font-size: 19px;
    font-weight: 700;
    color: #0F172A;
}

.modal-success-box p {
    margin: 8px 0 0;
    font-size: 13px;
    color: #64748B;
    line-height: 20px;
}

.btn-success-done {
    width: 100%;
    padding: 12px;
    background: #2563EB;
    border: 0;
    border-radius: 10px;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    margin-top: 24px;
    cursor: pointer;
    transition: background .15s ease;
}

.btn-success-done:hover {
    background: #1D4ED8;
}

/* ── Toast Notification ────────────────────────────────────────────────── */
.app-toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    background: #0F172A;
    color: #fff;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,.2);
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 10px;
    opacity: 0;
    transform: translateY(12px);
    transition: all .25s ease;
    pointer-events: none;
}

.app-toast.show {
    opacity: 1;
    transform: translateY(0);
}

/* Responsive */
@media (max-width: 900px) {
    .team-content-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="team-detail-container">
    {{-- ── 1. Page Title & Back Button ──────────────────────────────────── --}}
    <div class="team-page-header">
        <h1>Lihat Detail</h1>
        <a class="team-btn-back" href="{{ route('tim.index') }}">
            &larr; Kembali
        </a>
    </div>

    {{-- ── 2. Top Hero Card ─────────────────────────────────────────────── --}}
    <div class="team-hero-card">
        <div class="team-hero-left">
            <div class="team-avatar-stack" id="team-avatar-stack">
                <span class="team-stack-avatar" style="background:#F97316">C</span>
                <span class="team-stack-avatar" style="background:#22C55E">D</span>
                <span class="team-stack-avatar" style="background:#2563EB">A</span>
            </div>
            <div class="team-hero-info">
                <div class="team-hero-title-row">
                    <h2>{{ $team->name }}</h2>
                    <span class="team-status-badge open" id="top-status-badge">Dibuka</span>
                </div>
                <p>{{ $team->competition ?: 'Hackathon AI' }} &middot; Deadline rekrutmen {{ $deadline }}</p>
            </div>
        </div>

        <div class="team-hero-actions">
            <button class="btn-hero-edit" type="button" id="btn-open-edit-team">
                Edit Tim
            </button>
            <button class="btn-hero-toggle-recruitment" type="button" id="btn-hero-toggle">
                Tutup Rekrutmen
            </button>
        </div>
    </div>

    {{-- ── 3. Content 2-Column Grid ─────────────────────────────────────── --}}
    <div class="team-content-grid">
        {{-- ── Kolom Kiri ── --}}
        <div class="team-col">
            {{-- Card Anggota Tim --}}
            <section class="team-card">
                <div class="team-card-header">
                    <h3>Anggota Tim</h3>
                    <span class="team-slot-badge" id="member-slot-counter">
                        {{ $initialMembers }}/{{ $initialCapacity }} slot
                    </span>
                </div>

                <div class="member-list" id="member-list">
                    {{-- Ketua Tim (Owner) --}}
                    <div class="member-item" data-member-id="leader">
                        <div class="member-left">
                            <span class="member-avatar" style="background:#7C3AED">{{ $ownerInitial }}</span>
                            <div class="member-info">
                                <span class="member-name">{{ $ownerName }}</span>
                                <span class="member-role">Ketua &middot; Mobile Developer</span>
                            </div>
                        </div>
                        <span class="member-badge-leader">Ketua</span>
                    </div>

                    {{-- Approved Members from Database --}}
                    @if ($hasDbMembers)
                        @foreach ($approvedMembers as $joinRequest)
                            @php
                                $mInitial = mb_strtoupper(mb_substr($joinRequest->user->name, 0, 1));
                                $colors = ['#10B981', '#3B82F6', '#F59E0B', '#EC4899'];
                                $mColor = $colors[$loop->index % count($colors)];
                            @endphp
                            <div class="member-item" data-member-id="{{ $joinRequest->id }}">
                                <div class="member-left">
                                    <span class="member-avatar" style="background:{{ $mColor }}">{{ $mInitial }}</span>
                                    <div class="member-info">
                                        <span class="member-name">{{ $joinRequest->user->name }}</span>
                                        <span class="member-role">Backend Developer</span>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('teams.members.remove', [$team, $joinRequest]) }}" class="form-remove-member">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-member-remove" type="submit">
                                        Keluarkan
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    @else
                        {{-- Default Initial Members from Figma Mockup --}}
                        <div class="member-item" data-member-id="demo-nadia">
                            <div class="member-left">
                                <span class="member-avatar" style="background:#22C55E">N</span>
                                <div class="member-info">
                                    <span class="member-name">Nadia Kusuma</span>
                                    <span class="member-role">Backend Developer</span>
                                </div>
                            </div>
                            <button class="btn-member-remove" type="button" onclick="handleRemoveMemberDirect(this, 'Nadia Kusuma')">
                                Keluarkan
                            </button>
                        </div>

                        <div class="member-item" data-member-id="demo-bima">
                            <div class="member-left">
                                <span class="member-avatar" style="background:#2563EB">B</span>
                                <div class="member-info">
                                    <span class="member-name">Bima Aditya</span>
                                    <span class="member-role">Mobile Developer</span>
                                </div>
                            </div>
                            <button class="btn-member-remove" type="button" onclick="handleRemoveMemberDirect(this, 'Bima Aditya')">
                                Keluarkan
                            </button>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Card Permintaan Bergabung --}}
            <section class="team-card">
                <div class="team-card-header">
                    <h3>Permintaan Bergabung</h3>
                    <span class="team-waiting-badge" id="waiting-badge-counter">
                        {{ $initialPending }} menunggu
                    </span>
                </div>

                <div class="applicant-list" id="applicant-list-container">
                    @if ($hasDbPending)
                        @foreach ($pendingMembers as $joinRequest)
                            @php
                                $aInitial = mb_strtoupper(mb_substr($joinRequest->user->name, 0, 1));
                                $aColors = ['#EC4899', '#F97316', '#0284C7', '#8B5CF6'];
                                $aColor = $aColors[$loop->index % count($aColors)];
                            @endphp
                            <div class="applicant-item" id="req-{{ $joinRequest->id }}" data-applicant-name="{{ $joinRequest->user->name }}" data-role="UI/UX Designer">
                                <div class="applicant-left">
                                    <span class="applicant-avatar" style="background:{{ $aColor }}">{{ $aInitial }}</span>
                                    <div class="applicant-info">
                                        <div class="applicant-header-line">
                                            <span class="applicant-name">{{ $joinRequest->user->name }}</span>
                                            <span class="applicant-time">{{ $joinRequest->created_at->diffForHumans() }}</span>
                                        </div>
                                        <span class="applicant-apply-role">Melamar sebagai UI/UX Designer</span>
                                        <div class="applicant-skills">
                                            <span class="applicant-skill-tag">Figma</span>
                                            <span class="applicant-skill-tag">UI/UX</span>
                                            <span class="applicant-skill-tag">Flutter</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="applicant-actions">
                                    <form method="POST" action="{{ route('teams.requests.approve', $joinRequest) }}" class="form-approve-req">
                                        @csrf
                                        <button class="btn-applicant-accept" type="submit">Terima</button>
                                    </form>
                                    <form method="POST" action="{{ route('teams.requests.reject', $joinRequest) }}" class="form-reject-req">
                                        @csrf
                                        <button class="btn-applicant-reject" type="submit">Tolak</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- 5 Default Applicants from Figma Mockup --}}
                        <div class="applicant-item" id="app-1" data-applicant-name="Rina Maharani" data-role="UI/UX Designer" data-color="#EC4899" data-initial="R">
                            <div class="applicant-left">
                                <span class="applicant-avatar" style="background:#EC4899">R</span>
                                <div class="applicant-info">
                                    <div class="applicant-header-line">
                                        <span class="applicant-name">Rina Maharani</span>
                                        <span class="applicant-time">2 jam lalu</span>
                                    </div>
                                    <span class="applicant-apply-role">Melamar sebagai UI/UX Designer</span>
                                    <div class="applicant-skills">
                                        <span class="applicant-skill-tag">Figma</span>
                                        <span class="applicant-skill-tag">UI/UX</span>
                                        <span class="applicant-skill-tag">Flutter</span>
                                    </div>
                                </div>
                            </div>
                            <div class="applicant-actions">
                                <button class="btn-applicant-accept" type="button" onclick="handleApplicantAccept('app-1')">Terima</button>
                                <button class="btn-applicant-reject" type="button" onclick="handleApplicantReject('app-1')">Tolak</button>
                            </div>
                        </div>

                        <div class="applicant-item" id="app-2" data-applicant-name="Dimas Saputra" data-role="Mobile Developer" data-color="#F97316" data-initial="D">
                            <div class="applicant-left">
                                <span class="applicant-avatar" style="background:#F97316">D</span>
                                <div class="applicant-info">
                                    <div class="applicant-header-line">
                                        <span class="applicant-name">Dimas Saputra</span>
                                        <span class="applicant-time">5 jam lalu</span>
                                    </div>
                                    <span class="applicant-apply-role">Melamar sebagai Mobile Developer</span>
                                    <div class="applicant-skills">
                                        <span class="applicant-skill-tag">Flutter</span>
                                        <span class="applicant-skill-tag">Laravel</span>
                                    </div>
                                </div>
                            </div>
                            <div class="applicant-actions">
                                <button class="btn-applicant-accept" type="button" onclick="handleApplicantAccept('app-2')">Terima</button>
                                <button class="btn-applicant-reject" type="button" onclick="handleApplicantReject('app-2')">Tolak</button>
                            </div>
                        </div>

                        <div class="applicant-item" id="app-3" data-applicant-name="Salsa Putri" data-role="UI/UX Designer" data-color="#0284C7" data-initial="S">
                            <div class="applicant-left">
                                <span class="applicant-avatar" style="background:#0284C7">S</span>
                                <div class="applicant-info">
                                    <div class="applicant-header-line">
                                        <span class="applicant-name">Salsa Putri</span>
                                        <span class="applicant-time">Kemarin</span>
                                    </div>
                                    <span class="applicant-apply-role">Melamar sebagai UI/UX Designer</span>
                                    <div class="applicant-skills">
                                        <span class="applicant-skill-tag">Figma</span>
                                        <span class="applicant-skill-tag">Riset UX</span>
                                    </div>
                                </div>
                            </div>
                            <div class="applicant-actions">
                                <button class="btn-applicant-accept" type="button" onclick="handleApplicantAccept('app-3')">Terima</button>
                                <button class="btn-applicant-reject" type="button" onclick="handleApplicantReject('app-3')">Tolak</button>
                            </div>
                        </div>

                        <div class="applicant-item" id="app-4" data-applicant-name="Farhan Ramadhan" data-role="Data Engineer" data-color="#8B5CF6" data-initial="F">
                            <div class="applicant-left">
                                <span class="applicant-avatar" style="background:#8B5CF6">F</span>
                                <div class="applicant-info">
                                    <div class="applicant-header-line">
                                        <span class="applicant-name">Farhan Ramadhan</span>
                                        <span class="applicant-time">Kemarin</span>
                                    </div>
                                    <span class="applicant-apply-role">Melamar sebagai Data Engineer</span>
                                    <div class="applicant-skills">
                                        <span class="applicant-skill-tag">Python</span>
                                        <span class="applicant-skill-tag">SQL</span>
                                    </div>
                                </div>
                            </div>
                            <div class="applicant-actions">
                                <button class="btn-applicant-accept" type="button" onclick="handleApplicantAccept('app-4')">Terima</button>
                                <button class="btn-applicant-reject" type="button" onclick="handleApplicantReject('app-4')">Tolak</button>
                            </div>
                        </div>

                        <div class="applicant-item" id="app-5" data-applicant-name="Alya Safira" data-role="Frontend Developer" data-color="#10B981" data-initial="A">
                            <div class="applicant-left">
                                <span class="applicant-avatar" style="background:#10B981">A</span>
                                <div class="applicant-info">
                                    <div class="applicant-header-line">
                                        <span class="applicant-name">Alya Safira</span>
                                        <span class="applicant-time">2 hari lalu</span>
                                    </div>
                                    <span class="applicant-apply-role">Melamar sebagai Frontend Developer</span>
                                    <div class="applicant-skills">
                                        <span class="applicant-skill-tag">React</span>
                                        <span class="applicant-skill-tag">Tailwind</span>
                                    </div>
                                </div>
                            </div>
                            <div class="applicant-actions">
                                <button class="btn-applicant-accept" type="button" onclick="handleApplicantAccept('app-5')">Terima</button>
                                <button class="btn-applicant-reject" type="button" onclick="handleApplicantReject('app-5')">Tolak</button>
                            </div>
                        </div>
                    @endif
                </div>

                <a class="link-all-requests" id="link-all-requests" href="#">
                    Lihat semua permintaan ({{ $initialPending }})
                </a>
            </section>
        </div>

        {{-- ── Kolom Kanan ── --}}
        <div class="team-col">
            {{-- Card Slot & Posisi --}}
            <section class="team-card">
                <div class="team-card-header">
                    <h3>Slot &amp; Posisi</h3>
                </div>

                <div class="slot-metric-line">
                    <span>Slot terisi</span>
                    <span class="slot-metric-value" id="slot-terisi-text">{{ $initialMembers }}/{{ $initialCapacity }}</span>
                </div>
                <div class="slot-progress-bar">
                    <div class="slot-progress-fill" id="slot-progress-fill" style="width: {{ round(($initialMembers / $initialCapacity) * 100) }}%"></div>
                </div>

                <div class="positions-subheading">Posisi terbuka</div>
                <div class="positions-list" id="positions-list">
                    <div class="position-row" data-pos-name="UI/UX Designer">
                        <span class="pos-title">UI/UX Designer</span>
                        <span class="position-slot-text"><span class="pos-num">1</span> slot</span>
                    </div>
                    <div class="position-row" data-pos-name="Data Engineer">
                        <span class="pos-title">Data Engineer</span>
                        <span class="position-slot-text"><span class="pos-num">1</span> slot</span>
                    </div>
                </div>

                <button class="btn-add-position" type="button" id="btn-open-add-position">
                    + Tambah Posisi
                </button>
            </section>

            {{-- Card Status Rekrutmen --}}
            <section class="team-card">
                <div class="team-card-header">
                    <h3>Status Rekrutmen</h3>
                </div>

                <div class="recruitment-toggle-row">
                    <div class="recruitment-toggle-text">
                        <strong id="toggle-status-label">Rekrutmen dibuka</strong>
                        <span>Mahasiswa lain bisa mengajukan gabung</span>
                    </div>
                    <label class="switch-label">
                        <input type="checkbox" id="recruitment-toggle-switch" checked>
                        <span class="switch-slider"></span>
                    </label>
                </div>

                <div class="recruitment-meta-list">
                    <div class="recruitment-meta-row">
                        <span class="recruitment-meta-label">Deadline</span>
                        <span class="recruitment-meta-val">{{ $deadline }}</span>
                    </div>
                    <div class="recruitment-meta-row">
                        <span class="recruitment-meta-label">Lomba</span>
                        <span class="recruitment-meta-val">{{ $team->competition ?: 'Hackathon AI' }}</span>
                    </div>
                    <div class="recruitment-meta-row">
                        <span class="recruitment-meta-label">Dibuat</span>
                        <span class="recruitment-meta-val">{{ $createdDate }}</span>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

{{-- ── 4. Modal Tambah Posisi (Image 2) ─────────────────────────────────── --}}
<dialog class="app-modal" id="modal-tambah-posisi" aria-labelledby="modal-posisi-title">
    <div class="app-modal-inner">
        <div class="app-modal-header">
            <div>
                <h2 id="modal-posisi-title">Tambah Posisi</h2>
                <p>Tentukan posisi baru yang dibutuhkan tim {{ $team->name }}.</p>
            </div>
            <button type="button" class="app-modal-close" id="btn-close-posisi-modal" aria-label="Tutup">&times;</button>
        </div>

        <form id="form-tambah-posisi">
            <div class="modal-form-group">
                <label for="posisi-nama">Nama Posisi *</label>
                <select class="modal-select" id="posisi-nama" required>
                    <option value="Frontend Developer" selected>Frontend Developer</option>
                    <option value="Backend Developer">Backend Developer</option>
                    <option value="Mobile Developer">Mobile Developer</option>
                    <option value="UI/UX Designer">UI/UX Designer</option>
                    <option value="Data Engineer">Data Engineer</option>
                    <option value="Machine Learning Engineer">Machine Learning Engineer</option>
                    <option value="DevOps Specialist">DevOps Specialist</option>
                    <option value="Quality Assurance">Quality Assurance</option>
                </select>
            </div>

            <div class="modal-form-group">
                <label>Jumlah Slot *</label>
                <div class="stepper-control">
                    <button type="button" class="btn-stepper" id="btn-slot-minus">&minus;</button>
                    <input type="text" class="stepper-val" id="slot-count-input" value="1" readonly>
                    <button type="button" class="btn-stepper" id="btn-slot-plus">&plus;</button>
                </div>
            </div>

            <div class="modal-form-group">
                <label>Skill yang Dibutuhkan</label>
                <div class="modal-skill-chips" id="skill-chips-container">
                    <button type="button" class="skill-chip-btn selected" data-skill="React">&check; React</button>
                    <button type="button" class="skill-chip-btn selected" data-skill="JavaScript">&check; JavaScript</button>
                    <button type="button" class="skill-chip-btn" data-skill="Tailwind">Tailwind</button>
                    <button type="button" class="skill-chip-btn" data-skill="Figma">Figma</button>
                    <button type="button" class="skill-chip-btn" data-skill="TypeScript">TypeScript</button>
                    <button type="button" class="skill-chip-add" id="btn-add-custom-skill">+ Skill lain</button>
                </div>
            </div>

            <div class="modal-form-group">
                <label for="posisi-desc">Deskripsi Singkat</label>
                <textarea class="modal-textarea" id="posisi-desc" placeholder="Jelaskan tugas dan tanggung jawab posisi ini..."></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" id="btn-cancel-posisi">Batal</button>
                <button type="submit" class="btn-modal-submit">Simpan Posisi</button>
            </div>
        </form>
    </div>
</dialog>

{{-- ── 5. Modal Berhasil Tambah Posisi (Image 3) ────────────────────────── --}}
<dialog class="app-modal" id="modal-success-posisi" aria-labelledby="modal-success-title">
    <div class="app-modal-inner modal-success-box">
        <div class="modal-success-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <h2 id="modal-success-title">Posisi berhasil ditambahkan</h2>
        <p id="modal-success-subtext">Frontend Developer (1 slot) kini terbuka untuk pelamar.</p>
        <button type="button" class="btn-success-done" id="btn-success-done">Selesai</button>
    </div>
</dialog>

{{-- ── 6. Modal Edit Tim (Existing) ────────────────────────────────────── --}}
<dialog class="app-modal" id="team-edit-dialog" aria-labelledby="team-edit-title">
    <div class="app-modal-inner">
        <div class="app-modal-header">
            <div>
                <h2 id="team-edit-title">Edit Tim</h2>
                <p>Perbarui informasi dan kebutuhan tim {{ $team->name }}.</p>
            </div>
            <button type="button" class="app-modal-close" id="close-team-edit" aria-label="Tutup">&times;</button>
        </div>
        <form method="POST" action="{{ route('teams.update', $team) }}">
            @csrf
            @method('PUT')
            <div class="modal-form-group">
                <label for="team-name">Nama Tim</label>
                <input class="modal-input" id="team-name" name="name" value="{{ $team->name }}" required maxlength="200">
            </div>
            <div class="modal-form-group">
                <label for="team-competition">Nama Lomba</label>
                <input class="modal-input" id="team-competition" name="competition" value="{{ $team->competition }}" required maxlength="200">
            </div>
            <div class="modal-form-group">
                <label for="team-capacity">Jumlah Slot Anggota</label>
                <input class="modal-input" id="team-capacity" name="capacity" type="number" min="{{ max(2, $team->members_count) }}" max="20" value="{{ $initialCapacity }}" required>
            </div>
            <div class="modal-form-group">
                <label for="team-description">Deskripsi</label>
                <textarea class="modal-textarea" id="team-description" name="description" maxlength="1000">{{ $team->description }}</textarea>
            </div>
            <div class="modal-form-group">
                <label for="team-skills">Skill (pisahkan dengan koma)</label>
                <input class="modal-input" id="team-skills" name="skills" value="{{ implode(', ', $team->skills ?? []) }}" maxlength="500">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" id="cancel-team-edit">Batal</button>
                <button type="submit" class="btn-modal-submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</dialog>

{{-- Floating Toast Notification --}}
<div class="app-toast" id="app-toast" role="alert">
    <span id="toast-message">Pembaruan berhasil disimpan.</span>
</div>

{{-- ── 7. JavaScript Interaktif & Reaktif ──────────────────────────────── --}}
<script>
// State terpusat untuk sinkronisasi jumlah anggota, kuota, dan permintaan
var teamState = {
    membersCount: {{ $initialMembers }},
    capacity: {{ $initialCapacity }},
    pendingCount: {{ $initialPending }},
    positions: [
        { name: 'UI/UX Designer', slots: 1 },
        { name: 'Data Engineer', slots: 1 }
    ]
};

// Render ulang semua teks slot, badge, dan progress bar secara otomatis
function renderTeamState() {
    var memberSlotBadge = document.getElementById('member-slot-counter');
    var slotTerisiText = document.getElementById('slot-terisi-text');
    var progressFill = document.getElementById('slot-progress-fill');
    var waitingBadge = document.getElementById('waiting-badge-counter');
    var linkAllRequests = document.getElementById('link-all-requests');

    // 1. Sinkronisasi Slot Anggota
    if (memberSlotBadge) {
        memberSlotBadge.textContent = teamState.membersCount + '/' + teamState.capacity + ' slot';
    }
    if (slotTerisiText) {
        slotTerisiText.textContent = teamState.membersCount + '/' + teamState.capacity;
    }
    if (progressFill) {
        var pct = teamState.capacity > 0 ? Math.min(100, Math.round((teamState.membersCount / teamState.capacity) * 100)) : 0;
        progressFill.style.width = pct + '%';
    }

    // 2. Sinkronisasi Jumlah Permintaan Menunggu
    if (waitingBadge) {
        waitingBadge.textContent = teamState.pendingCount + ' menunggu';
        if (teamState.pendingCount <= 0) {
            waitingBadge.classList.add('empty');
        } else {
            waitingBadge.classList.remove('empty');
        }
    }
    if (linkAllRequests) {
        if (teamState.pendingCount > 0) {
            linkAllRequests.style.display = 'inline-block';
            linkAllRequests.textContent = 'Lihat semua permintaan (' + teamState.pendingCount + ')';
        } else {
            linkAllRequests.style.display = 'none';
        }
    }

    // 3. Tampilkan pesan kosong jika semua permintaan sudah diproses
    var applicantContainer = document.getElementById('applicant-list-container');
    if (applicantContainer) {
        var remainingItems = applicantContainer.querySelectorAll('.applicant-item');
        if (remainingItems.length === 0) {
            var emptyNotice = document.getElementById('empty-req-notice');
            if (!emptyNotice) {
                var p = document.createElement('p');
                p.id = 'empty-req-notice';
                p.className = 'empty-requests-msg';
                p.textContent = 'Semua permintaan bergabung telah diproses.';
                applicantContainer.appendChild(p);
            }
        }
    }

    renderAvatarStack();
}

// Render avatar stack di hero banner agar sesuai jumlah anggota
function renderAvatarStack() {
    var stack = document.getElementById('team-avatar-stack');
    if (!stack) return;
    var colors = ['#F97316', '#22C55E', '#2563EB', '#14B8A6', '#8B5CF6', '#EC4899'];
    var initials = ['C', 'D', 'A', 'R', 'N', 'B', 'S'];
    var count = Math.min(5, Math.max(1, teamState.membersCount));
    var html = '';
    for (var i = 0; i < count; i++) {
        html += '<span class="team-stack-avatar" style="background:' + colors[i % colors.length] + '">' + initials[i % initials.length] + '</span>';
    }
    stack.innerHTML = html;
}

// Sinkronisasi data anggota & kuota ke database di server
function persistTeamStats() {
    fetch('{{ route('teams.sync-stats', $team) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            members_count: teamState.membersCount,
            capacity: teamState.capacity
        })
    }).catch(function(err) {
        console.warn('Sync stats error:', err);
    });
}

// Menampilkan Toast Pop-up
function showToast(msg) {
    var toast = document.getElementById('app-toast');
    var toastMsg = document.getElementById('toast-message');
    if (toast && toastMsg) {
        toastMsg.textContent = msg;
        toast.classList.add('show');
        setTimeout(function () {
            toast.classList.remove('show');
        }, 3000);
    }
}

// Handler ketika pemilik tim menerima pelamar
function handleApplicantAccept(rowId) {
    if (teamState.membersCount >= teamState.capacity) {
        alert('Kuota anggota tim sudah penuh (' + teamState.capacity + ' slot). Silakan tambah posisi atau perluas kuota terlebih dahulu.');
        return;
    }

    var row = document.getElementById(rowId);
    if (!row) return;

    var name = row.getAttribute('data-applicant-name') || 'Pelamar';
    var role = row.getAttribute('data-role') || 'Anggota Tim';
    var color = row.getAttribute('data-color') || '#2563EB';
    var initial = row.getAttribute('data-initial') || name.charAt(0).toUpperCase();

    // Animasi hapus dari daftar permintaan
    row.style.opacity = '0';
    row.style.transform = 'translateX(20px)';
    setTimeout(function () {
        row.remove();
    }, 220);

    // Update state: kurangi permintaan, tambah anggota
    teamState.pendingCount = Math.max(0, teamState.pendingCount - 1);
    teamState.membersCount++;

    // Tambahkan anggota baru ke daftar "Anggota Tim"
    var memberList = document.getElementById('member-list');
    if (memberList) {
        var newMember = document.createElement('div');
        newMember.className = 'member-item';
        newMember.style.opacity = '0';
        newMember.innerHTML = `
            <div class="member-left">
                <span class="member-avatar" style="background:${color}">${initial}</span>
                <div class="member-info">
                    <span class="member-name">${name}</span>
                    <span class="member-role">${role}</span>
                </div>
            </div>
            <button class="btn-member-remove" type="button" onclick="handleRemoveMemberDirect(this, '${name}')">
                Keluarkan
            </button>
        `;
        memberList.appendChild(newMember);
        setTimeout(function () {
            newMember.style.opacity = '1';
        }, 50);
    }

    // Kurangi 1 slot dari posisi terbuka jika cocok
    decrementOpenPosition(role);

    // Update UI responsif
    renderTeamState();
    persistTeamStats();
    showToast(name + ' berhasil diterima menjadi anggota tim!');
}

// Handler ketika pemilik tim menolak pelamar
function handleApplicantReject(rowId) {
    var row = document.getElementById(rowId);
    if (!row) return;

    var name = row.getAttribute('data-applicant-name') || 'Pelamar';

    row.style.opacity = '0';
    row.style.transform = 'translateX(-20px)';
    setTimeout(function () {
        row.remove();
    }, 220);

    // Update state: kurangi permintaan
    teamState.pendingCount = Math.max(0, teamState.pendingCount - 1);
    renderTeamState();
    showToast('Permintaan bergabung dari ' + name + ' ditolak.');
}

// Handler mengeluarkan anggota tim secara langsung
function handleRemoveMemberDirect(btn, name) {
    if (!confirm('Keluarkan ' + name + ' dari tim?')) return;

    var item = btn.closest('.member-item');
    if (item) {
        item.style.opacity = '0';
        setTimeout(function () {
            item.remove();
        }, 220);
    }

    // Update state: kurangi anggota
    teamState.membersCount = Math.max(1, teamState.membersCount - 1);
    renderTeamState();
    persistTeamStats();
    showToast(name + ' berhasil dikeluarkan dari tim.');
}

// Fungsi mengurangi slot posisi terbuka saat pelamar diterima
function decrementOpenPosition(role) {
    var rows = document.querySelectorAll('#positions-list .position-row');
    rows.forEach(function (row) {
        var title = row.querySelector('.pos-title');
        var numSpan = row.querySelector('.pos-num');
        if (title && numSpan && title.textContent.trim().toLowerCase() === role.trim().toLowerCase()) {
            var currentNum = parseInt(numSpan.textContent, 10) || 1;
            if (currentNum > 1) {
                numSpan.textContent = currentNum - 1;
            } else {
                row.style.opacity = '0.5';
                numSpan.textContent = '0';
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    renderTeamState();

    // ── Ajax Handler untuk Form Database (jika ada data real) ──
    document.querySelectorAll('.form-approve-req').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var row = form.closest('.applicant-item');
            var name = row.querySelector('.applicant-name')?.textContent || 'Pelamar';
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(function (res) {
                if (res.ok) {
                    handleApplicantAccept(row.id);
                } else {
                    form.submit();
                }
            }).catch(function () {
                handleApplicantAccept(row.id);
            });
        });
    });

    document.querySelectorAll('.form-reject-req').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var row = form.closest('.applicant-item');
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(function (res) {
                if (res.ok) {
                    handleApplicantReject(row.id);
                } else {
                    form.submit();
                }
            }).catch(function () {
                handleApplicantReject(row.id);
            });
        });
    });

    document.querySelectorAll('.form-remove-member').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var row = form.closest('.member-item');
            var name = row.querySelector('.member-name')?.textContent || 'Anggota';
            if (!confirm('Keluarkan ' + name + ' dari tim?')) return;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-HTTP-Method-Override': 'DELETE',
                    'Accept': 'application/json'
                }
            }).then(function (res) {
                if (res.ok) {
                    handleRemoveMemberDirect(form.querySelector('button'), name);
                } else {
                    form.submit();
                }
            }).catch(function () {
                handleRemoveMemberDirect(form.querySelector('button'), name);
            });
        });
    });

    // ── Edit Tim Modal ──
    var editDialog = document.getElementById('team-edit-dialog');
    var btnOpenEdit = document.getElementById('btn-open-edit-team');
    var btnCloseEdit = document.getElementById('close-team-edit');
    var btnCancelEdit = document.getElementById('cancel-team-edit');

    if (btnOpenEdit && editDialog) {
        btnOpenEdit.addEventListener('click', function () { editDialog.showModal(); });
    }
    if (btnCloseEdit && editDialog) {
        btnCloseEdit.addEventListener('click', function () { editDialog.close(); });
    }
    if (btnCancelEdit && editDialog) {
        btnCancelEdit.addEventListener('click', function () { editDialog.close(); });
    }
    if (editDialog) {
        editDialog.addEventListener('click', function (e) { if (e.target === editDialog) editDialog.close(); });
    }

    // ── Tambah Posisi Modal ──
    var modalPosisi = document.getElementById('modal-tambah-posisi');
    var btnOpenPosisi = document.getElementById('btn-open-add-position');
    var btnClosePosisi = document.getElementById('btn-close-posisi-modal');
    var btnCancelPosisi = document.getElementById('btn-cancel-posisi');

    if (btnOpenPosisi && modalPosisi) {
        btnOpenPosisi.addEventListener('click', function () { modalPosisi.showModal(); });
    }
    if (btnClosePosisi && modalPosisi) {
        btnClosePosisi.addEventListener('click', function () { modalPosisi.close(); });
    }
    if (btnCancelPosisi && modalPosisi) {
        btnCancelPosisi.addEventListener('click', function () { modalPosisi.close(); });
    }
    if (modalPosisi) {
        modalPosisi.addEventListener('click', function (e) { if (e.target === modalPosisi) modalPosisi.close(); });
    }

    // ── Success Modal ──
    var modalSuccess = document.getElementById('modal-success-posisi');
    var btnSuccessDone = document.getElementById('btn-success-done');

    if (btnSuccessDone && modalSuccess) {
        btnSuccessDone.addEventListener('click', function () { modalSuccess.close(); });
    }
    if (modalSuccess) {
        modalSuccess.addEventListener('click', function (e) { if (e.target === modalSuccess) modalSuccess.close(); });
    }

    // ── Stepper Slot ──
    var slotInput = document.getElementById('slot-count-input');
    var btnMinus = document.getElementById('btn-slot-minus');
    var btnPlus = document.getElementById('btn-slot-plus');

    if (btnMinus && slotInput) {
        btnMinus.addEventListener('click', function () {
            var val = parseInt(slotInput.value, 10) || 1;
            if (val > 1) slotInput.value = val - 1;
        });
    }
    if (btnPlus && slotInput) {
        btnPlus.addEventListener('click', function () {
            var val = parseInt(slotInput.value, 10) || 1;
            if (val < 10) slotInput.value = val + 1;
        });
    }

    // ── Skill Chips Selection ──
    var chipsContainer = document.getElementById('skill-chips-container');
    if (chipsContainer) {
        chipsContainer.addEventListener('click', function (e) {
            var btn = e.target.closest('.skill-chip-btn');
            if (btn) {
                btn.classList.toggle('selected');
                var skill = btn.getAttribute('data-skill');
                if (btn.classList.contains('selected')) {
                    btn.innerHTML = '&check; ' + skill;
                } else {
                    btn.textContent = skill;
                }
            }
        });
    }

    // ── Tambah Skill Lain ──
    var btnAddSkill = document.getElementById('btn-add-custom-skill');
    if (btnAddSkill && chipsContainer) {
        btnAddSkill.addEventListener('click', function () {
            var skillName = prompt('Masukkan nama skill baru:');
            if (skillName && skillName.trim() !== '') {
                var cleanName = skillName.trim();
                var newBtn = document.createElement('button');
                newBtn.type = 'button';
                newBtn.className = 'skill-chip-btn selected';
                newBtn.setAttribute('data-skill', cleanName);
                newBtn.innerHTML = '&check; ' + cleanName;
                chipsContainer.insertBefore(newBtn, btnAddSkill);
            }
        });
    }

    // ── Submit Form Tambah Posisi ──
    var formPosisi = document.getElementById('form-tambah-posisi');
    if (formPosisi) {
        formPosisi.addEventListener('submit', function (e) {
            e.preventDefault();
            var posName = document.getElementById('posisi-nama').value;
            var posSlot = parseInt(document.getElementById('slot-count-input').value, 10) || 1;

            if (modalPosisi) modalPosisi.close();

            // Tambahkan baris ke daftar "Posisi terbuka"
            var posList = document.getElementById('positions-list');
            if (posList) {
                var row = document.createElement('div');
                row.className = 'position-row';
                row.setAttribute('data-pos-name', posName);
                row.innerHTML = '<span class="pos-title">' + posName + '</span><span class="position-slot-text"><span class="pos-num">' + posSlot + '</span> slot</span>';
                posList.appendChild(row);
            }

            // Tambah kuota tim secara otomatis agar slot konsisten
            teamState.capacity += posSlot;
            renderTeamState();
            persistTeamStats();

            // Tampilkan modal sukses
            var subtext = document.getElementById('modal-success-subtext');
            if (subtext) {
                subtext.textContent = posName + ' (' + posSlot + ' slot) kini terbuka untuk pelamar.';
            }
            if (modalSuccess) {
                modalSuccess.showModal();
            }
        });
    }

    // ── Toggle Status Rekrutmen ──
    var toggleSwitch = document.getElementById('recruitment-toggle-switch');
    var toggleLabel = document.getElementById('toggle-status-label');
    var topBadge = document.getElementById('top-status-badge');
    var btnHeroToggle = document.getElementById('btn-hero-toggle');

    function updateRecruitmentStatus(isOpen) {
        if (isOpen) {
            toggleSwitch.checked = true;
            toggleLabel.textContent = 'Rekrutmen dibuka';
            topBadge.className = 'team-status-badge open';
            topBadge.textContent = 'Dibuka';
            btnHeroToggle.className = 'btn-hero-toggle-recruitment';
            btnHeroToggle.textContent = 'Tutup Rekrutmen';
            showToast('Rekrutmen tim berhasil dibuka.');
        } else {
            toggleSwitch.checked = false;
            toggleLabel.textContent = 'Rekrutmen ditutup';
            topBadge.className = 'team-status-badge closed';
            topBadge.textContent = 'Ditutup';
            btnHeroToggle.className = 'btn-hero-toggle-recruitment is-closed';
            btnHeroToggle.textContent = 'Buka Rekrutmen';
            showToast('Rekrutmen tim berhasil ditutup.');
        }
    }

    if (toggleSwitch) {
        toggleSwitch.addEventListener('change', function () {
            updateRecruitmentStatus(toggleSwitch.checked);
        });
    }

    if (btnHeroToggle) {
        btnHeroToggle.addEventListener('click', function () {
            var currentOpen = toggleSwitch ? toggleSwitch.checked : true;
            updateRecruitmentStatus(!currentOpen);
        });
    }
});
</script>
@endsection
