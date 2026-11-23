@extends('layouts.app')

@section('title', 'Pesan')

@section('content')
<div class="content">
    <div class="page-head">
        <div>
            <h1>Pesan</h1>
            <p>Komunikasi dengan tim dan anggota Anda.</p>
        </div>
    </div>

    @if (session('success'))
        <section class="card" style="margin-bottom:16px;color:var(--ok)">
            {{ session('success') }}
        </section>
    @endif

    @if ($teams->isNotEmpty())
        <section class="card" style="display:grid;grid-template-columns:220px 1fr;gap:20px;min-height:480px">
            <aside style="border-right:1px solid #E2E8F0;padding-right:20px">
                <h2 style="font-size:16px;margin:0 0 12px">Tim Saya</h2>
                @foreach ($teams as $team)
                    <a href="{{ route('messages.index', ['team' => $team->id]) }}"
                       class="row"
                       style="padding:10px;border-radius:10px;margin-bottom:6px;{{ $selectedTeam && $selectedTeam->id === $team->id ? 'background:#E0F2FE;' : '' }}">
                        <span class="avatar av" aria-hidden="true">{{ mb_strtoupper(mb_substr($team->name, 0, 1)) }}</span>
                        <div class="tx">
                            <span class="nm">{{ $team->name }}</span>
                            <span class="ds">{{ $team->competition }}</span>
                        </div>
                    </a>
                @endforeach
            </aside>

            <div style="display:flex;flex-direction:column">
                <div style="border-bottom:1px solid #E2E8F0;padding-bottom:12px">
                    <h2 style="font-size:18px;margin:0">{{ $selectedTeam->name }}</h2>
                    <p style="margin:4px 0 0;color:var(--muted);font-size:12px">{{ $selectedTeam->competition }}</p>
                </div>

                <div style="flex:1;overflow-y:auto;padding:18px 4px;max-height:340px">
                    @forelse ($chatMessages as $message)
                        <article style="margin-bottom:12px;display:flex;flex-direction:column;align-items:{{ $message->user_id === auth()->id() ? 'flex-end' : 'flex-start' }}">
                            <span style="font-size:11px;color:var(--muted);margin-bottom:4px">{{ $message->user->name }}</span>
                            <div style="max-width:75%;padding:10px 12px;border-radius:12px;background:{{ $message->user_id === auth()->id() ? '#0A87D9' : '#EAF2FB' }};color:{{ $message->user_id === auth()->id() ? '#fff' : '#0F172A' }}">
                                {{ $message->body }}
                            </div>
                        </article>
                    @empty
                        <p style="text-align:center;color:var(--muted)">Belum ada pesan. Mulai percakapan dengan tim Anda.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('teams.messages.store', $selectedTeam) }}" style="display:flex;gap:10px;padding-top:12px;border-top:1px solid #E2E8F0">
                    @csrf
                    <input type="text" name="body" required maxlength="2000" placeholder="Tulis pesan untuk tim..."
                           style="flex:1;height:42px;border:1px solid #CBD5E1;border-radius:10px;padding:0 12px;font-family:inherit">
                    <button class="btn primary" type="submit">Kirim</button>
                </form>
            </div>
        </section>
    @else
        <section class="card">
            <h2>Belum ada tim untuk dikirim pesan</h2>
            <p>Buat atau bergabung dengan tim terlebih dahulu melalui menu Pencarian atau Kelola Tim.</p>
            <a class="btn primary" href="{{ route('recruitment.index') }}">Cari Tim</a>
        </section>
    @endif
</div>
@endsection
