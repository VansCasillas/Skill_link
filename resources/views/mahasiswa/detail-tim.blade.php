@extends('layouts.app')

@section('title', 'Detail Tim')

@section('content')
<style>
.team-detail-page { max-width: 1136px; display:flex; flex-direction:column; gap:18px; }
.team-detail-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; }
.team-detail-head h1 { margin:0; font-size:26px; }
.team-detail-head p { margin:4px 0 0; color:#64748B; font-size:12px; }
.team-detail-card { background:#fff; border-radius:14px; padding:20px; box-shadow:0 2px 10px rgba(10,51,102,.06); }
.team-detail-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px 24px; }
.team-detail-field span { display:block; color:#64748B; font-size:10px; }
.team-detail-field strong { display:block; margin-top:3px; color:#0F172A; font-size:13px; }
.team-detail-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:16px; }
.team-member-section { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.team-member-section h2 { margin:0 0 12px; font-size:16px; }
.team-member-list { display:flex; flex-direction:column; gap:8px; }
.team-member-row { display:grid; grid-template-columns:36px minmax(0,1fr) auto; align-items:center; gap:10px; padding:12px; border:1px solid #E2E8F0; border-radius:10px; }
.team-member-avatar { width:36px;height:36px;display:grid;place-items:center;border-radius:50%;background:#2563EB;color:#fff;font-size:12px;font-weight:700; }
.team-member-info strong,.team-member-info span { display:block; }
.team-member-info strong { font-size:12px; }
.team-member-info span { margin-top:2px;color:#64748B;font-size:10px; }
.team-request-actions { display:flex; gap:6px; }
.team-edit-dialog { width:min(520px,calc(100% - 28px)); border:0;border-radius:16px;padding:0; }
.team-edit-dialog::backdrop { background:rgba(15,23,42,.55); }
.team-edit-inner { padding:20px; }
.team-edit-head { display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px; }
.team-edit-head h2 { margin:0;font-size:19px; }
.team-edit-grid { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.team-edit-field { display:flex;flex-direction:column;gap:6px; }
.team-edit-field.full { grid-column:1/-1; }
.team-edit-field label { color:#334155;font-size:11px;font-weight:700; }
.team-edit-field input,.team-edit-field textarea { width:100%;border:1px solid #CBD5E1;border-radius:9px;padding:10px 12px;font:inherit;font-size:12px; }
.team-edit-field textarea { min-height:82px;resize:vertical; }
@media(max-width:760px){ .team-detail-grid,.team-member-section,.team-edit-grid{grid-template-columns:1fr}.team-detail-head{flex-direction:column}.team-edit-field.full{grid-column:auto}.team-member-row{grid-template-columns:36px minmax(0,1fr)}.team-request-actions{grid-column:1/-1}}
</style>

<div class="team-detail-page">
    <div class="team-detail-head">
        <div>
            <h1>{{ $team->name }}</h1>
            <p>Kelola informasi, anggota, dan permintaan bergabung tim.</p>
        </div>
        <a class="btn outline sm" href="{{ route('tim.index') }}">Kembali</a>
    </div>

    <section class="team-detail-card">
        <div class="team-detail-grid">
            <div class="team-detail-field"><span>Nama lomba</span><strong>{{ $team->competition ?: '-' }}</strong></div>
            <div class="team-detail-field"><span>Jumlah slot</span><strong>{{ $team->members_count }} / {{ $team->capacity }} anggota</strong></div>
            <div class="team-detail-field"><span>Status</span><strong>{{ $team->members_count >= $team->capacity ? 'Penuh' : 'Dibuka' }}</strong></div>
            <div class="team-detail-field"><span>Skill</span><strong>{{ collect($team->skills ?? [])->join(', ') ?: '-' }}</strong></div>
        </div>
        @if ($team->description)
            <p style="margin:14px 0 0;color:#475569;font-size:12px">{{ $team->description }}</p>
        @endif
        <div class="team-detail-actions">
            <button class="btn primary sm" type="button" id="open-team-edit">Edit Tim</button>
        </div>
    </section>

    <div class="team-member-section">
        <section class="team-detail-card">
            <h2>Anggota Tim ({{ $approvedMembers->count() + 1 }})</h2>
            <div class="team-member-list">
                <div class="team-member-row">
                    <span class="team-member-avatar">{{ mb_strtoupper(mb_substr($team->owner->name, 0, 1)) }}</span>
                    <div class="team-member-info">
                        <strong>{{ $team->owner->name }}</strong>
                        <span>Ketua tim</span>
                    </div>
                </div>
                @forelse ($approvedMembers as $joinRequest)
                    <div class="team-member-row">
                        <span class="team-member-avatar">{{ mb_strtoupper(mb_substr($joinRequest->user->name, 0, 1)) }}</span>
                        <div class="team-member-info">
                            <strong>{{ $joinRequest->user->name }}</strong>
                            <span>Anggota tim</span>
                        </div>
                        <form method="POST" action="{{ route('teams.members.remove', [$team, $joinRequest]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn outline sm" type="submit" onclick="return confirm('Keluarkan anggota ini dari tim?')">Keluarkan</button>
                        </form>
                    </div>
                @empty
                    <p style="color:#64748B;font-size:12px">Belum ada anggota tambahan.</p>
                @endforelse
            </div>
        </section>

        <section class="team-detail-card">
            <h2>Permintaan Bergabung ({{ $pendingMembers->count() }})</h2>
            <div class="team-member-list">
                @forelse ($pendingMembers as $joinRequest)
                    <div class="team-member-row">
                        <span class="team-member-avatar">{{ mb_strtoupper(mb_substr($joinRequest->user->name, 0, 1)) }}</span>
                        <div class="team-member-info">
                            <strong>{{ $joinRequest->user->name }}</strong>
                            <span>Menunggu persetujuan</span>
                        </div>
                        <div class="team-request-actions">
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
                    <p style="color:#64748B;font-size:12px">Tidak ada permintaan menunggu.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>

<dialog class="team-edit-dialog" id="team-edit-dialog" aria-labelledby="team-edit-title">
    <div class="team-edit-inner">
        <div class="team-edit-head">
            <h2 id="team-edit-title">Edit Tim</h2>
            <button type="button" class="manage-team-dialog-close" id="close-team-edit" aria-label="Tutup">&times;</button>
        </div>
        <form method="POST" action="{{ route('teams.update', $team) }}">
            @csrf
            @method('PUT')
            <div class="team-edit-grid">
                <div class="team-edit-field full"><label for="team-name">Nama tim</label><input id="team-name" name="name" value="{{ $team->name }}" required maxlength="200"></div>
                <div class="team-edit-field"><label for="team-competition">Nama lomba</label><input id="team-competition" name="competition" value="{{ $team->competition }}" required maxlength="200"></div>
                <div class="team-edit-field"><label for="team-capacity">Jumlah slot</label><input id="team-capacity" name="capacity" type="number" min="{{ $team->members_count }}" max="20" value="{{ $team->capacity }}" required></div>
                <div class="team-edit-field full"><label for="team-description">Deskripsi</label><textarea id="team-description" name="description" maxlength="1000">{{ $team->description }}</textarea></div>
                <div class="team-edit-field full"><label for="team-skills">Skill (pisahkan dengan koma)</label><input id="team-skills" name="skills" value="{{ implode(', ', $team->skills ?? []) }}" maxlength="500"></div>
            </div>
            <div class="team-detail-actions">
                <button type="button" class="btn outline sm" id="cancel-team-edit">Batal</button>
                <button type="submit" class="btn primary sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</dialog>

<script>
(function () {
    var dialog = document.getElementById('team-edit-dialog');
    document.getElementById('open-team-edit').addEventListener('click', function () { dialog.showModal(); });
    document.getElementById('close-team-edit').addEventListener('click', function () { dialog.close(); });
    document.getElementById('cancel-team-edit').addEventListener('click', function () { dialog.close(); });
    dialog.addEventListener('click', function (event) { if (event.target === dialog) dialog.close(); });
})();
</script>
@endsection
