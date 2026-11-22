@extends('layouts.app')

@section('title', 'Pusat Informasi Lomba')

@section('content')
<div class="content">
    <div class="page-head">
        <div>
            <h1>Pusat Informasi Lomba</h1>
            <p>Temukan kompetisi aktif yang sesuai dengan minat dan bidang Anda.</p>
        </div>
    </div>

    @if ($competitions->isNotEmpty())
        <div class="cols" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr))">
            @foreach ($competitions as $competition)
                <article class="card" style="display:flex;flex-direction:column;gap:12px">
                    @if ($competition->poster_path)
                        <img src="{{ asset('storage/'.$competition->poster_path) }}" alt="Poster {{ $competition->name }}" style="width:100%;height:180px;object-fit:cover;border-radius:12px">
                    @endif
                    <div>
                        <span class="chip">{{ $competition->category }}</span>
                        <span class="chip">{{ $competition->level }}</span>
                    </div>
                    <h2 style="margin:0;font-size:20px">{{ $competition->name }}</h2>
                    <p style="margin:0;color:var(--muted)">{{ $competition->organizer }}</p>
                    <p style="margin:0">{{ \Illuminate\Support\Str::limit($competition->description, 180) }}</p>
                    <div style="margin-top:auto">
                        <strong>Deadline:</strong> {{ $competition->deadline->format('d M Y') }}
                        <div style="margin-top:12px">
                            <a class="btn primary" href="{{ $competition->registration_url }}" target="_blank" rel="noopener noreferrer">Daftar</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($competitions->lastPage() > 1)
            <div style="margin-top:24px">{{ $competitions->links() }}</div>
        @endif
    @else
        <section class="card">
            <h2>Tidak ada lomba ditemukan</h2>
            <p>Belum ada lomba aktif atau hasil pencarian tidak cocok.</p>
            @if ($search !== '')
                <a class="btn outline" href="{{ route('competition.index') }}">Hapus pencarian</a>
            @endif
        </section>
    @endif
</div>
@endsection
