@extends('layouts.app')

@section('title', 'Kelola Tim')

@section('content')
<style>
    .manage-team-page {
        display: flex;
        flex-direction: column;
        gap: 18px;
        width: 100%;
        max-width: 1136px;
        margin: 0 auto;
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
    [data-team-panel="active"] {
        display: flex;
        flex-direction: column;
        gap: 12px;
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
    .manage-team-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .manage-team-edit {
        min-width: 86px;
        min-height: 32px;
        border: 1px solid #BFDBFE;
        border-radius: 8px;
        background: #EFF6FF;
        color: #2563EB;
        font-size: 11px;
        font-weight: 700;
    }
    .manage-team-dialog {
        width: min(520px, calc(100% - 28px));
        border: 0;
        border-radius: 16px;
        padding: 0;
        box-shadow: 0 20px 60px rgba(15, 23, 42, .25);
    }
    .manage-team-dialog::backdrop {
        background: rgba(15, 23, 42, .55);
    }
    .manage-team-dialog-inner {
        padding: 20px;
    }
    .manage-team-dialog-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    .manage-team-dialog-header h2,
    .manage-team-dialog-header p {
        margin: 0;
    }
    .manage-team-dialog-header h2 {
        font-size: 19px;
    }
    .manage-team-dialog-header p {
        margin-top: 4px;
        color: #64748B;
        font-size: 11px;
    }
    .manage-team-dialog-close {
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 8px;
        background: #F1F5F9;
        color: #0F172A;
        font-size: 18px;
    }
    .manage-team-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .manage-team-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .manage-team-field.full {
        grid-column: 1 / -1;
    }
    .manage-team-field label {
        color: #334155;
        font-size: 11px;
        font-weight: 700;
    }
    .manage-team-field input,
    .manage-team-field textarea {
        width: 100%;
        border: 1px solid #CBD5E1;
        border-radius: 9px;
        padding: 10px 12px;
        font: inherit;
        font-size: 12px;
    }
    .manage-team-field textarea {
        min-height: 82px;
        resize: vertical;
    }
    .manage-team-dialog-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 16px;
    }
    .manage-member-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #E2E8F0;
    }
    .manage-member-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: #334155;
        font-size: 12px;
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
                        @php
                            $colors = ['#F97316', '#22C55E', '#3B82F6', '#14B8A6', '#8B5CF6', '#EC4899'];
                            $stackInitials = !empty($team['initials']) ? $team['initials'] : ['A', 'C', 'D'];
                            $displayInitials = array_slice($stackInitials, 0, min(5, max(1, (int) $team['members'])));
                        @endphp
                        @foreach ($displayInitials as $index => $initial)
                            <span class="manage-team-avatar" style="background: {{ $colors[$index % count($colors)] }};">
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
                    <div class="manage-team-actions">
                        @if ($team['role'] === 'Ketua')
                            <button class="manage-team-edit" type="button"
                                    data-edit-team='@json($team)'>
                                Edit Tim
                            </button>
                        @endif
                        <a class="manage-team-detail" href="{{ route('teams.detail', $team['id']) }}">Lihat Detail</a>
                    </div>
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
        <h2>Permintaan Masuk Tim</h2>
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

    @foreach ($teams as $team)
        @if ($team['role'] === 'Ketua')
            <dialog class="manage-team-dialog" id="manage-team-dialog-{{ $team['id'] }}" aria-labelledby="manage-team-dialog-title-{{ $team['id'] }}">
                <div class="manage-team-dialog-inner">
                    <div class="manage-team-dialog-header">
                        <div>
                            <h2 id="manage-team-dialog-title-{{ $team['id'] }}">Edit Tim</h2>
                            <p>Ubah informasi tim atau keluarkan anggota.</p>
                        </div>
                        <button class="manage-team-dialog-close" type="button" data-close-team-dialog="{{ $team['id'] }}" aria-label="Tutup">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('teams.update', $team['id']) }}">
                        @csrf
                        @method('PUT')
                        <div class="manage-team-form-grid">
                            <div class="manage-team-field full">
                                <label for="team-name-{{ $team['id'] }}">Nama tim</label>
                                <input id="team-name-{{ $team['id'] }}" name="name" value="{{ $team['name'] }}" maxlength="200" required>
                            </div>
                            <div class="manage-team-field">
                                <label for="team-competition-{{ $team['id'] }}">Nama lomba</label>
                                <input id="team-competition-{{ $team['id'] }}" name="competition" value="{{ $team['competition'] }}" maxlength="200" required>
                            </div>
                            <div class="manage-team-field">
                                <label for="team-capacity-{{ $team['id'] }}">Kuota anggota</label>
                                <input id="team-capacity-{{ $team['id'] }}" name="capacity" type="number" min="2" max="20" value="{{ $team['capacity'] }}" required>
                            </div>
                            <div class="manage-team-field full">
                                <label for="team-description-{{ $team['id'] }}">Deskripsi</label>
                                <textarea id="team-description-{{ $team['id'] }}" name="description" maxlength="1000">{{ $team['description'] }}</textarea>
                            </div>
                            <div class="manage-team-field full">
                                <label for="team-skills-{{ $team['id'] }}">Skill (pisahkan dengan koma)</label>
                                <input id="team-skills-{{ $team['id'] }}" name="skills" value="{{ implode(', ', $team['skills']) }}" maxlength="500">
                            </div>
                        </div>

                        <div class="manage-team-dialog-actions">
                            <button class="btn outline sm" type="button" data-close-team-dialog="{{ $team['id'] }}">Batal</button>
                            <button class="btn primary sm" type="submit">Simpan Perubahan</button>
                        </div>
                    </form>

                    <div class="manage-member-list">
                        <strong style="font-size:12px;color:#334155">Anggota tim</strong>
                        @forelse ($team['approved_members'] as $member)
                            <div class="manage-member-row">
                                <span>{{ $member['name'] }}</span>
                                <form method="POST" action="{{ route('teams.members.remove', [$team['id'], $member['id']]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn outline sm" type="submit"
                                            onclick="return confirm('Keluarkan {{ e($member['name']) }} dari tim ini?')">
                                        Keluarkan
                                    </button>
                                </form>
                            </div>
                        @empty
                            <span style="color:#64748B;font-size:11px">Belum ada anggota yang disetujui.</span>
                        @endforelse
                    </div>
                </div>
            </dialog>
        @endif
    @endforeach
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

    document.querySelectorAll('[data-edit-team]').forEach(function (button) {
        button.addEventListener('click', function () {
            var team = JSON.parse(button.getAttribute('data-edit-team'));
            var dialog = document.getElementById('manage-team-dialog-' + team.id);
            if (dialog) {
                dialog.showModal();
            }
        });
    });

    document.querySelectorAll('[data-close-team-dialog]').forEach(function (button) {
        button.addEventListener('click', function () {
            var dialog = document.getElementById('manage-team-dialog-' + button.getAttribute('data-close-team-dialog'));
            if (dialog) {
                dialog.close();
            }
        });
    });

    document.querySelectorAll('.manage-team-dialog').forEach(function (dialog) {
        dialog.addEventListener('click', function (event) {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    });
</script>
@endsection
