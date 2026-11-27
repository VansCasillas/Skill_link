@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<style>
    .pf {
        max-width: 1000px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
        color: #0F172A;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif
    }

    .pf *,
    .pf *::before,
    .pf *::after {
        box-sizing: border-box
    }

    .pf h1 {
        margin: 0;
        font-weight: 700;
        font-size: 30px;
        line-height: 38px
    }

    .pf-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px
    }

    .pf-cols {
        display: grid;
        grid-template-columns: 300px minmax(0, 1fr);
        gap: 20px;
        align-items: start
    }

    .pf-left {
        display: flex;
        flex-direction: column;
        gap: 20px
    }

    .pf-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(10, 51, 102, .08);
        padding: 24px 26px
    }

    /* Foto */
    .pf-photo {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px
    }

    .pf-avatar {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(10, 51, 102, .2);
        background: #14B8A6;
        display: grid;
        place-items: center;
        overflow: hidden;
        font-weight: 700;
        font-size: 40px;
        color: #0F172A
    }

    .pf-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block
    }

    .pf-name {
        font-weight: 700;
        font-size: 16px;
        line-height: 20px;
        text-align: center
    }

    .pf-sub {
        font-size: 11px;
        line-height: 14px;
        color: #475569;
        margin-top: -12px;
        text-align: center
    }

    .pf-photo-actions {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 8px
    }

    .pf-hint {
        font-size: 10px;
        line-height: 13px;
        color: #64748B;
        text-align: center
    }

    .pf-hint.err {
        color: #D92D20
    }

    /* Tombol */
    .pf-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        border-radius: 10px;
        border: 1px solid transparent;
        font: 500 12px/15px 'Plus Jakarta Sans', system-ui, sans-serif;
        cursor: pointer;
        white-space: nowrap
    }

    .pf-btn.primary {
        background: #0A87D9;
        color: #fff
    }

    .pf-btn.outline {
        background: #fff;
        color: #0A87D9;
        border-color: #0A87D9
    }

    .pf-btn.gray {
        background: #fff;
        color: #475569;
        border-color: #CBD5E1
    }

    .pf-btn:hover {
        filter: brightness(.95)
    }

    .pf-btn:focus-visible,
    .pf-input:focus-visible {
        outline: 3px solid rgba(10, 135, 217, .45);
        outline-offset: 2px
    }

    .pf-btn.block {
        width: 100%
    }

    /* Kelengkapan */
    .pf-prog {
        display: flex;
        flex-direction: column;
        gap: 10px
    }

    .pf-prog .lab {
        display: flex;
        justify-content: space-between;
        font-weight: 700;
        font-size: 12px;
        line-height: 15px
    }

    .pf-prog .lab b {
        color: #0A87D9
    }

    .pf-track {
        height: 8px;
        background: #DCE8F7;
        border-radius: 4px;
        overflow: hidden
    }

    .pf-track i {
        display: block;
        height: 100%;
        background: #0A87D9;
        border-radius: 4px
    }

    .pf-prog p {
        margin: 0;
        font-size: 11px;
        line-height: 14px;
        color: #475569
    }

    /* View-mode data card */
    .pf-data {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(10, 51, 102, .08);
        padding: 24px 26px
    }

    .pf-value {
        font: 500 12px/15px 'Plus Jakarta Sans', system-ui, sans-serif;
        color: #0F172A
    }

    .pf-value.bio {
        white-space: pre-wrap;
        line-height: 1.5
    }

    /* Portfolio list card (view mode) */
    .pf-portfolio {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(10, 51, 102, .08);
        padding: 24px 26px
    }

    .pf-portfolio-list {
        display: flex;
        flex-direction: column;
        gap: 12px
    }

    .pf-portfolio-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 12px 14px
    }

    .pf-portfolio-thumb {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #E2E8F0;
        display: grid;
        place-items: center;
        color: #fff;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0
    }

    .pf-portfolio-info {
        flex: 1;
        min-width: 0
    }

    .pf-portfolio-title {
        font-weight: 700;
        font-size: 13px;
        line-height: 16px;
        color: #0F172A;
        margin: 0 0 2px
    }

    .pf-portfolio-type {
        font-size: 10px;
        line-height: 13px;
        color: #64748B;
        margin: 0 0 6px;
        text-transform: uppercase;
        letter-spacing: .05em
    }

    .pf-portfolio-desc {
        font-size: 11px;
        line-height: 14px;
        color: #475569;
        margin: 0 0 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden
    }

    .pf-portfolio-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 8px
    }

    .pf-tag {
        padding: 2px 8px;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 20px;
        font-size: 10px;
        line-height: 13px;
        color: #1D4ED8;
        font-weight: 600
    }

    .pf-portfolio-img {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        overflow: hidden;
        background: #E2E8F0;
        flex-shrink: 0
    }

    .pf-portfolio-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block
    }

    /* Detail view (read-only data diri) */
    .pf-detail {
        display: flex;
        flex-direction: column;
        gap: 14px
    }

    .pf-detail-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px
    }

    .pf-detail-row.pf-detail-row-full {
        grid-template-columns: 1fr
    }

    .pf-detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px
    }

    .pf-detail-label {
        font-size: 10px;
        line-height: 13px;
        color: #64748B;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em
    }

    .pf-detail-value {
        font: 500 13px/16px 'Plus Jakarta Sans', system-ui, sans-serif;
        color: #0F172A
    }

    .pf-detail-bio {
        white-space: pre-wrap;
        line-height: 1.55
    }

    .pf-line-subtle {
        height: 1px;
        background: #F1F5F9;
        border: 0;
        margin: 0
    }

    /* Portfolio header within detail card */
    .pf-portfolio-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px
    }

    .pf-portfolio-header h3 {
        margin: 0;
        font-weight: 700;
        font-size: 15px;
        line-height: 19px;
        color: #0F172A
    }

    .pf-desc-small {
        margin: 0;
        font-size: 11px;
        line-height: 14px;
        color: #64748B;
        flex: 1
    }

    /* Portfolio item improvements */
    .pf-portfolio-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 12px 14px
    }

    .pf-portfolio-thumb {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #E2E8F0;
        display: grid;
        place-items: center;
        color: #fff;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0
    }

    .pf-portfolio-info {
        flex: 1;
        min-width: 0
    }

    .pf-portfolio-title {
        font-weight: 700;
        font-size: 13px;
        line-height: 16px;
        color: #0F172A;
        margin: 0 0 4px
    }

    .pf-portfolio-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 6px
    }

    .pf-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        line-height: 13px;
        font-weight: 600
    }

    .pf-badge-proyek { background: #DBEAFE; color: #1D4ED8; }
    .pf-badge-prestasi { background: #FEF3C7; color: #B45309; }
    .pf-badge-sertifikat { background: #DCFCE7; color: #16A34A; }
    .pf-badge-lain { background: #E2E8F0; color: #475569; }

    .pf-tag-small {
        padding: 1px 6px;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 20px;
        font-size: 9px;
        line-height: 12px;
        color: #1D4ED8;
        font-weight: 600
    }

    .pf-portfolio-desc {
        font-size: 11px;
        line-height: 14px;
        color: #475569;
        margin: 0 0 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden
    }

    .pf-portfolio-img {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        overflow: hidden;
        background: #E2E8F0;
        flex-shrink: 0
    }

    /* Empty portfolio state */
    .pf-empty-portfolio {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 32px 20px;
        text-align: center;
        color: #64748B
    }

    .pf-empty-icon {
        font-size: 32px
    }

    .pf-empty-portfolio p {
        margin: 0;
        font-size: 13px;
        line-height: 16px
    }

    .pf-btn-sm {
        padding: 6px 16px;
        font-size: 12px;
        line-height: 15px
    }

    /* Portfolio image overlay */
    .pf-img-overlay {
        position: absolute;
        inset: 0;
        background: rgba(10, 135, 217, .7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity .2s;
        border-radius: 8px
    }

    .pf-portfolio-img:hover .pf-img-overlay {
        opacity: 1
    }

    .pf-img-overlay span {
        color: #fff;
        font-weight: 700;
        font-size: 12px;
        background: rgba(0,0,0,.4);
        padding: 4px 10px;
        border-radius: 20px
    }

    /* Modal portfolio */
    .pf-modal-portfolio {
        position: relative;
        background: #fff;
        border-radius: 16px;
        overflow: hidden
    }

    .pf-modal-close {
        position: absolute;
        top: 12px;
        right: 14px;
        background: rgba(0,0,0,.2);
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        font-size: 24px;
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1
    }

    .pf-modal-close:hover {
        background: rgba(0,0,0,.4)
    }

    .pf-modal-portfolio-body {
        padding: 24px
    }

    .pf-modal-portfolio-body h3 {
        margin: 0 0 16px;
        font-weight: 700;
        font-size: 16px;
        line-height: 20px;
        color: #0F172A
    }

    /* Form */
    .pf-form {
        display: flex;
        flex-direction: column;
        gap: 16px
    }

    .pf-form h2 {
        margin: 0;
        font-weight: 700;
        font-size: 16px;
        line-height: 20px
    }

    .pf-form .desc {
        margin: 2px 0 0;
        font-size: 11px;
        line-height: 14px;
        color: #475569
    }

    .pf-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px
    }

    .pf-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0
    }

    .pf-field label {
        font-weight: 500;
        font-size: 11px;
        line-height: 14px
    }

    .pf-field label i {
        font-style: normal;
        font-weight: 700;
        color: #D92D20;
        margin-left: 3px
    }

    .pf-input {
        width: 100%;
        height: 40px;
        padding: 0 14px;
        background: #fff;
        border: 1px solid #475569;
        border-radius: 12px;
        font: 400 12px/15px 'Plus Jakarta Sans', system-ui, sans-serif;
        color: #0F172A
    }

    textarea.pf-input {
        height: 96px;
        padding: 12px 14px;
        resize: vertical
    }

    .pf-input::placeholder {
        color: rgba(15, 23, 42, .5)
    }

    .pf-input.invalid {
        border-color: #D92D20
    }

    .pf-error {
        font-size: 10px;
        line-height: 13px;
        color: #D92D20
    }

    .pf-count {
        font-size: 10px;
        line-height: 13px;
        color: #64748B;
        text-align: right
    }

    .pf-line {
        height: 1px;
        background: #E2E8F0;
        border: 0;
        margin: 0
    }

    .pf-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px
    }

    /* Dialog */
    .pf-scrim {
        position: fixed;
        inset: 0;
        z-index: 100;
        background: rgba(11, 27, 51, .55);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px
    }

    .pf-scrim.open {
        display: flex
    }

    .pf-dialog {
        width: 420px;
        max-width: 100%;
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 16px 48px rgba(10, 38, 89, .3);
        padding: 32px 32px 28px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        text-align: center
    }

    .pf-ico {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        margin-bottom: 4px
    }

    .pf-ico span {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        color: #fff;
        font-weight: 700;
        font-size: 24px;
        line-height: 1
    }

    .pf-ico.ask {
        background: #E0F0FC
    }

    .pf-ico.ask span {
        background: #0A87D9
    }

    .pf-ico.ok {
        background: #D1FADF
    }

    .pf-ico.ok span {
        background: #12B76A
    }

    .pf-ico.warn {
        background: #FEF0C7
    }

    .pf-ico.warn span {
        background: #F79009
    }

    .pf-dialog h3 {
        margin: 0;
        font-weight: 700;
        font-size: 19px;
        line-height: 24px
    }

    .pf-dialog p {
        margin: 0;
        font-size: 12px;
        line-height: 15px;
        color: #475569
    }

    .pf-dialog .btns {
        width: 100%;
        display: flex;
        gap: 12px;
        margin-top: 8px
    }

    .pf-dialog .btns button {
        flex: 1;
        padding: 12px 0;
        border-radius: 12px;
        border: 1px solid #0A87D9;
        font: 700 13px/16px 'Plus Jakarta Sans', system-ui, sans-serif;
        cursor: pointer
    }

    .pf-dialog .btns .no {
        background: #fff;
        color: #0A87D9
    }

    .pf-dialog .btns .yes {
        background: #0A87D9;
        color: #fff
    }

    @media (max-width:900px) {
        .pf-cols {
            grid-template-columns: 1fr
        }
    }

    @media (max-width:640px) {
        .pf-head {
            align-items: flex-start
        }

        .pf-row {
            grid-template-columns: 1fr
        }

        .pf h1 {
            font-size: 26px;
            line-height: 32px
        }

        .pf-card {
            padding: 20px 18px
        }

        .pf-portfolio-item {
            flex-direction: column;
            align-items: stretch
        }

        .pf-portfolio-img {
            width: 100%;
            height: 70px
        }

        .pf-detail-row {
            grid-template-columns: 1fr
        }
    }
</style>

@php
    $nama     = old('name',     $user['name']     ?? '');
    $inisial  = strtoupper(mb_substr($nama ?: 'U', 0, 1));
    $persen   = $stats['profil'] ?? 0;
    $foto     = $user['foto'] ?? null;
    $editMode = $editMode ?? false;
    $portfolios = $portfolios ?? [];
@endphp

<div class="pf">
    <div class="pf-head">
        <h1>Profil</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="pf-btn outline">Keluar</button>
        </form>
    </div>

    <form id="pf-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="hapus_foto" id="hapus-foto" value="0">
        <input type="file" name="foto" id="foto" accept="image/jpeg,image/png" hidden>

        <div class="pf-cols">
            {{-- Kolom kiri --}}
            <div class="pf-left">
                <section class="pf-card pf-photo">
                    <div class="pf-avatar" id="pf-avatar">
                        @if($foto)
                            <img src="{{ $foto }}" alt="Foto profil">
                        @else
                            {{ $inisial }}
                        @endif
                    </div>
                    <div class="pf-name">{{ $user['name'] ?? '' }}</div>
                    <div class="pf-sub">{{ $user['prodi'] ?? 'TRPL' }} · Angkatan {{ $user['angkatan'] ?? '' }}</div>

                    <div class="pf-photo-actions">
                        @if($editMode)
                            <button type="button" class="pf-btn primary block" id="btn-ubah-foto">Ubah Foto</button>
                            <button type="button" class="pf-btn gray block" id="btn-hapus-foto">Hapus Foto</button>
                        @else
                            <a href="{{ route('profil.edit') }}" class="pf-btn primary block">Edit Profil</a>
                        @endif
                    </div>
                    <div class="pf-hint" id="foto-hint">Format JPG atau PNG, maksimal 2 MB</div>
                    @error('foto')<div class="pf-hint err">{{ $message }}</div>@enderror
                </section>

                <section class="pf-card pf-prog">
                    <div class="lab"><span>Kelengkapan profil</span><b>{{ $persen }}%</b></div>
                    <div class="pf-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $persen }}"><i style="width:{{ $persen }}%"></i></div>
                    <p>Lengkapi skill dan portofolio agar mudah ditemukan tim lomba.</p>
                </section>
            </div>

            {{-- Kolom kanan --}}
            @if($editMode)
                <section class="pf-card pf-form">
            @else
                <section class="pf-card pf-data">
            @endif
                <div>
                    <h2>Data Diri</h2>
                    <p class="desc">@if($editMode) Isi data sesuai identitas mahasiswa. Tanda * wajib diisi. @else Data profil Anda. Ubah melalui tombol Edit Profil. @endif</p>
                </div>

                @if($editMode)
                <div class="pf-row">
                    <div class="pf-field">
                        <label for="name">Nama Lengkap<i>*</i></label>
                        <input class="pf-input @error('name') invalid @enderror" type="text" id="name" name="name" required
                            placeholder="Masukkan nama anda" value="{{ old('name', $user['name'] ?? '') }}">
                        @error('name')<span class="pf-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="pf-field">
                        <label for="npm">NPM<i>*</i></label>
                        <input class="pf-input @error('npm') invalid @enderror" type="text" id="npm" name="npm" required inputmode="numeric"
                            placeholder="Masukkan npm anda" value="{{ old('npm', $user['npm'] ?? '') }}">
                        @error('npm')<span class="pf-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="pf-row">
                    <div class="pf-field">
                        <label for="jurusan">Jurusan<i>*</i></label>
                        <input class="pf-input @error('jurusan') invalid @enderror" type="text" id="jurusan" name="jurusan" required
                            placeholder="Masukkan jurusan anda" value="{{ old('jurusan', $user['jurusan'] ?? '') }}">
                        @error('jurusan')<span class="pf-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="pf-field">
                        <label for="prodi">Program Studi<i>*</i></label>
                        <input class="pf-input @error('prodi') invalid @enderror" type="text" id="prodi" name="prodi" required
                            placeholder="Masukkan program studi anda" value="{{ old('prodi', $user['prodi'] ?? '') }}">
                        @error('prodi')<span class="pf-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="pf-row">
                    <div class="pf-field">
                        <label for="email">Email<i>*</i></label>
                        <input class="pf-input @error('email') invalid @enderror" type="email" id="email" name="email" required
                            placeholder="Masukkan email anda" value="{{ old('email', $user['email'] ?? '') }}">
                        @error('email')<span class="pf-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="pf-field">
                        <label for="angkatan">Angkatan<i>*</i></label>
                        <input class="pf-input @error('angkatan') invalid @enderror" type="text" id="angkatan" name="angkatan" required inputmode="numeric" maxlength="4"
                            placeholder="Masukkan angkatan anda" value="{{ old('angkatan', $user['angkatan'] ?? '') }}">
                        @error('angkatan')<span class="pf-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                @else
                <div class="pf-detail">
                    <div class="pf-detail-row">
                        <div class="pf-detail-item">
                            <div class="pf-detail-label">Nama Lengkap</div>
                            <div class="pf-detail-value">{{ $user['name'] ?? '-' }}</div>
                        </div>
                        <div class="pf-detail-item">
                            <div class="pf-detail-label">NPM</div>
                            <div class="pf-detail-value">{{ $user['npm'] ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="pf-detail-row">
                        <div class="pf-detail-item">
                            <div class="pf-detail-label">Jurusan</div>
                            <div class="pf-detail-value">{{ $user['jurusan'] ?? '-' }}</div>
                        </div>
                        <div class="pf-detail-item">
                            <div class="pf-detail-label">Program Studi</div>
                            <div class="pf-detail-value">{{ $user['prodi'] ?? 'TRPL' }}</div>
                        </div>
                    </div>
                    <div class="pf-detail-row">
                        <div class="pf-detail-item">
                            <div class="pf-detail-label">Email</div>
                            <div class="pf-detail-value">{{ $user['email'] ?? '-' }}</div>
                        </div>
                        <div class="pf-detail-item">
                            <div class="pf-detail-label">Angkatan</div>
                            <div class="pf-detail-value">{{ $user['angkatan'] ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="pf-detail-row pf-detail-row-full">
                        <div class="pf-detail-item">
                            <div class="pf-detail-label">Bio Singkat</div>
                            <div class="pf-detail-value pf-detail-bio">{{ $user['bio'] ?? '-' }}</div>
                        </div>
                    </div>
                    <hr class="pf-line pf-line-subtle">
                    <div class="pf-portfolio-header">
                        <h3>Portofolio</h3>
                        <p class="pf-desc-small">Karya yang Anda unggah melalui menu Susunan Portofolio.</p>
                    </div>
                    <div class="pf-portfolio-list">
                        @if(count($portfolios) > 0)
                            @foreach($portfolios as $p)
                            <div class="pf-portfolio-item">
                                <div class="pf-portfolio-thumb" style="background: {{ $p['warna'] ?? '#64748B' }};">
                                    {{ $p['inisial'] ?? '?' }}
                                </div>
                                <div class="pf-portfolio-info">
                                    <div class="pf-portfolio-title">{{ $p['judul'] }}</div>
                                    <div class="pf-portfolio-meta">
                                        <span class="pf-badge pf-badge-{{ strtolower(str_replace(' ', '-', $p['tipe'] ?? 'Lain')) }}">{{ $p['tipe'] }}</span>
                                        @if(!empty($p['tags']))
                                            @foreach($p['tags'] as $tag)
                                            <span class="pf-tag-small">{{ $tag }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                    @if(!empty($p['deskripsi']))
                                    <div class="pf-portfolio-desc">{{ $p['deskripsi'] }}</div>
                                    @endif
                                </div>
                                <div class="pf-portfolio-img" onclick="openPortfolioModal('{{ $p['bukti_url'] }}', '{{ $p['judul'] }}')">
                                    <img src="{{ $p['bukti_url'] }}" alt="{{ $p['judul'] }}">
                                    <div class="pf-img-overlay">
                                        <span>Lihat bukti</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="pf-empty-portfolio">
                                <span class="pf-empty-icon">📭</span>
                                <p>Belum ada portofolio yang diunggah.</p>
                                <a href="{{ route('portofolio.index') }}" class="pf-btn primary pf-btn-sm">Tambah Portofolio</a>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </section>

            {{-- Modal: bukti portofolio --}}
            <div class="pf-scrim" id="dlg-portfolio-modal" aria-hidden="true">
                <div class="pf-dialog" style="max-width: 700px; padding: 0;">
                    <div class="pf-modal-portfolio">
                        <button type="button" class="pf-modal-close" onclick="closePortfolioModal()">&times;</button>
                        <div class="pf-modal-portfolio-body">
                            <h3 id="modal-portfolio-title"></h3>
                            <img id="modal-portfolio-img" src="" alt="" style="width:100%;height:auto;border-radius:12px;display:block;margin:0 auto;">
                        </div>
                    </div>
                </div>
            </div>

    </form>
</div>

{{-- Dialog: konfirmasi simpan --}}
<div class="pf-scrim" id="dlg-simpan" aria-hidden="true">
    <div class="pf-dialog" role="dialog" aria-modal="true" aria-labelledby="t-simpan">
        <div class="pf-ico ask"><span>?</span></div>
        <h3 id="t-simpan">Simpan perubahan pada profil Anda?</h3>
        <p>Data diri yang Anda ubah akan diperbarui.</p>
        <div class="btns">
            <button type="button" class="no" data-close>Tidak</button>
            <button type="button" class="yes" id="yes-simpan">Ya</button>
        </div>
    </div>
</div>

{{-- Dialog: konfirmasi batal --}}
<div class="pf-scrim" id="dlg-batal" aria-hidden="true">
    <div class="pf-dialog" role="dialog" aria-modal="true" aria-labelledby="t-batal">
        <div class="pf-ico warn"><span>!</span></div>
        <h3 id="t-batal">Batal edit profil?</h3>
        <p>Perubahan yang belum disimpan akan hilang.</p>
        <div class="btns">
            <button type="button" class="no" data-close>Tidak</button>
            <button type="button" class="yes" id="yes-batal">Ya</button>
        </div>
    </div>
</div>

{{-- Dialog: berhasil --}}
<div class="pf-scrim {{ session('success') ? 'open' : '' }}" id="dlg-sukses" aria-hidden="{{ session('success') ? 'false' : 'true' }}">
    <div class="pf-dialog" role="dialog" aria-modal="true" aria-labelledby="t-sukses">
        <div class="pf-ico ok"><span>✓</span></div>
        <h3 id="t-sukses">Perubahan telah disimpan</h3>
        <p>Profil Anda berhasil diperbarui.</p>
        <div class="btns">
            <button type="button" class="yes" data-close>Selesai</button>
        </div>
    </div>
</div>

<script>
    (function() {
        var form = document.getElementById('pf-form');
        var cancelUrl = @json(url('/dashboard'));
        var dlgSimpan = document.getElementById('dlg-simpan');
        var dlgBatal = document.getElementById('dlg-batal');
        var dlgSukses = document.getElementById('dlg-sukses');
        var dlgPortfolioModal = document.getElementById('dlg-portfolio-modal');
        var bio = document.getElementById('bio');
        var count = document.getElementById('bio-count');
        var fotoInput = document.getElementById('foto');
        var avatar = document.getElementById('pf-avatar');
        var hint = document.getElementById('foto-hint');
        var hapusFoto = document.getElementById('hapus-foto');
        var initial = @json($inisial);
        var hintDefault = hint.textContent;
        var hasExistingFoto = @json((bool) $foto);
        var progressLabel = document.querySelector('.pf-prog .lab b');
        var progressTrack = document.querySelector('.pf-track');
        var progressBar = progressTrack.querySelector('i');

        function updateProfileProgress() {
            var filledFields = 0;
            ['name', 'npm', 'jurusan', 'prodi', 'email', 'angkatan', 'bio'].forEach(function(field) {
                if (form.elements[field].value.trim() !== '') {
                    filledFields++;
                }
            });

            var hasFoto = hapusFoto.value === '0'
                ? (hasExistingFoto || fotoInput.files.length > 0)
                : (fotoInput.files.length > 0);

            if (hasFoto) {
                filledFields++;
            }

            var percent = Math.round((filledFields / 8) * 100);
            progressLabel.textContent = percent + '%';
            progressTrack.setAttribute('aria-valuenow', percent);
            progressBar.style.width = percent + '%';
        }

        function snapshot() {
            var o = {};
            ['name', 'npm', 'jurusan', 'prodi', 'email', 'angkatan', 'bio'].forEach(function(n) {
                o[n] = form.elements[n].value;
            });
            o.foto = fotoInput.value;
            o.hapus = hapusFoto.value;
            return JSON.stringify(o);
        }
        var awal = snapshot();

        function open(el) {
            el.classList.add('open');
            el.setAttribute('aria-hidden', 'false');
            var b = el.querySelector('button.yes, button.no');
            if (b) b.focus();
        }

        function close(el) {
            el.classList.remove('open');
            el.setAttribute('aria-hidden', 'true');
        }

        // Hitung karakter bio
        function updateCount() {
            count.textContent = bio.value.length;
        }
        bio.addEventListener('input', function() {
            updateCount();
            updateProfileProgress();
        });
        updateCount();

        ['name', 'npm', 'jurusan', 'prodi', 'email', 'angkatan'].forEach(function(field) {
            form.elements[field].addEventListener('input', updateProfileProgress);
        });

        // Foto
        document.getElementById('btn-ubah-foto').addEventListener('click', function() {
            fotoInput.click();
        });
        fotoInput.addEventListener('change', function() {
            var f = fotoInput.files[0];
            hint.classList.remove('err');
            hint.textContent = hintDefault;
            if (!f) return;
            if (['image/jpeg', 'image/png'].indexOf(f.type) === -1) {
                hint.textContent = 'Format harus JPG atau PNG.';
                hint.classList.add('err');
                fotoInput.value = '';
                return;
            }
            if (f.size > 2 * 1024 * 1024) {
                hint.textContent = 'Ukuran file melebihi 2 MB.';
                hint.classList.add('err');
                fotoInput.value = '';
                return;
            }
            var img = document.createElement('img');
            img.alt = 'Foto profil';
            img.src = URL.createObjectURL(f);
            avatar.textContent = '';
            avatar.appendChild(img);
            hapusFoto.value = '0';
            updateProfileProgress();
        });
        document.getElementById('btn-hapus-foto').addEventListener('click', function() {
            fotoInput.value = '';
            hapusFoto.value = '1';
            avatar.textContent = initial;
            updateProfileProgress();
        });

        updateProfileProgress();

        // Simpan
        document.getElementById('btn-simpan').addEventListener('click', function() {
            if (!form.reportValidity()) return;
            open(dlgSimpan);
        });
        document.getElementById('yes-simpan').addEventListener('click', function() {
            form.submit();
        });

        // Batal
        document.getElementById('btn-batal').addEventListener('click', function() {
            if (snapshot() === awal) {
                window.location.href = cancelUrl;
            } else {
                open(dlgBatal);
            }
        });
        document.getElementById('yes-batal').addEventListener('click', function() {
            window.location.href = cancelUrl;
        });

        // Tutup dialog: tombol, klik overlay, Esc
        document.querySelectorAll('.pf-scrim').forEach(function(s) {
            s.addEventListener('click', function(e) {
                if (e.target === s || e.target.hasAttribute('data-close')) close(s);
            });
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                [dlgSimpan, dlgBatal, dlgSukses, dlgPortfolioModal].forEach(close);
            }
        });

        function openPortfolioModal(src, title) {
            document.getElementById('modal-portfolio-img').src = src;
            document.getElementById('modal-portfolio-title').textContent = title;
            open(dlgPortfolioModal);
        }

        function closePortfolioModal() {
            close(dlgPortfolioModal);
            document.getElementById('modal-portfolio-img').src = '';
        }

    })();
</script>
@endsection