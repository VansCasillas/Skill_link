@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="content">
    <div class="page-head">
        <div>
            <h1>Notifikasi</h1>
            <p>Pembaruan undangan, lomba, dan profil Anda.</p>
        </div>
    </div>

    <section class="card">
        @foreach ($notifications as $notification)
            <article class="row">
                <span class="ic" style="background:#0A87D9">
                    <i data-lucide="bell" class="i" aria-hidden="true"></i>
                </span>
                <div class="tx">
                    <span class="nm">{{ $notification['title'] }}</span>
                    <span class="ds">{{ $notification['description'] }}</span>
                </div>
                <span class="chip">{{ $notification['time'] }}</span>
            </article>
        @endforeach
    </section>
</div>
@endsection
