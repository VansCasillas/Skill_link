<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Carbon\Carbon;
use Database\Seeders\CompetitionSeeder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CompetitionController extends Controller
{
    public function index(Request $request): View
    {
        $this->markExpiredCompetitions();

        if (Competition::count() === 0) {
            (new CompetitionSeeder)->run();
            $this->markExpiredCompetitions();
        }

        $query = Competition::query();
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $sort = (string) $request->query('sort', 'nearest');

        if ($search !== '') {
            $query->where(function ($competitionQuery) use ($search): void {
                $competitionQuery
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('organizer', 'like', '%'.$search.'%')
                    ->orWhere('category', 'like', '%'.$search.'%');
            });
        }

        if (in_array($status, ['active', 'draft', 'ended'], true)) {
            $query->where('status', $status);
        }

        match ($sort) {
            'farthest' => $query->orderByRaw("CASE WHEN status = 'ended' THEN 1 ELSE 0 END")->orderByDesc('deadline'),
            'recent' => $query->orderByDesc('created_at'),
            default => $query->orderByRaw("CASE WHEN status = 'ended' THEN 1 ELSE 0 END")->orderBy('deadline'),
        };

        $competitions = $query->orderByDesc('id')->paginate(10)->withQueryString();

        $upcomingDeadlines = Competition::query()
            ->whereIn('status', ['active', 'draft'])
            ->whereDate('deadline', '>=', today())
            ->orderBy('deadline')
            ->limit(3)
            ->get();

        $draftCount = Competition::where('status', 'draft')->count();

        $needsAction = Competition::query()
            ->where('status', 'draft')
            ->orderBy('deadline')
            ->get();

        return view('mahasiswa.kelola-pusat-informasi-lomba', [
            'menu' => $this->adminMenu(),
            'user' => $this->layoutUser($request, true),
            'competitions' => $competitions,
            'upcomingDeadlines' => $upcomingDeadlines,
            'needsAction' => $needsAction,
            'draftCount' => $draftCount,
            'stats' => [
                'total' => Competition::count(),
                'active' => Competition::where('status', 'active')->count(),
                'draft' => Competition::where('status', 'draft')->count(),
                'ended' => Competition::where('status', 'ended')->count(),
            ],
            'filters' => compact('search', 'status', 'sort'),
        ]);
    }

    public function publicIndex(Request $request): RedirectResponse
    {
        $query = array_filter(['q' => $request->query('q')]);

        return redirect()->route('recruitment.index', $query);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeRequestData($request);

        $validated = $request->validate($this->rules());
        $validated['poster_path'] = $request->file('poster')?->store('competitions', 'public');
        unset($validated['poster']);

        Competition::create($validated);

        $message = ($validated['status'] === 'active')
            ? 'Lomba berhasil diterbitkan.'
            : 'Lomba berhasil disimpan sebagai draf.';

        return redirect()->route('competition.manage.index')->with('success', $message);
    }

    public function update(Request $request, Competition $competition): RedirectResponse
    {
        $this->normalizeRequestData($request);

        $validated = $request->validate($this->rules());

        if ($validated['status'] === 'active' && Carbon::parse($validated['deadline'])->isBefore(today())) {
            return back()
                ->withErrors(['deadline' => 'Ubah deadline ke hari ini atau setelahnya sebelum mengaktifkan lomba.'])
                ->withInput()
                ->with('_form_mode', 'edit')
                ->with('_competition_id', $competition->id);
        }

        $oldPosterPath = $competition->poster_path;
        $newPosterPath = $request->file('poster')?->store('competitions', 'public');
        unset($validated['poster']);

        if ($newPosterPath !== null) {
            $validated['poster_path'] = $newPosterPath;
        }

        $competition->update($validated);

        if ($newPosterPath !== null && $oldPosterPath !== null) {
            Storage::disk('public')->delete($oldPosterPath);
        }

        return redirect()->route('competition.manage.index')->with('success', 'Informasi lomba berhasil diperbarui.');
    }

    public function destroy(Competition $competition): RedirectResponse
    {
        if ($competition->poster_path !== null) {
            Storage::disk('public')->delete($competition->poster_path);
        }

        $competition->delete();

        return redirect()->route('competition.manage.index')->with('success', 'Lomba berhasil dihapus.');
    }

    public function publish(Competition $competition): RedirectResponse
    {
        if ($competition->status !== 'draft') {
            return back()->with('error', 'Hanya lomba berstatus draf yang dapat diterbitkan.');
        }

        $competition->update(['status' => 'active']);

        return redirect()->route('competition.manage.index')->with('success', 'Lomba berhasil diterbitkan.');
    }

    private function normalizeRequestData(Request $request): void
    {
        $rawFee = $request->input('fee');
        if ($rawFee === null || trim((string) $rawFee) === '' || strtolower(trim((string) $rawFee)) === 'gratis') {
            $request->merge(['fee' => 0]);
        } else {
            $cleanFee = (int) preg_replace('/[^0-9]/', '', (string) $rawFee);
            $request->merge(['fee' => $cleanFee]);
        }

        if (! $request->filled('opens_at')) {
            $request->merge(['opens_at' => today()->toDateString()]);
        }

        $deadline = $request->input('deadline');
        if ($deadline && preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', (string) $deadline, $matches)) {
            $request->merge(['deadline' => sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1])]);
        }

        if (! $request->filled('status')) {
            $request->merge(['status' => 'draft']);
        }
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'organizer' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:5000'],
            'category' => ['required', 'string', 'max:100'],
            'level' => ['required', Rule::in(['Regional', 'Nasional', 'Internasional'])],
            'opens_at' => ['required', 'date'],
            'deadline' => ['required', 'date'],
            'fee' => ['required', 'integer', 'min:0', 'max:100000000'],
            'registration_url' => ['required', 'url', 'max:2048'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['required', Rule::in(['draft', 'active'])],
        ];
    }

    private function markExpiredCompetitions(): void
    {
        Competition::query()
            ->where('status', '!=', 'ended')
            ->whereDate('deadline', '<', today())
            ->update(['status' => 'ended']);
    }

    private function adminMenu(): array
    {
        return [
            ['Dashboard', route('dashboard'), 'layout-dashboard', 'dashboard*'],
            ['Profil Saya', route('profile'), 'user', 'profile*|profil*'],
            ['Pencarian', route('recruitment.index'), 'users', ['find-teams*', 'cari-tim*', 'recruitment*']],
            ['Filter Rekrutmen', route('filter-rekrutmen'), 'sliders-horizontal', 'filter-rekrutmen*'],
            ['Kelola Tim', route('tim.index'), 'users-round', 'my-teams*'],
            ['Susunan Portofolio', route('portofolio.index'), 'briefcase', 'susunan-portofolio*'],
            ['Kelola Pusat Informasi Lomba', route('competition.manage.index'), 'trophy', 'admin/kelola-pusat-informasi*'],
            ['Validasi Nilai & Sertifikat', route('portfolio.validation.index'), 'badge-check', 'admin/validasi-portofolio*'],
        ];
    }

    private function studentMenu(): array
    {
        return [
            ['Beranda', route('dashboard'), 'layout-dashboard', 'dashboard*'],
            ['Profil Saya', route('profile'), 'user', 'profile*'],
            ['Pencarian', route('recruitment.index'), 'users', ['*find-teams*', '*cari-tim*', '*recruitment*']],
            ['Filter Rekrutmen', route('filter-rekrutmen'), 'sliders-horizontal', 'filter-rekrutmen*'],
            ['Kelola Tim', route('tim.index'), 'users-round', 'my-teams*'],
            ['Susunan Portofolio', route('portofolio.index'), 'briefcase', 'susunan-portofolio*'],
            ['Pesan', route('messages.index'), 'message-circle', 'messages*'],
            ['Notifikasi', route('notifications'), 'bell', 'notifications*'],
        ];
    }

    private function layoutUser(Request $request, bool $isAdmin = false): array
    {
        $user = $request->user();

        return [
            'name' => $isAdmin ? 'Dzaky' : ($user->name ?? 'Dzaky'),
            'prodi' => 'TRPL',
            'angkatan' => '2025',
            'subline' => $isAdmin ? 'Admin · TRPL' : (($user->prodi ?? 'TRPL').' · Angkatan '.($user->angkatan ?? '2025')),
            'inisial' => mb_strtoupper(mb_substr($user->name ?? 'D', 0, 1)),
        ];
    }
}
