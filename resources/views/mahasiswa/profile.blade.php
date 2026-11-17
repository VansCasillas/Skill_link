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
    }
</style>

@php
    $nama     = old('name',     $user['name']     ?? '');
    $inisial  = strtoupper(mb_substr($nama ?: 'U', 0, 1));
    $persen   = $stats['profil'] ?? 0;
    $foto     = $user['foto'] ?? null;
@endphp

<div class="pf">
    <h1>Edit Profil</h1>

    <form id="pf-form" method="POST" action="{{ url('/profil') }}" enctype="multipart/form-data" novalidate>
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
                            <img src="{{ asset($foto) }}" alt="Foto profil">
                        @else
                            {{ $inisial }}
                        @endif
                    </div>
                    <div class="pf-name">{{ $user['name'] ?? '' }}</div>
                    <div class="pf-sub">{{ $user['prodi'] ?? 'TRPL' }} · Angkatan {{ $user['angkatan'] ?? '' }}</div>

                    <div class="pf-photo-actions">
                        <button type="button" class="pf-btn primary block" id="btn-ubah-foto">Ubah Foto</button>
                        <button type="button" class="pf-btn gray block" id="btn-hapus-foto">Hapus Foto</button>
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
            <section class="pf-card pf-form">
                <div>
                    <h2>Data Diri</h2>
                    <p class="desc">Isi data sesuai identitas mahasiswa. Tanda * wajib diisi.</p>
                </div>

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

                <div class="pf-field">
                    <label for="bio">Bio Singkat<i>*</i></label>
                    <textarea class="pf-input @error('bio') invalid @enderror" id="bio" name="bio" required maxlength="300"
                        placeholder="Maksimal 300 karakter">{{ old('bio', $user['bio'] ?? '') }}</textarea>
                    <div class="pf-count"><span id="bio-count">0</span> / 300 karakter</div>
                    @error('bio')<span class="pf-error">{{ $message }}</span>@enderror
                </div>

                <hr class="pf-line">

                <div class="pf-actions">
                    <button type="button" class="pf-btn outline" id="btn-batal">Batal</button>
                    <button type="button" class="pf-btn primary" id="btn-simpan">Simpan Perubahan</button>
                </div>
            </section>
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
        var bio = document.getElementById('bio');
        var count = document.getElementById('bio-count');
        var fotoInput = document.getElementById('foto');
        var avatar = document.getElementById('pf-avatar');
        var hint = document.getElementById('foto-hint');
        var hapusFoto = document.getElementById('hapus-foto');
        var initial = @json($inisial);
        var hintDefault = hint.textContent;

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
        bio.addEventListener('input', updateCount);
        updateCount();

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
        });
        document.getElementById('btn-hapus-foto').addEventListener('click', function() {
            fotoInput.value = '';
            hapusFoto.value = '1';
            avatar.textContent = initial;
        });

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
                [dlgSimpan, dlgBatal, dlgSukses].forEach(close);
            }
        });
    })();
</script>
@endsection