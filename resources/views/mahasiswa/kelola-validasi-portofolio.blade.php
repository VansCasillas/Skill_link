@extends('layouts.app')

@section('title', 'Validasi Portofolio')

@section('content')
<div class="content">
    <div class="page-head">
        <div>
            <h1>Validasi Portofolio</h1>
            <p>Periksa bukti karya sebelum ditampilkan pada profil mahasiswa.</p>
        </div>
    </div>

    @if (session('success'))
        <section class="card" style="margin-bottom:20px;color:var(--ok)">{{ session('success') }}</section>
    @endif

    <nav class="kil-filter-chips" style="margin-bottom:20px">
        @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $key => $label)
            <a class="kil-filter-chip {{ $status === $key ? 'is-active' : '' }}" href="{{ route('portfolio.validation.index', ['status' => $key]) }}">{{ $label }}</a>
        @endforeach
    </nav>

    @if ($portfolios->isNotEmpty())
        <div class="cols" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr))">
            @foreach ($portfolios as $portfolio)
                <article class="card">
                    <img src="{{ asset('storage/'.$portfolio->evidence_path) }}" alt="Bukti {{ $portfolio->title }}" style="width:100%;height:220px;object-fit:contain;border-radius:12px;background:#F1F5F9">
                    <div style="margin-top:14px">
                        <span class="chip">{{ $portfolio->type }}</span>
                        <h2 style="font-size:18px;margin:10px 0 4px">{{ $portfolio->title }}</h2>
                        <p style="margin:0;color:var(--muted)">oleh {{ $portfolio->user->name }}</p>
                        <p>{{ $portfolio->description }}</p>
                        @if ($portfolio->status === 'pending')
                            <div style="display:flex;gap:10px">
                                <form method="POST" action="{{ route('portfolio.validation.approve', $portfolio) }}">
                                    @csrf
                                    <button class="btn primary" type="submit">Setujui</button>
                                </form>
                                <form method="POST" action="{{ route('portfolio.validation.reject', $portfolio) }}">
                                    @csrf
                                    <button class="btn outline" type="submit">Tolak</button>
                                </form>
                            </div>
                        @else
                            <span class="chip">{{ ucfirst($portfolio->status) }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        {{ $portfolios->links() }}
    @else
        <section class="card">
            <h2>Tidak ada portofolio</h2>
            <p>Tidak ada pengajuan pada status ini.</p>
        </section>
    @endif
</div>
@endsection
