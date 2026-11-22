<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\TeamMessage;
use Illuminate\Contracts\View\View;
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
        $ownedTeam = Team::firstOrCreate(
            ['name' => 'Tim Garuda Code'],
            [
                'owner_id' => $user->id,
                'competition' => 'Gemastik 2026',
                'capacity' => 4,
                'members_count' => 3,
            ]
        );

        $teams = Team::where(function ($query) use ($user): void {
            $query->where('owner_id', $user->id)
                ->orWhereHas('joinRequests', fn ($joinQuery) => $joinQuery->where('user_id', $user->id)->where('status', 'approved'));
        })->get()->map(function (Team $team) use ($user): array {
            return [
                'id' => $team->id,
                'name' => $team->name,
                'competition' => $team->competition,
                'role' => $team->owner_id === $user->id ? 'Ketua' : 'Anggota',
                'members' => $team->members_count,
                'capacity' => $team->capacity,
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

    public function approveJoinRequest(Request $request, TeamJoinRequest $joinRequest): RedirectResponse
    {
        if ($joinRequest->team->owner_id !== $request->user()->id) {
            abort(403);
        }

        if ($joinRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $team = $joinRequest->team;
        if ($team->members_count >= $team->capacity) {
            return back()->with('error', 'Kuota anggota tim sudah penuh.');
        }

        $joinRequest->update(['status' => 'approved']);
        $team->increment('members_count');

        return back()->with('success', $joinRequest->user->name.' diterima menjadi anggota tim.');
    }

    public function rejectJoinRequest(Request $request, TeamJoinRequest $joinRequest): RedirectResponse
    {
        if ($joinRequest->team->owner_id !== $request->user()->id) {
            abort(403);
        }

        if ($joinRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $joinRequest->update(['status' => 'rejected']);

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
                ['Dashboard', route('dashboard'), 'layout-dashboard', 'dashboard*'],
                ['Profil Saya', route('profile'), 'user', 'profile*|profil*'],
                ['Cari Tim', route('recruitment.index'), 'users', '*find-teams*|*cari-tim*|*filter-rekrutmen*'],
                ['Kelola Tim', route('tim.index'), 'users-round', 'my-teams*'],
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
