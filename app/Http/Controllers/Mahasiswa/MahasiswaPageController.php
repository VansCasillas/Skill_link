<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\TeamMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MahasiswaPageController extends Controller
{
    public function createTeam(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'competition' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:1000'],
            'skills' => ['nullable', 'string', 'max:500'],
            'capacity' => ['nullable', 'integer', 'min:2', 'max:20'],
        ]);

        $skills = array_values(array_filter(array_map('trim', explode(',', $validated['skills'] ?? ''))));

        Team::create([
            'owner_id' => $request->user()->id,
            'name' => $validated['name'],
            'competition' => $validated['competition'],
            'description' => $validated['description'],
            'skills' => $skills,
            'capacity' => $validated['capacity'] ?? 4,
            'members_count' => 1,
        ]);

        return redirect()->route('tim.index')->with('success', 'Tim berhasil dibuat dan rekrutmen dipublikasikan.');
    }

    public function teams(Request $request): View
    {
        $user = $request->user();
        $search = mb_strtolower(trim((string) $request->query('q', '')));
        Team::firstOrCreate(
            ['name' => 'Tim Garuda Code'],
            [
                'owner_id' => $user->id,
                'competition' => 'Gemastik 2026',
                'capacity' => 5,
                'members_count' => 3,
            ]
        );

        $ownedTeams = Team::where('owner_id', $user->id)->get();
        foreach ($ownedTeams as $ownedTeam) {
            $this->seedDemoTeamData($ownedTeam);
        }

        $teamsQuery = Team::where(function ($query) use ($user): void {
            $query->where('owner_id', $user->id)
                ->orWhereHas('joinRequests', fn ($joinQuery) => $joinQuery->where('user_id', $user->id)->where('status', 'approved'));
        });

        if ($search !== '') {
            $teamsQuery->where(function ($query) use ($search): void {
                $query->whereRaw('LOWER(name) LIKE ?', ['%'.$search.'%'])
                    ->orWhereRaw('LOWER(competition) LIKE ?', ['%'.$search.'%']);
            });
        }

        $teams = $teamsQuery->get()->map(function (Team $team) use ($user): array {
            $approved = $team->joinRequests()
                ->with('user')
                ->where('status', 'approved')
                ->get();

            $initials = collect([$team->owner ? mb_strtoupper(mb_substr($team->owner->name, 0, 1)) : 'A'])
                ->concat($approved->map(fn ($r): string => mb_strtoupper(mb_substr($r->user->name, 0, 1))))
                ->all();

            return [
                'id' => $team->id,
                'name' => $team->name,
                'competition' => $team->competition,
                'description' => $team->description,
                'role' => $team->owner_id === $user->id ? 'Ketua' : 'Anggota',
                'members' => $team->members_count,
                'capacity' => $team->capacity,
                'skills' => $team->skills ?? [],
                'status' => $team->owner_id === $user->id ? 'Dibuka' : 'Aktif',
                'initials' => $initials,
                'approved_members' => $approved->map(fn (TeamJoinRequest $joinRequest): array => [
                    'id' => $joinRequest->id,
                    'user_id' => $joinRequest->user_id,
                    'name' => $joinRequest->user->name,
                ])->all(),
            ];
        })->all();

        $ownedTeamIds = Team::where('owner_id', $user->id)->pluck('id');
        $joinRequests = TeamJoinRequest::with(['user', 'team'])
            ->whereIn('team_id', $ownedTeamIds)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('mahasiswa.kelola-tim', $this->pageData($request, [
            'teams' => $teams,
            'joinRequests' => $joinRequests,
        ]));
    }

    public function teamDetail(Request $request, Team $team): View
    {
        if ($team->owner_id !== $request->user()->id) {
            abort(403);
        }

        $this->seedDemoTeamData($team);

        $joinRequests = TeamJoinRequest::with('user')
            ->where('team_id', $team->id)
            ->latest()
            ->get();

        return view('mahasiswa.detail-tim', $this->pageData($request, [
            'team' => $team,
            'joinRequests' => $joinRequests,
            'approvedMembers' => $joinRequests->where('status', 'approved')->values(),
            'pendingMembers' => $joinRequests->where('status', 'pending')->values(),
        ]));
    }

    public function syncStats(Request $request, Team $team): JsonResponse
    {
        if ($team->owner_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'members_count' => ['nullable', 'integer', 'min:1', 'max:50'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        if (isset($validated['members_count'])) {
            $team->members_count = $validated['members_count'];
        }
        if (isset($validated['capacity'])) {
            $team->capacity = $validated['capacity'];
        }
        $team->save();

        return response()->json([
            'success' => true,
            'members_count' => $team->members_count,
            'capacity' => $team->capacity,
        ]);
    }

    private function seedDemoTeamData(Team $team): void
    {
        if (app()->environment('testing')) {
            return;
        }

        if ($team->joinRequests()->count() > 0) {
            return;
        }

        if (! in_array($team->name, ['Byte Builders', 'Tim Garuda Code'], true)) {
            return;
        }

        $demoUsers = [
            ['name' => 'Nadia Kusuma', 'email' => 'nadia.kusuma@student.ac.id', 'status' => 'approved'],
            ['name' => 'Bima Aditya', 'email' => 'bima.aditya@student.ac.id', 'status' => 'approved'],
            ['name' => 'Rina Maharani', 'email' => 'rina.maharani@student.ac.id', 'status' => 'pending'],
            ['name' => 'Dimas Saputra', 'email' => 'dimas.saputra@student.ac.id', 'status' => 'pending'],
            ['name' => 'Salsa Putri', 'email' => 'salsa.putri@student.ac.id', 'status' => 'pending'],
            ['name' => 'Farhan Ramadhan', 'email' => 'farhan.ramadhan@student.ac.id', 'status' => 'pending'],
            ['name' => 'Alya Safira', 'email' => 'alya.safira@student.ac.id', 'status' => 'pending'],
        ];

        foreach ($demoUsers as $demo) {
            $u = User::firstOrCreate(
                ['email' => $demo['email']],
                [
                    'name' => $demo['name'],
                    'password' => bcrypt('password'),
                    'role' => 'mahasiswa',
                    'npm' => '250109'.rand(100, 999),
                    'prodi' => 'TRPL',
                ]
            );

            TeamJoinRequest::firstOrCreate(
                ['team_id' => $team->id, 'user_id' => $u->id],
                ['status' => $demo['status']]
            );
        }

        $approvedCount = $team->joinRequests()->where('status', 'approved')->count();
        $team->members_count = 1 + $approvedCount;
        if ($team->capacity < 5) {
            $team->capacity = 5;
        }
        $team->save();
    }

    public function updateTeam(Request $request, Team $team): RedirectResponse
    {
        if ($team->owner_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'competition' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'skills' => ['nullable', 'string', 'max:500'],
            'capacity' => ['required', 'integer', 'min:2', 'max:20'],
        ]);

        $skills = array_values(array_filter(array_map('trim', explode(',', $validated['skills'] ?? ''))));

        if ((int) $validated['capacity'] < $team->members_count) {
            return back()->withInput()->with('error', 'Kuota tidak boleh lebih kecil dari jumlah anggota saat ini.');
        }

        $team->update([
            'name' => $validated['name'],
            'competition' => $validated['competition'],
            'description' => $validated['description'] ?? null,
            'skills' => $skills,
            'capacity' => $validated['capacity'],
        ]);

        return back()->with('success', 'Informasi tim berhasil diperbarui.');
    }

    public function removeMember(Request $request, Team $team, TeamJoinRequest $joinRequest): RedirectResponse|JsonResponse
    {
        if ($team->owner_id !== $request->user()->id) {
            abort(403);
        }

        if ($joinRequest->team_id !== $team->id || $joinRequest->status !== 'approved') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Anggota tidak ditemukan di tim ini.'], 422);
            }

            return back()->with('error', 'Anggota tidak ditemukan di tim ini.');
        }

        $joinRequest->delete();
        $team->decrement('members_count');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Anggota berhasil dikeluarkan dari tim.',
                'members_count' => $team->members_count,
                'capacity' => $team->capacity,
            ]);
        }

        return back()->with('success', 'Anggota berhasil dikeluarkan dari tim.');
    }

    public function approveJoinRequest(Request $request, TeamJoinRequest $joinRequest): RedirectResponse|JsonResponse
    {
        if ($joinRequest->team->owner_id !== $request->user()->id) {
            abort(403);
        }

        if ($joinRequest->status !== 'pending') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Permintaan ini sudah diproses.'], 422);
            }

            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $team = $joinRequest->team;
        if ($team->members_count >= $team->capacity) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Kuota anggota tim sudah penuh.'], 422);
            }

            return back()->with('error', 'Kuota anggota tim sudah penuh.');
        }

        $joinRequest->update(['status' => 'approved']);
        $team->increment('members_count');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $joinRequest->user->name.' diterima menjadi anggota tim.',
                'members_count' => $team->members_count,
                'capacity' => $team->capacity,
            ]);
        }

        return back()->with('success', $joinRequest->user->name.' diterima menjadi anggota tim.');
    }

    public function rejectJoinRequest(Request $request, TeamJoinRequest $joinRequest): RedirectResponse|JsonResponse
    {
        if ($joinRequest->team->owner_id !== $request->user()->id) {
            abort(403);
        }

        if ($joinRequest->status !== 'pending') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Permintaan ini sudah diproses.'], 422);
            }

            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $joinRequest->update(['status' => 'rejected']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permintaan bergabung ditolak.',
            ]);
        }

        return back()->with('success', 'Permintaan bergabung ditolak.');
    }

    public function messages(Request $request): View
    {
        $user = $request->user();
        $teams = Team::where('owner_id', $user->id)
            ->orWhereHas('joinRequests', fn ($query) => $query
                ->where('user_id', $user->id)
                ->where('status', 'approved'))
            ->orderBy('name')
            ->get();

        $selectedTeam = $teams->firstWhere('id', (int) $request->query('team'))
            ?? $teams->first();
        $chatMessages = $selectedTeam
            ? TeamMessage::with('user')->where('team_id', $selectedTeam->id)->oldest()->get()
            : collect();

        return view('mahasiswa.pesan', $this->pageData($request, [
            'teams' => $teams,
            'selectedTeam' => $selectedTeam,
            'chatMessages' => $chatMessages,
        ]));
    }

    public function sendMessage(Request $request, Team $team): RedirectResponse
    {
        $user = $request->user();
        $canAccess = $team->owner_id === $user->id
            || $team->joinRequests()
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->exists();

        if (! $canAccess) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        TeamMessage::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        return redirect()->route('messages.index', ['team' => $team->id])
            ->with('success', 'Pesan berhasil dikirim.');
    }

    public function notifications(Request $request): View
    {
        return view('mahasiswa.notifikasi', $this->pageData($request, [
            'notifications' => [
                [
                    'title' => 'Undangan tim diterima',
                    'description' => 'Anda telah bergabung dengan Tim Garuda Code.',
                    'time' => '10 menit yang lalu',
                ],
                [
                    'title' => 'Deadline lomba mendekat',
                    'description' => 'Gemastik 2026 akan ditutup pada 30 November 2026.',
                    'time' => '2 jam yang lalu',
                ],
                [
                    'title' => 'Profil belum lengkap',
                    'description' => 'Lengkapi profil agar lebih mudah ditemukan oleh tim.',
                    'time' => 'Kemarin',
                ],
            ],
        ]));
    }

    private function pageData(Request $request, array $data): array
    {
        $user = $request->user();

        return array_merge($data, [
            'menu' => [
                ['Beranda', route('dashboard'), 'layout-dashboard', 'dashboard*'],
                ['Profil Saya', route('profile'), 'user', 'profile*|profil*'],
                ['Pencarian', route('recruitment.index'), 'users', '*find-teams*|*cari-tim*|*filter-rekrutmen*'],
                ['Filter Rekrutmen', route('filter-rekrutmen'), 'sliders-horizontal', 'filter-rekrutmen*'],
                ['Kelola Tim', route('tim.index'), 'users-round', 'my-teams*|teams/*/detail'],
                ['Pusat Lomba', route('competition.index'), 'trophy', 'find-competitions*|lomba*'],
                ['Susunan Portofolio', route('portofolio.index'), 'briefcase', 'susunan-portofolio*'],
                ['Pesan', route('messages.index'), 'message-circle', 'messages*'],
                ['Notifikasi', route('notifications'), 'bell', 'notifications*'],
            ],
            'user' => [
                'name' => $user->name,
                'prodi' => $user->prodi ?? 'TRPL',
                'angkatan' => $user->angkatan ?? substr((string) $user->npm, 0, 2),
                'inisial' => strtoupper(mb_substr($user->name, 0, 1)),
            ],
        ]);
    }
}
