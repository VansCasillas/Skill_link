<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\TeamMessage;
use App\Models\User;
use App\Notifications\TeamActivityNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            ->where('type', 'application')
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
            ->where(fn ($query) => $query
                ->where('type', 'application')
                ->orWhere('status', 'approved'))
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
        if ($joinRequest->type !== 'application' || $joinRequest->team->owner_id !== $request->user()->id) {
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
        $joinRequest->user->notify(new TeamActivityNotification(
            'Permintaan gabung diterima',
            'Anda diterima bergabung ke tim '.$team->name.'.',
            route('messages.index', ['team' => $team->id]),
        ));

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
        if ($joinRequest->type !== 'application' || $joinRequest->team->owner_id !== $request->user()->id) {
            abort(403);
        }

        if ($joinRequest->status !== 'pending') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Permintaan ini sudah diproses.'], 422);
            }

            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $joinRequest->update(['status' => 'rejected']);
        $joinRequest->user->notify(new TeamActivityNotification(
            'Permintaan gabung ditolak',
            'Permintaan Anda untuk bergabung ke tim '.$joinRequest->team->name.' ditolak.',
            route('recruitment.index'),
        ));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permintaan bergabung ditolak.',
            ]);
        }

        return back()->with('success', 'Permintaan bergabung ditolak.');
    }

    public function inviteMember(Request $request, User $member): RedirectResponse
    {
        $validated = $request->validate([
            'team_id' => ['required', 'integer', 'exists:teams,id'],
        ]);
        $team = Team::whereKey($validated['team_id'])
            ->where('owner_id', $request->user()->id)
            ->firstOrFail();

        if ($member->id === $request->user()->id || $member->role !== 'mahasiswa') {
            abort(403);
        }

        if ($team->members_count >= $team->capacity) {
            return back()->with('error', 'Kuota anggota '.$team->name.' sudah penuh.');
        }

        $joinRequest = TeamJoinRequest::firstOrNew([
            'team_id' => $team->id,
            'user_id' => $member->id,
        ]);

        if ($joinRequest->exists && in_array($joinRequest->status, ['pending', 'approved'], true)) {
            return back()->with('error', 'Undangan atau keanggotaan untuk '.$member->name.' sudah tercatat.');
        }

        $joinRequest->fill([
            'type' => 'invitation',
            'status' => 'pending',
        ])->save();

        $member->notify(new TeamActivityNotification(
            'Undangan bergabung ke tim',
            'Anda diundang bergabung ke tim '.$team->name.'.',
            route('recruitment.index', ['mode' => 'anggota']),
        ));

        return back()->with('success', 'Undangan bergabung berhasil dikirim kepada '.$member->name.'.');
    }

    public function acceptTeamInvitation(Request $request, TeamJoinRequest $joinRequest): RedirectResponse
    {
        if ($joinRequest->type !== 'invitation' || $joinRequest->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($joinRequest->status !== 'pending') {
            return back()->with('error', 'Undangan ini sudah diproses.');
        }

        $team = $joinRequest->team;
        if ($team->members_count >= $team->capacity) {
            return back()->with('error', 'Kuota anggota '.$team->name.' sudah penuh.');
        }

        $joinRequest->update(['status' => 'approved']);
        $team->increment('members_count');
        $team->owner?->notify(new TeamActivityNotification(
            'Undangan tim diterima',
            $request->user()->name.' menerima undangan untuk bergabung ke tim '.$team->name.'.',
            route('messages.index', ['team' => $team->id]),
        ));

        return back()->with('success', 'Anda berhasil bergabung ke '.$team->name.'.');
    }

    public function rejectTeamInvitation(Request $request, TeamJoinRequest $joinRequest): RedirectResponse
    {
        if ($joinRequest->type !== 'invitation' || $joinRequest->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($joinRequest->status !== 'pending') {
            return back()->with('error', 'Undangan ini sudah diproses.');
        }

        $joinRequest->update(['status' => 'rejected']);
        $joinRequest->team->owner?->notify(new TeamActivityNotification(
            'Undangan tim ditolak',
            $request->user()->name.' menolak undangan untuk bergabung ke tim '.$joinRequest->team->name.'.',
            route('tim.index'),
        ));

        return back()->with('success', 'Undangan berhasil ditolak.');
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
            ? $selectedTeam->messages()
                ->with('user')
                ->whereDoesntHave('hiddenByUsers', fn ($query) => $query->where('users.id', $user->id))
                ->oldest()
                ->get()
            : collect();
        $oldMessagesCount = $chatMessages
            ->filter(fn (TeamMessage $message): bool => $message->created_at->lt(now()->subDays(30)))
            ->count();

        return view('mahasiswa.pesan', $this->pageData($request, [
            'teams' => $teams,
            'selectedTeam' => $selectedTeam,
            'chatMessages' => $chatMessages,
            'oldMessagesCount' => $oldMessagesCount,
        ]));
    }

    public function sendMessage(Request $request, Team $team): RedirectResponse
    {
        $user = $request->user();
        if (! $this->canAccessTeam($user, $team)) {
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

        $recipientIds = $team->joinRequests()
            ->where('status', 'approved')
            ->pluck('user_id');

        if ($team->owner_id !== null) {
            $recipientIds->push($team->owner_id);
        }

        $recipientIds = $recipientIds
            ->unique()
            ->reject(fn (int|string $recipientId): bool => (int) $recipientId === $user->id);
        $notification = new TeamActivityNotification(
            'Pesan baru di '.$team->name,
            $user->name.': '.Str::limit($validated['body'], 120),
            route('messages.index', ['team' => $team->id]),
        );

        User::query()->whereIn('id', $recipientIds)->get()->each(
            fn (User $recipient): mixed => $recipient->notify($notification)
        );

        return redirect()->route('messages.index', ['team' => $team->id])
            ->with('success', 'Pesan berhasil dikirim.');
    }

    public function clearOldMessages(Request $request, Team $team): RedirectResponse
    {
        $user = $request->user();

        if (! $this->canAccessTeam($user, $team)) {
            abort(403);
        }

        $messageIds = $team->messages()
            ->where('created_at', '<', now()->subDays(30))
            ->whereDoesntHave('hiddenByUsers', fn ($query) => $query->where('users.id', $user->id))
            ->pluck('team_messages.id');

        if ($messageIds->isNotEmpty()) {
            $timestamp = now();
            $hiddenMessages = $messageIds->map(fn (int|string $messageId): array => [
                'team_message_id' => (int) $messageId,
                'user_id' => $user->id,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ])->all();

            foreach (array_chunk($hiddenMessages, 500) as $hiddenMessageChunk) {
                DB::table('hidden_team_messages')->insertOrIgnore($hiddenMessageChunk);
            }
        }

        return redirect()->route('messages.index', ['team' => $team->id])
            ->with('success', $messageIds->count().' pesan lama disembunyikan hanya untuk Anda.');
    }

    public function notifications(Request $request): View
    {
        $user = $request->user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        $oldNotificationCount = $user->notifications()
            ->where('created_at', '<', now()->subDays(30))
            ->count();
        $notifications = $user->notifications()
            ->latest()
            ->get()
            ->map(fn (DatabaseNotification $notification): array => [
                'title' => $notification->data['title'] ?? 'Notifikasi baru',
                'description' => $notification->data['description'] ?? '',
                'time' => $notification->created_at?->diffForHumans() ?? '',
                'url' => $notification->data['url'] ?? route('notifications'),
            ]);

        return view('mahasiswa.notifikasi', $this->pageData($request, [
            'notifications' => $notifications,
            'oldNotificationCount' => $oldNotificationCount,
        ]));
    }

    public function clearOldNotifications(Request $request): RedirectResponse
    {
        $deletedCount = $request->user()->notifications()
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        return redirect()->route('notifications')
            ->with('success', $deletedCount.' notifikasi lama berhasil dibersihkan.');
    }

    private function canAccessTeam(User $user, Team $team): bool
    {
        return $team->owner_id === $user->id
            || $team->joinRequests()
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->exists();
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
