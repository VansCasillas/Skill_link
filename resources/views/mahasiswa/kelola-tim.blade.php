@extends('layouts.app')

@section('title', 'Kelola Tim')

@section('content')
<style>
    .manage-team-page {
        display: flex;
        flex-direction: column;
        gap: 18px;
        max-width: 1136px;
    }
    .manage-team-heading h1 {
        margin: 0;
        font-size: 26px;
        line-height: 33px;
        color: #0F172A;
    }
    .manage-team-heading p {
        margin: 4px 0 0;
        font-size: 12px;
        line-height: 15px;
        color: #64748B;
    }
    .manage-team-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
    }
    .manage-team-stat {
        min-height: 100px;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(10, 51, 102, .06);
    }
    .manage-team-stat-number {
        width: 40px;
        height: 40px;
        display: grid;
        place-items: center;
        border-radius: 8px;
        background: #2563EB;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
    }
    .manage-team-stat.pending .manage-team-stat-number {
        background: #F59E0B;
    }
    .manage-team-stat-copy {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .manage-team-stat-copy span {
        color: #64748B;
        font-size: 10px;
    }
    .manage-team-stat-copy strong {
        color: #0F172A;
        font-size: 13px;
    }
    .manage-team-panel {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .manage-team-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .manage-team-tab {
        min-height: 32px;
        padding: 8px 18px;
        border: 1px solid #D5DFEF;
        border-radius: 20px;
        background: #fff;
        color: #334155;
        font-size: 11px;
        font-weight: 700;
    }
    .manage-team-tab.active {
        border-color: #2563EB;
        background: #2563EB;
        color: #fff;
    }
    .manage-team-card {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto auto;
        align-items: center;
        gap: 12px;
        min-height: 142px;
        padding: 16px 20px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(10, 51, 102, .06);
    }
    .manage-team-stack {
        display: flex;
        width: 62px;
    }
    .manage-team-avatar {
        width: 26px;
        height: 26px;
        margin-right: -8px;
        display: grid;
        place-items: center;
        border: 2px solid #fff;
        border-radius: 50%;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
    }
    .manage-team-info {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 1px;
    }
    .manage-team-info strong {
        color: #0F172A;
        font-size: 13px;
    }
    .manage-team-info span {
        color: #64748B;
        font-size: 11px;
    }
    .manage-team-status {
        padding: 4px 10px;
        border-radius: 20px;
        background: #DCFCE7;
        color: #15803D;
        font-size: 10px;
        font-weight: 700;
    }
    .manage-team-status.closed {
        background: #F1F5F9;
        color: #64748B;
    }
    .manage-team-detail {
        min-width: 100px;
        min-height: 32px;
        display: inline-grid;
        place-items: center;
        border-radius: 8px;
        background: #2563EB;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
    }
    .manage-team-empty {
        padding: 24px;
        background: #fff;
        border-radius: 14px;
        color: #64748B;
        text-align: center;
    }
    .manage-team-requests {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .manage-team-request {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(10, 51, 102, .06);
    }
    .manage-team-request-actions {
        display: flex;
        gap: 8px;
    }
    @media (max-width: 760px) {
        .manage-team-stats {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .manage-team-card {
            grid-template-columns: auto minmax(0, 1fr);
        }
        .manage-team-status,
        .manage-team-detail {
            grid-column: 2;
            justify-self: start;
        }
        .manage-team-request {
            grid-template-columns: auto minmax(0, 1fr);
        }
        .manage-team-request-actions {
            grid-column: 1 / -1;
        }
    }
</style>

<div class="manage-team-page">
    <div class="manage-team-heading">
        <div>
            <h1>Kelola Tim</h1>
            <p>Kelola anggota, permintaan bergabung, dan status rekrutmen tim yang Anda pimpin.</p>
        </div>
    </div>

    <div class="manage-team-stats">
        <section class="manage-team-stat">
            <span class="manage-team-stat-number">{{ count($teams) }}</span>
            <div class="manage-team-stat-copy">
                <span>Tim Saya</span>
                <strong>{{ count($teams) }} tim aktif</strong>
            </div>
        </section>
        <section class="manage-team-stat pending">
            <span class="manage-team-stat-number">{{ $joinRequests->count() }}</span>
            <div class="manage-team-stat-copy">
                <span>Permintaan Gabung Tim</span>
                <strong>{{ $joinRequests->count() }} menunggu</strong>
            </div>
        </section>
    </div>

    <div class="manage-team-panel">
        <div class="manage-team-tabs" role="tablist" aria-label="Daftar tim">
            <button class="manage-team-tab active" type="button" data-team-tab="active" role="tab">Tim Saya</button>
            <button class="manage-team-tab" type="button" data-team-tab="history" role="tab">Riwayat tim</button>
        </div>

        <div data-team-panel="active">
            @forelse ($teams as $team)
                <article class="manage-team-card">
                    <div class="manage-team-stack" aria-label="{{ $team['members'] }} anggota">
                        @foreach (array_slice(['A', 'C', 'D', 'T'], 0, min(4, $team['members'])) as $index => $initial)
                            <span class="manage-team-avatar" style="background: {{ ['#F97316', '#22C55E', '#3B82F6', '#14B8A6'][$index] }};">
                                {{ $initial }}
                            </span>
                        @endforeach
                    </div>
                    <div class="manage-team-info">
                        <strong>{{ $team['name'] }}</strong>
                        <span>{{ $team['competition'] ?: 'Kompetisi belum ditentukan' }} · {{ $team['role'] }} · {{ $team['members'] }}/{{ $team['capacity'] }} anggota</span>
                    </div>
                    <span class="manage-team-status {{ $team['members'] >= $team['capacity'] ? 'closed' : '' }}">
                        {{ $team['members'] >= $team['capacity'] ? 'Penuh' : $team['status'] }}
                    </span>
                    <a class="manage-team-detail" href="{{ route('messages.index', ['team' => $team['id']]) }}">Lihat Detail</a>
                </article>
            @empty
                <div class="manage-team-empty">Belum ada tim yang dapat ditampilkan.</div>
            @endforelse
        </div>

        <div data-team-panel="history" style="display:none">
            <div class="manage-team-empty">Belum ada riwayat tim.</div>
        </div>
    </div>

    <section class="manage-team-requests">
        @forelse ($joinRequests as $joinRequest)
            <div class="manage-team-request">
                <span class="manage-team-avatar" style="background:#2563EB; margin:0;">
                    {{ mb_strtoupper(mb_substr($joinRequest->user->name, 0, 1)) }}
                </span>
                <div class="manage-team-info">
                    <strong>{{ $joinRequest->user->name }}</strong>
                    <span>Ingin bergabung ke {{ $joinRequest->team->name }}</span>
                </div>
                <div class="manage-team-request-actions">
                    <form method="POST" action="{{ route('teams.requests.approve', $joinRequest) }}">
                        @csrf
                        <button class="btn primary sm" type="submit">Terima</button>
                    </form>
                    <form method="POST" action="{{ route('teams.requests.reject', $joinRequest) }}">
                        @csrf
                        <button class="btn outline sm" type="submit">Tolak</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="manage-team-empty">Belum ada permintaan anggota yang menunggu persetujuan.</div>
        @endforelse
    </section>
</div>

<script>
    document.querySelectorAll('[data-team-tab]').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('[data-team-tab]').forEach(function (item) {
                item.classList.remove('active');
            });
            tab.classList.add('active');
            document.querySelectorAll('[data-team-panel]').forEach(function (panel) {
                panel.style.display = panel.getAttribute('data-team-panel') === tab.getAttribute('data-team-tab')
                    ? ''
                    : 'none';
            });
        });
    });
</script>
@endsection
