@extends('layouts.app')

@section('title', 'Portofolio Publik')

@section('content')
<style>
    .portfolio-page {
        max-width: 1120px;
        margin: 0 auto;
        color: #0F172A;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
    }

    .portfolio-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
    }

    .portfolio-header h1 {
        margin: 0;
        font-size: 28px;
        line-height: 36px;
    }

    .portfolio-header p {
        margin: 6px 0 0;
        color: #64748B;
        font-size: 14px;
    }

    .portfolio-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 16px;
        border: 1px solid #0A87D9;
        border-radius: 10px;
        color: #0A87D9;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .portfolio-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 18px;
    }

    .portfolio-card {
        overflow: hidden;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 4px 16px rgba(10, 51, 102, .08);
    }

    .portfolio-card img {
        display: block;
        width: 100%;
        height: 190px;
        object-fit: cover;
        background: #F1F5F9;
    }

    .portfolio-card-body {
        padding: 18px;
    }

    .portfolio-card h2 {
        margin: 0;
        font-size: 16px;
        line-height: 22px;
    }

    .portfolio-type {
        display: inline-block;
        margin-top: 8px;
        padding: 4px 9px;
        border-radius: 999px;
        background: #EFF6FF;
        color: #1D4ED8;
        font-size: 11px;
        font-weight: 600;
    }

    .portfolio-description {
        margin: 12px 0;
        color: #475569;
        font-size: 13px;
        line-height: 20px;
        white-space: pre-line;
    }

    .portfolio-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .portfolio-tag {
        padding: 4px 8px;
        border-radius: 999px;
        background: #F1F5F9;
        color: #475569;
        font-size: 11px;
    }

    .portfolio-empty {
        padding: 48px 24px;
        border-radius: 16px;
        background: #fff;
        color: #64748B;
        text-align: center;
        box-shadow: 0 4px 16px rgba(10, 51, 102, .08);
    }

    @media (max-width: 640px) {
        .portfolio-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .portfolio-header h1 {
            font-size: 24px;
        }
    }
</style>

<div class="portfolio-page">
    <header class="portfolio-header">
        <div>
            <h1>Portofolio {{ $user['name'] }}</h1>
            <p>Daftar karya yang telah divalidasi dan ditampilkan pada profil Anda.</p>
        </div>
        <a class="portfolio-back" href="{{ route('portofolio.index') }}">Kelola Portofolio</a>
    </header>

    @if (count($portfolios) > 0)
        <div class="portfolio-grid">
            @foreach ($portfolios as $portfolio)
                <article class="portfolio-card">
                    <img src="{{ $portfolio['bukti_url'] }}" alt="Bukti {{ $portfolio['judul'] }}">
                    <div class="portfolio-card-body">
                        <h2>{{ $portfolio['judul'] }}</h2>
                        <span class="portfolio-type">{{ $portfolio['tipe'] }}</span>
                        @if ($portfolio['deskripsi'] !== '')
                            <p class="portfolio-description">{{ $portfolio['deskripsi'] }}</p>
                        @endif
                        @if (count($portfolio['tags']) > 0)
                            <div class="portfolio-tags">
                                @foreach ($portfolio['tags'] as $tag)
                                    <span class="portfolio-tag">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="portfolio-empty">
            <p>Belum ada portofolio yang dipublikasikan.</p>
            <a href="{{ route('portofolio.index') }}">Atur portofolio Anda</a>
        </div>
    @endif
</div>
@endsection
