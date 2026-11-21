<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Carbon\Carbon;
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

        $query = Competition::query();
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $sort = (string) $request->query('sort', 'nearest');

        if ($search !== '') {
            $query->where(function ($competitionQuery) use ($search): void {
                $competitionQuery
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('organizer', 'like', '%'.$search.'%');
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

        $competitions = $query->orderByDesc('id')->paginate(8)->withQueryString();
        $upcomingDeadlines = Competition::query()
            ->whereIn('status', ['active', 'draft'])
            ->whereDate('deadline', '>=', today())
            ->orderBy('deadline')
            ->limit(4)
            ->get();

        $needsAction = Competition::query()
            ->orderBy('deadline')
            ->get()
            ->filter(function (Competition $competition): bool {
                if ($competition->status === 'draft' || ! $competition->isCompleteForPublication()) {
                    return true;
                }

                return $competition->status === 'active'
                    && today()->diffInDays($competition->deadline, false) <= 7;
            })
            ->take(5)
            ->values();

        return view('mahasiswa.kelola-pusat-informasi-lomba', [
            'menu' => $this->adminMenu(),
            'user' => $this->layoutUser($request, true),
            'competitions' => $competitions,
            'upcomingDeadlines' => $upcomingDeadlines,
            'needsAction' => $needsAction,
            'stats' => [
                'total' => Competition::count(),
                'active' => Competition::where('status', 'active')->count(),
                'draft' => Competition::where('status', 'draft')->count(),
                'ended' => Competition::where('status', 'ended')->count(),
            ],
            'filters' => compact('search', 'status', 'sort'),
        ]);
    }

    public function publicIndex(Request $request): View
    {
        $this->markExpiredCompetitions();

        $search = trim((string) $request->query('q', ''));
        $competitions = Competition::query()
            ->where('status', 'active')
            ->whereDate('deadline', '>=', today())
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($competitionQuery) use ($search): void {
                    $competitionQuery
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('organizer', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('deadline')
            ->paginate(9)
            ->withQueryString();

        return view('mahasiswa.pusat-informasi-lomba', [
            'menu' => $this->studentMenu(),
            'user' => $this->layoutUser($request),
            'competitions' => $competitions,
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['poster_path'] = $request->file('poster')?->store('competitions', 'public');
        unset($validated['poster']);
        $validated['status'] = 'draft';

        Competition::create($validated);

        return redirect()->route('competition.manage.index')->with('success', 'Lomba berhasil disimpan sebagai draf.');
    }

    public function update(Request $request, Competition $competition): RedirectResponse
    {
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

        if (! $competition->isCompleteForPublication()) {
            return back()->with('error', 'Lengkapi nama, penyelenggara, deskripsi, kategori, tingkat, tanggal, dan tautan pendaftaran sebelum menerbitkan lomba.');
        }

        $competition->update(['status' => 'active']);

        return redirect()->route('competition.manage.index')->with('success', 'Lomba berhasil diterbitkan dan kini terlihat oleh mahasiswa.');
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
            'deadline' => ['required', 'date', 'after_or_equal:opens_at'],
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
            ['Kelola Pusat Lomba', route('competition.manage.index'), 'trophy', 'admin/kelola-pusat-informasi*'],
        ];
    }

    private function studentMenu(): array
    {
        return [
            ['Dashboard', route('dashboard'), 'layout-dashboard', 'dashboard*'],
            ['Profil Saya', route('profile'), 'user', 'profile*'],
            ['Cari Tim', route('recruitment.index'), 'users', '*find-teams*|*cari-tim*|*filter-rekrutmen*'],
            ['Kelola Tim', route('tim.index'), 'users-round', 'my-teams*'],
            ['Pusat Lomba', route('competition.index'), 'trophy', 'find-competitions*'],
            ['Susunan Portofolio', route('portofolio.index'), 'briefcase', 'susunan-portofolio*'],
            ['Pesan', route('messages.index'), 'message-circle', 'messages*'],
            ['Notifikasi', route('notifications'), 'bell', 'notifications*'],
        ];
    }

    private function layoutUser(Request $request, bool $isAdmin = false): array
    {
        $user = $request->user();

        return [
            'name' => $user->name,
            'prodi' => $isAdmin ? 'Admin' : 'TRPL',
            'angkatan' => $isAdmin ? 'Skill Link' : substr((string) $user->npm, 0, 2),
            'subline' => $isAdmin ? 'Admin · Skill Link' : null,
            'inisial' => mb_strtoupper(mb_substr($user->name, 0, 1)),
        ];
    }
}
