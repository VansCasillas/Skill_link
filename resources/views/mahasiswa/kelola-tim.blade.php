@extends('layouts.app')

@section('title', 'Kelola Tim')

@section('content')
<div class="content">
    <div class="page-head">
        <div>
            <h1>Kelola Tim</h1>
            <p>Kelola tim lomba yang sedang Anda ikuti.</p>
        </div>
        <a class="btn primary" href="{{ route('recruitment.index') }}">Cari Tim</a>
    </div>

    <div class="cols" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr))">
        @foreach ($teams as $team)
            <section class="card">
                <div class="profile">
                    <span class="avatar" aria-hidden="true">{{ mb_substr($team['name'], 4, 1) }}</span>
                    <div>
                        <div class="nm">{{ $team['name'] }}</div>
                        <div class="sub">{{ $team['competition'] }}</div>
                    </div>
                </div>
                <div style="margin-top:16px">
                    <p><strong>Peran:</strong> {{ $team['role'] }}</p>
                    <p><strong>Anggota:</strong> {{ $team['members'] }} / {{ $team['capacity'] }}</p>
                </div>
                <div class="track" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $team['capacity'] }}" aria-valuenow="{{ $team['members'] }}">
                    <i style="width:{{ round(($team['members'] / $team['capacity']) * 100) }}%"></i>
                </div>
            </section>
        @endforeach
    </div>

    @if ($joinRequests->isNotEmpty())
        <section class="card" style="margin-top:24px">
            <div class="card-head">
                <h3>Permintaan Masuk Tim</h3>
            </div>
            @foreach ($joinRequests as $joinRequest)
                <div class="row" style="border-top:1px solid #E2E8F0;padding:14px 0">
                    <span class="avatar av" aria-hidden="true">{{ mb_strtoupper(mb_substr($joinRequest->user->name, 0, 1)) }}</span>
                    <div class="tx">
                        <span class="nm">{{ $joinRequest->user->name }}</span>
                        <span class="ds">Ingin bergabung ke {{ $joinRequest->team->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('teams.requests.approve', $joinRequest) }}" style="display:inline">
                        @csrf
                        <button class="btn primary sm" type="submit">Terima</button>
                    </form>
                    <form method="POST" action="{{ route('teams.requests.reject', $joinRequest) }}" style="display:inline">
                        @csrf
                        <button class="btn outline sm" type="submit">Tolak</button>
                    </form>
                </div>
            @endforeach
        </section>
    @else
        <section class="card" style="margin-top:24px">
            <h3>Permintaan Masuk Tim</h3>
            <p style="color:var(--muted);margin-bottom:0">Belum ada permintaan anggota yang menunggu persetujuan.</p>
        </section>
    @endif
</div>
@endsection
