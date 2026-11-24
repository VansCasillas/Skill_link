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
        @if (session('success'))
            <p style="color:var(--ok)">{{ session('success') }}</p>
        @endif

        @if ($oldNotificationCount > 0)
            <form method="POST" action="{{ route('notifications.clear-old') }}" style="margin-bottom:16px">
                @csrf
                @method('DELETE')
                <button class="btn outline" type="submit">
                    Bersihkan {{ $oldNotificationCount }} notifikasi yang lebih dari 30 hari
                </button>
            </form>
        @endif

        @forelse ($notifications as $notification)
            <a class="row" href="{{ $notification['url'] }}">
                <span class="ic" style="background:#0A87D9">
                    <i data-lucide="bell" class="i" aria-hidden="true"></i>
                </span>
                <div class="tx">
                    <span class="nm">{{ $notification['title'] }}</span>
                    <span class="ds">{{ $notification['description'] }}</span>
                </div>
                <span class="chip">{{ $notification['time'] }}</span>
            </a>
        @empty
            <p class="empty">Belum ada notifikasi.</p>
        @endforelse
    </section>
</div>
@endsection
