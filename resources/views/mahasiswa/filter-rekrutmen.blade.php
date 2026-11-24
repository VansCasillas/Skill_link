@extends('layouts.app')

@section('content')

{{-- ── Page-level styles ─────────────────────────────────────────────────────── --}}
<style>
    /* ===== Layout (main + sidebar) ===== */
    .fr-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 328px;
        gap: 24px;
        align-items: start;
    }

    /* ===== Main section ===== */
    .fr-main {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .fr-heading h2 {
        margin: 0 0 4px;
        font-family: 'Roboto', sans-serif;
        font-weight: 700;
        font-size: 20px;
        line-height: 23px;
        color: #111827;
    }

    .fr-heading p {
        margin: 0;
        font-family: 'Roboto', sans-serif;
        font-weight: 400;
        font-size: 13px;
        line-height: 15px;
        color: #64748B;
    }

    .fr-count {
        font-family: 'Roboto', sans-serif;
        font-weight: 700;
        font-size: 13px;
        line-height: 15px;
        color: #111827;
        margin: 2px 0 6px;
    }

    /* ===== Team article card ===== */
    .team-article {
        background: #FFFFFF;
        border: 1px solid #E1E7EF;
        border-radius: 13px;
        padding: 25px 16px 15px;
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    /* row 1: avatar stack + name + status badge */
    .ta-top {
        display: flex;
        align-items: center;
        gap: 13px;
    }

    /* avatar stack */
    .av-stack {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .av-stack .av {
        width: 31px;
        height: 31px;
        border-radius: 50%;
        border: 1px solid #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 500;
        font-size: 13px;
        color: #FFFFFF;
        flex-shrink: 0;
    }

    .av-stack .av:not(:first-child) {
        margin-left: -6px;
    }

    /* team name + category */
    .ta-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .ta-info .ta-name {
        font-family: 'Roboto', sans-serif;
        font-weight: 700;
        font-size: 14px;
        line-height: 16px;
        color: #111827;
    }

    .ta-info .ta-cat {
        font-family: 'Roboto', sans-serif;
        font-weight: 400;
        font-size: 12px;
        line-height: 14px;
        color: #64748B;
    }

    /* status badge pushed to the right */
    .ta-badge-wrap {
        margin-left: auto;
    }

    .ta-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-family: 'Roboto', sans-serif;
        font-weight: 600;
        font-size: 11px;
        line-height: 13px;
    }

    .ta-badge.open   { background: #DCFCE7; color: #059669; }
    .ta-badge.full   { background: #F1F5F9; color: #64748B; }
    .ta-badge.almost { background: #FEF9C3; color: #A16207; }

    /* row 2: skill chips */
    .ta-skills {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .ta-chip {
        display: inline-flex;
        align-items: center;
        padding: 6px 13px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 18px;
        font-family: 'Roboto', sans-serif;
        font-weight: 400;
        font-size: 12px;
        line-height: 14px;
        color: #526477;
        white-space: nowrap;
    }

    /* row 3: progress bar + slot text + button */
    .ta-footer {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .ta-bar-wrap {
        flex: 1;
        height: 6px;
        background: #E2E8F0;
        border-radius: 5px;
        overflow: hidden;
    }

    .ta-bar-fill {
        height: 100%;
        border-radius: 5px;
    }

    .ta-bar-fill.active   { background: #2563EB; }
    .ta-bar-fill.full-bar { background: #94A3B8; }

    .ta-slot {
        font-family: 'Roboto', sans-serif;
        font-weight: 400;
        font-size: 12px;
        line-height: 14px;
        color: #64748B;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .ta-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 16px;
        border-radius: 8px;
        font-family: 'Roboto', sans-serif;
        font-weight: 600;
        font-size: 14px;
        line-height: 16px;
        border: none;
        cursor: pointer;
        white-space: nowrap;
        flex-shrink: 0;
        text-decoration: none;
        transition: background .15s;
    }

    .ta-btn.join  { background: #2563EB; color: #FFFFFF; }
    .ta-btn.join:hover { background: #1D4ED8; }
    .ta-btn.penuh { background: #FFFFFF; border: 1px solid #E2E8F0; color: #94A3B8; cursor: default; }

    /* ===== Filter sidebar ===== */
    .fr-aside {
        background: #FFFFFF;
        border: 1px solid #E1E7EF;
        border-radius: 17px;
        position: sticky;
        top: calc(var(--top-h) + 16px);
        overflow: hidden;
    }

    .fr-aside-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 22px 18px;
        border-bottom: 1px solid #E2E8F0;
    }

    .fr-aside-header span {
        font-family: 'Roboto', sans-serif;
        font-weight: 700;
        font-size: 14px;
        line-height: 16px;
        color: #111827;
    }

    .fr-aside-close {
        background: none;
        border: none;
        font-size: 20px;
        line-height: 1;
        color: #64748B;
        cursor: pointer;
        padding: 0;
    }

    .fr-aside-body {
        padding: 18px 18px 0 18px;
        display: flex;
        flex-direction: column;
        gap: 0;
        max-height: 644px;
        overflow-y: auto;
    }

    /* section label row */
    .flt-section-label {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 8px;
    }

    .flt-section-label span {
        font-family: 'Roboto', sans-serif;
        font-weight: 700;
        font-size: 12px;
        line-height: 14px;
        color: #111827;
        white-space: nowrap;
    }

    .flt-divider { flex: 1; height: 1px; background: #F4F6F8; }

    .flt-row { display: flex; align-items: center; padding: 5px 4px; }

    .flt-row label {
        font-family: 'Roboto', sans-serif;
        font-weight: 400;
        font-size: 12px;
        line-height: 14px;
        color: #475569;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 9px;
        width: 100%;
    }

    /* custom checkbox */
    .flt-cb {
        appearance: none;
        -webkit-appearance: none;
        width: 16px;
        height: 16px;
        border: 1px solid #999999;
        border-radius: 2px;
        background: #FFFFFF;
        flex-shrink: 0;
        cursor: pointer;
        position: relative;
        transition: border-color .15s, background .15s;
    }

    .flt-cb:checked {
        background: #2563EB;
        border-color: #2563EB;
    }

    .flt-cb:checked::after {
        content: '';
        position: absolute;
        left: 3px; top: 1px;
        width: 8px; height: 5px;
        border-left: 2px solid #fff;
        border-bottom: 2px solid #fff;
        transform: rotate(-45deg);
    }

    /* custom radio */
    .flt-radio {
        appearance: none;
        -webkit-appearance: none;
        width: 16px;
        height: 16px;
        border: 1px solid #999999;
        border-radius: 50%;
        background: #FFFFFF;
        flex-shrink: 0;
        cursor: pointer;
        position: relative;
        transition: border-color .15s;
    }

    .flt-radio:checked {
        border-color: #2563EB;
        background: radial-gradient(circle at 50% 50%, #2563EB 0%, #2563EB 40%, #FFFFFF 45%);
    }

    /* skill tag cloud */
    .flt-tags {
        position: relative;
        height: 100px;
        margin: 8px 0 16px;
    }

    .flt-tag {
        display: inline-flex;
        align-items: center;
        padding: 5px 13px;
        border-radius: 18px;
        font-family: 'Roboto', sans-serif;
        font-size: 13px;
        line-height: 16px;
        cursor: pointer;
        border: 1px solid #E2E8F0;
        background: #F8FAFC;
        color: #526477;
        font-weight: 400;
        position: absolute;
        white-space: nowrap;
        transition: background .15s, border-color .15s, color .15s;
        user-select: none;
    }

    .flt-tag.active {
        background: #DBEAFE;
        border-color: #93C5FD;
        color: #2563EB;
        font-weight: 600;
    }

    .flt-tag:hover:not(.active) { background: #F1F5F9; }

    .flt-section { padding-top: 16px; margin-bottom: 4px; }

    /* aside footer */
    .fr-aside-footer {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 13px 17px;
        border-top: 1px solid #E2E8F0;
    }

    .fr-btn-reset {
        flex: 1; height: 37px;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-family: 'Roboto', sans-serif;
        font-weight: 400;
        font-size: 14px;
        color: #000000;
        cursor: pointer;
        transition: background .15s;
    }

    .fr-btn-reset:hover { background: #F8FAFC; }

    .fr-btn-apply {
        flex: 1; height: 37px;
        background: #2563EB;
        border: 1px solid #2563EB;
        border-radius: 8px;
        font-family: 'Roboto', sans-serif;
        font-weight: 700;
        font-size: 14px;
        color: #FFFFFF;
        cursor: pointer;
        transition: background .15s;
    }

    .fr-btn-apply:hover { background: #1D4ED8; }

    /* responsive */
    @media (max-width: 1024px) {
        .fr-layout { grid-template-columns: 1fr; }
        .fr-aside   { position: static; }
    }

    @media (max-width: 640px) {
        .ta-btn { font-size: 12px; padding: 5px 10px; }
        .fr-content,
        .content { padding-left: 12px; padding-right: 12px; }
        .fr-layout { gap: 16px; }
        .ta-top { align-items:flex-start; }
        .ta-footer { flex-wrap: wrap; }
        .ta-bar-wrap { flex-basis: calc(100% - 82px); }
        .ta-btn { margin-left: auto; }
        .fr-aside { border-radius: 14px; }
        .flt-tags { height: 108px; }
        .flt-tag { font-size: 11px; }
    }
</style>

<div class="content" style="max-width:1200px">

    {{-- ── Main layout ── --}}
    <div class="fr-layout">

        {{-- LEFT: team list --}}
        <section class="fr-main" aria-label="Daftar tim">

            <div class="fr-heading">
                <h2>Filter Rekrutmen Tim</h2>
                <p>Sesuaikan kriteria untuk menemukan tim yang paling cocok denganmu.</p>
            </div>

            <p class="fr-count" id="fr-count">Sebanyak {{ count($teams) }} tim ditemukan sesuai filter</p>

            @foreach($teams as $team)
            @php
                $isFull   = $team['terisi'] >= $team['total'];
                $pct      = $team['total'] > 0 ? round($team['terisi'] / $team['total'] * 100) : 100;
                $isAlmost = !$isFull && $pct >= 60;

                $badgeClass = $isFull ? 'full' : ($isAlmost ? 'almost' : 'open');
                $badgeLabel = $isFull ? 'Penuh' : ($isAlmost ? 'Hampir Penuh' : 'Dibuka');
                $sisa       = $team['total'] - $team['terisi'];
            @endphp
            <article class="team-article"
                     data-kategori="{{ strtolower($team['kategori']) }}"
                     data-status="{{ $isFull ? 'penuh' : ($isAlmost ? 'hampir' : 'dibuka') }}"
                     data-sisa="{{ $sisa }}"
                     data-skills="{{ implode(',', array_map('strtolower', $team['skills'])) }}">

                {{-- Row 1 --}}
                <div class="ta-top">
                    <div class="av-stack">
                        @foreach($team['anggota'] as $a)
                        <div class="av" style="background:{{ $a['warna'] }}">{{ $a['inisial'] }}</div>
                        @endforeach
                    </div>
                    <div class="ta-info">
                        <span class="ta-name">{{ $team['nama'] }}</span>
                        <span class="ta-cat">{{ $team['lomba'] }}</span>
                    </div>
                    <div class="ta-badge-wrap">
                        <span class="ta-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                    </div>
                </div>

                {{-- Row 2: skills --}}
                <div class="ta-skills">
                    @foreach($team['skills'] as $skill)
                    <span class="ta-chip">{{ $skill }}</span>
                    @endforeach
                </div>

                {{-- Row 3: bar + slot + button --}}
                <div class="ta-footer">
                    <div class="ta-bar-wrap">
                        <div class="ta-bar-fill {{ $isFull ? 'full-bar' : 'active' }}"
                             style="width:{{ $pct }}%"></div>
                    </div>
                    <span class="ta-slot">{{ $team['terisi'] }}/{{ $team['total'] }} slot</span>
                    @if($isFull)
                        <button class="ta-btn penuh" disabled>Penuh</button>
                    @else
                        <form method="POST" action="{{ route('teams.join', $team['id']) }}" style="display:inline">
                            @csrf
                            <button type="submit" class="ta-btn join">Gabung Tim</button>
                        </form>
                    @endif
                </div>

            </article>
            @endforeach

        </section>

        {{-- RIGHT: filter aside --}}
        <aside class="fr-aside" id="fr-aside" aria-label="Filter rekrutmen">

            <div class="fr-aside-header">
                <span>Filter Rekrutmen</span>
                <button class="fr-aside-close" type="button" id="fr-close" aria-label="Tutup filter">×</button>
            </div>

            <div class="fr-aside-body">

                {{-- Kategori Lomba --}}
                <div>
                    <div class="flt-section-label">
                        <span>Kategori Lomba</span>
                        <div class="flt-divider"></div>
                    </div>
                    <div class="flt-row">
                        <label><input type="checkbox" class="flt-cb flt-kategori" value="hackathon" checked> Hackathon</label>
                    </div>
                    <div class="flt-row">
                        <label><input type="checkbox" class="flt-cb flt-kategori" value="business case competition"> Business Case Competition</label>
                    </div>
                    <div class="flt-row">
                        <label><input type="checkbox" class="flt-cb flt-kategori" value="ui/ux design"> UI/UX Design</label>
                    </div>
                    <div class="flt-row">
                        <label><input type="checkbox" class="flt-cb flt-kategori" value="data science & analytics"> Data Science &amp; Analytics</label>
                    </div>
                    <div class="flt-row">
                        <label><input type="checkbox" class="flt-cb flt-kategori" value="business plan"> Business Plan</label>
                    </div>
                </div>

                {{-- Skill Dibutuhkan --}}
                <div class="flt-section">
                    <div class="flt-section-label">
                        <span>Skill Dibutuhkan</span>
                        <div class="flt-divider"></div>
                    </div>
                    <div class="flt-tags">
                        <span class="flt-tag active"  style="left:0;top:0"       data-skill="python">Python</span>
                        <span class="flt-tag"          style="left:78px;top:0"    data-skill="ui design">UI Design</span>
                        <span class="flt-tag"          style="left:0;top:34px"    data-skill="data analysis">Data Analysis</span>
                        <span class="flt-tag"          style="left:115px;top:34px" data-skill="public speaking">Public Speaking</span>
                        <span class="flt-tag"          style="left:0;top:68px"    data-skill="marketing">Marketing</span>
                        <span class="flt-tag"          style="left:95px;top:68px" data-skill="backend dev">Backend Dev</span>
                    </div>
                </div>

                {{-- Status Rekrutmen --}}
                <div class="flt-section" style="padding-top:19px">
                    <div class="flt-section-label">
                        <span>Status Rekrutmen</span>
                        <div class="flt-divider"></div>
                    </div>
                    <div class="flt-row">
                        <label><input type="radio" class="flt-radio" name="status" value="semua" checked> Semua</label>
                    </div>
                    <div class="flt-row">
                        <label><input type="radio" class="flt-radio" name="status" value="dibuka"> Dibuka</label>
                    </div>
                    <div class="flt-row">
                        <label><input type="radio" class="flt-radio" name="status" value="hampir"> Hampir Penuh</label>
                    </div>
                    <div class="flt-row">
                        <label><input type="radio" class="flt-radio" name="status" value="penuh"> Ditutup</label>
                    </div>
                </div>

                {{-- Sisa Slot --}}
                <div class="flt-section" style="padding-top:15px; padding-bottom:16px">
                    <div class="flt-section-label">
                        <span>Sisa Slot</span>
                        <div class="flt-divider"></div>
                    </div>
                    <div class="flt-row">
                        <label><input type="radio" class="flt-radio" name="slot" value="semua" checked> Semua</label>
                    </div>
                    <div class="flt-row">
                        <label><input type="radio" class="flt-radio" name="slot" value="1-2"> 1–2 Orang</label>
                    </div>
                    <div class="flt-row">
                        <label><input type="radio" class="flt-radio" name="slot" value="3+"> 3 Orang atau Lebih</label>
                    </div>
                </div>

            </div>{{-- /fr-aside-body --}}

            <div class="fr-aside-footer">
                <button class="fr-btn-reset" type="button" id="fr-reset">Reset</button>
                <button class="fr-btn-apply" type="button" id="fr-apply">Terapkan Filter</button>
            </div>

        </aside>

    </div>{{-- /fr-layout --}}

</div>{{-- /content --}}

<script>
(function () {
    var all = function(sel, ctx){ return Array.from((ctx||document).querySelectorAll(sel)); };
    var $   = function(sel, ctx){ return (ctx||document).querySelector(sel); };

    var articles = all('.team-article');
    var countEl  = $('#fr-count');
    var globalSearch = $('#fr-global-search');

    // skill tag toggle
    all('.flt-tag').forEach(function(tag){
        tag.addEventListener('click', function(){ tag.classList.toggle('active'); });
    });

    // apply
    function applyFilter(){
        var cats   = all('.flt-kategori:checked').map(function(el){ return el.value; });
        var skills = all('.flt-tag.active').map(function(el){ return el.dataset.skill; });
        var status = ($('input[name="status"]:checked') || {}).value || 'semua';
        var slot   = ($('input[name="slot"]:checked')   || {}).value || 'semua';
        var query  = ((globalSearch && globalSearch.value) || '').trim().toLowerCase();
        var visible = 0;

        articles.forEach(function(art){
            var artKat    = (art.dataset.kategori || '').toLowerCase();
            var artStatus = art.dataset.status || '';
            var artSisa   = parseInt(art.dataset.sisa || '0', 10);
            var artSkills = (art.dataset.skills || '').split(',');
            var searchable = [
                art.querySelector('.ta-name')?.textContent || '',
                art.querySelector('.ta-cat')?.textContent || '',
                art.dataset.kategori || '',
                art.dataset.skills || ''
            ].join(' ').toLowerCase();

            var catOk   = cats.length === 0   || cats.some(function(c){ return artKat.includes(c); });
            var skillOk = skills.length === 0  || skills.some(function(s){ return artSkills.some(function(as){ return as.includes(s) || s.includes(as); }); });
            var statusOk = status === 'semua'  || artStatus === status;
            var searchOk = !query || searchable.includes(query);
            var slotOk   = true;
            if (slot === '1-2') slotOk = artSisa >= 1 && artSisa <= 2;
            else if (slot === '3+') slotOk = artSisa >= 3;

            var show = catOk && skillOk && statusOk && slotOk && searchOk;
            art.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        countEl.textContent = 'Sebanyak ' + visible + ' tim ditemukan sesuai filter';
    }

    // reset
    function resetFilter(){
        all('.flt-kategori').forEach(function(el){ el.checked = false; });
        var hack = $('input.flt-kategori[value="hackathon"]');
        if (hack) hack.checked = true;
        all('.flt-tag').forEach(function(el){ el.classList.remove('active'); });
        var sAll = $('input[name="status"][value="semua"]'); if(sAll) sAll.checked = true;
        var slAll= $('input[name="slot"][value="semua"]');   if(slAll)slAll.checked = true;
        applyFilter();
    }

    $('#fr-apply').addEventListener('click', applyFilter);
    $('#fr-reset').addEventListener('click', resetFilter);
    $('#fr-close').addEventListener('click', function(){
        var aside = $('#fr-aside');
        if(aside) aside.style.display = aside.style.display === 'none' ? '' : 'none';
    });
    if (globalSearch) {
        globalSearch.addEventListener('input', applyFilter);
    }

    // Biarkan semua tim terlihat saat halaman dibuka meski Hackathon tercentang
    // sebagai preferensi filter awal.
    if (globalSearch && globalSearch.value) {
        applyFilter();
    }
})();
</script>

@endsection
