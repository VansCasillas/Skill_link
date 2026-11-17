@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')
@section('content')
<div class="content">
    <div class="page-head">
        <div>
            <h1>Beranda</h1>
            <p>Selamat datang kembali, {{ $user['name'] }}. Ini ringkasan aktivitas Anda hari ini.</p>
        </div>
        <a class="btn primary" href="{{ url('/tim/buat') }}">+ Buat Tim Lomba</a>
    </div>

    <section class="banner">
        <div class="txt">
            <h2>Temukan tim lomba yang cocok untukmu</h2>
            <p>Gabung bersama mahasiswa TRPL lain, kembangkan skill, dan raih prestasi di lomba.</p>
        </div>
        <div class="actions">
            <a class="btn white" href="{{ url('/cari-tim') }}">Cari Tim</a>
            <a class="btn ghost" href="{{ url('/lomba') }}">Lihat Lomba</a>
        </div>
        <div class="stack" aria-hidden="true">
            <span class="avatar" style="background:#FF7F50"></span>
            <span class="avatar" style="background:#14B8A6"></span>
            <span class="avatar" style="background:#CCED11"></span>
            <span class="avatar" style="background:#0369A1"></span>
        </div>
    </section>

    <section class="stats" aria-label="Statistik">
        <div class="stat"><span class="k">Lomba Aktif</span><span class="v">{{ $stats['lomba_aktif'] ?? 0 }}</span>
            <span class="h" style="color:var(--blue)">{{ ($stats['lomba_aktif'] ?? 0) > 0 ? 'Sedang berjalan' : 'Tidak ada lomba' }}</span>
        </div>
        <div class="stat"><span class="k">Tim Saya</span><span class="v">{{ $stats['tim_saya'] ?? 0 }}</span>
            <span class="h" style="color:var(--blue)">sedang mencari anggota</span>
        </div>
        <div class="stat"><span class="k">Undangan Tim</span><span class="v">{{ $stats['undangan'] ?? 0 }}</span>
            <span class="h" style="color:var(--blue)">Menunggu respons</span>
        </div>
        <div class="stat"><span class="k">Kelengkapan Profil</span><span class="v">{{ $stats['profil'] ?? 0 }}%</span>
            <span class="h" style="color:var(--blue)">Lengkapi skill &amp; portofolio</span>
        </div>
    </section>

    <div class="cols">
        <div class="col">
            <section class="card">
                <div class="card-head">
                    <h3>Lomba Terbaru</h3><a href="{{ url('/lomba') }}">Lihat semua</a>
                </div>
                @forelse($lombas as $l)
                <div class="row">
                    <span class="ic {{ $loop->first ? 'ring' : '' }}" style="background:{{ $l['warna'] ?? '#0A87D9' }}">
                        <i data-lucide="trophy" class="i" aria-hidden="true"></i>
                    </span>
                    <div class="tx"><span class="nm">{{ $l['nama'] }}</span><span class="ds">{{ $l['desc'] ?? '' }}</span></div>
                    <a class="btn outline" href="{{ $l['url'] ?? '#' }}">Detail</a>
                </div>
                @empty
                <p class="empty">Belum ada lomba terbaru.</p>
                @endforelse
            </section>

            <section class="card">
                <div class="card-head">
                    <h3>Tim Mencari Anggota</h3><a href="{{ url('/cari-tim') }}">Lihat semua</a>
                </div>
                @forelse($tims as $t)
                <div class="row">
                    <span class="avatar av" style="background:{{ $t['warna'] ?? '#0A87D9' }}" aria-hidden="true"></span>
                    <div class="tx chips">
                        <span class="nm">{{ $t['nama'] }}</span>
                        <span class="chips-line">
                            <span class="chip">{{ $t['lomba'] }}</span>
                            <span class="chip need">Butuh {{ $t['butuh'] }} anggota</span>
                        </span>
                    </div>
                    <a class="btn primary sm" href="{{ $t['url'] ?? '#' }}">Gabung</a>
                </div>
                @empty
                <p class="empty">Belum ada tim yang mencari anggota.</p>
                @endforelse
            </section>
        </div>

        <aside class="col">
            <section class="card">
                <div class="profile">
                    <span class="avatar" aria-hidden="true">{{ $user['inisial'] }}</span>
                    <div>
                        <div class="nm">{{ $user['name'] }}</div>
                        <div class="sub">{{ $user['prodi'] ?? 'TRPL' }} · Angkatan {{ $user['angkatan'] ?? '' }}</div>
                    </div>
                </div>
                <div class="prog">
                    <div class="lab"><span>Kelengkapan profil</span><b>{{ $stats['profil'] ?? 0 }}%</b></div>
                    <div class="track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $stats['profil'] ?? 0 }}"><i style="width:{{ $stats['profil'] ?? 0 }}%"></i></div>
                </div>
                <a class="btn primary block" href="{{ url('/profil') }}">Lengkapi Profil</a>
            </section>

            <section class="card">
                <div class="card-head">
                    <h3>Deadline Terdekat</h3>
                </div>
                @forelse($deadlines as $d)
                <div class="dl">
                    <span class="date"><b>{{ $d['tgl'] }}</b><span>{{ $d['bln'] }}</span></span>
                    <span class="nm">{{ $d['nama'] }}</span>
                </div>
                @empty
                <p class="empty">Tidak ada deadline dekat.</p>
                @endforelse
            </section>

            <section class="card">
                <div class="card-head">
                    <h3>Notifikasi Terbaru</h3>
                </div>
                @forelse($notifs as $n)
                <div class="notif"><i></i><span>{{ $n }}</span></div>
                @empty
                <p class="empty">Belum ada notifikasi.</p>
                @endforelse
            </section>
        </aside>
    </div>
</div>
@endsection