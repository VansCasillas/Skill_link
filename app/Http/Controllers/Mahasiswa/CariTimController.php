<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CariTimController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        $search = mb_strtolower(trim((string) $request->query('q', '')));
        $mode = $request->query('mode') === 'anggota' ? 'anggota' : 'tim';

        $menu = [
            ['Beranda', route('dashboard'), 'layout-dashboard', 'dashboard*'],
            ['Profil Saya', route('profile'), 'user', 'profile*'],
            ['Pencarian', route('recruitment.index'), 'users', '*find-teams*|*cari-tim*'],
            ['Filter Rekrutmen', route('filter-rekrutmen'), 'sliders-horizontal', 'filter-rekrutmen*'],
            ['Kelola Tim', route('tim.index'), 'users-round', 'my-teams*'],
            ['Susunan Portofolio', route('portofolio.index'), 'briefcase', 'susunan-portofolio*'],
            ['Pesan', route('messages.index'), 'message-circle', 'messages*'],
            ['Notifikasi', route('notifications'), 'bell', 'notifications*'],
        ];

        $user = [
            'name' => $u->name ?? 'Dzaky',
            'prodi' => $u->prodi ?? 'TRPL',
            'angkatan' => $u->angkatan ?? (isset($u->npm) ? '20'.substr($u->npm, 0, 2) : '2025'),
            'inisial' => strtoupper(mb_substr($u->name ?? 'D', 0, 1)),
        ];

        $ownedTeams = Team::where('owner_id', $u->id)
            ->whereColumn('members_count', '<', 'capacity')
            ->orderBy('name')
            ->get();
        $ownedTeamIds = $ownedTeams->modelKeys();
        $existingTeamRequests = TeamJoinRequest::query()
            ->whereIn('team_id', $ownedTeamIds)
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->groupBy('user_id');

        $allTeams = Team::query()
            ->with(['owner', 'joinRequests' => fn ($query) => $query->where('status', 'approved')->with('user')])
            ->whereNotNull('owner_id')
            ->where('owner_id', '!=', $u->id)
            ->whereColumn('members_count', '<', 'capacity')
            ->whereDoesntHave('joinRequests', fn ($query) => $query
                ->where('user_id', $u->id)
                ->whereIn('status', ['pending', 'approved']))
            ->latest()
            ->get()
            ->map(function (Team $team): array {
                $members = collect([$team->owner])
                    ->concat($team->joinRequests->pluck('user'))
                    ->filter()
                    ->map(fn (User $member): array => [
                        'inisial' => mb_strtoupper(mb_substr($member->name, 0, 1)),
                        'warna' => '#14B8A6',
                    ])
                    ->values()
                    ->all();
                $skills = $team->skills ?? [];

                return [
                    'id' => $team->id,
                    'nama' => $team->name,
                    'inisial' => mb_strtoupper(mb_substr($team->name, 0, 1)),
                    'warna' => '#0369A1',
                    'lomba' => $team->competition ?? 'Belum menentukan lomba',
                    'butuh_teks' => 'Butuh '.max(0, $team->capacity - $team->members_count).' anggota',
                    'deskripsi' => $team->description ?? 'Tim ini belum menambahkan deskripsi.',
                    'skills' => $skills,
                    'terisi' => $team->members_count,
                    'total' => $team->capacity,
                    'deadline' => '',
                    'anggota' => $members,
                ];
            });
        $teams = $allTeams->filter(function (array $team) use ($search): bool {
            if ($search === '') {
                return true;
            }

            $searchable = mb_strtolower(implode(' ', [
                $team['nama'],
                $team['lomba'],
                $team['deskripsi'],
                ...$team['skills'],
            ]));

            return str_contains($searchable, $search);
        })->values();

        $allMembers = User::query()
            ->where('role', 'mahasiswa')
            ->whereKeyNot($u->id)
            ->with(['portfolios' => fn ($query) => $query
                ->where('status', 'approved')
                ->where('is_visible', true)
                ->orderBy('sort_order')])
            ->orderBy('name')
            ->get()
            ->map(function (User $member) use ($ownedTeams, $existingTeamRequests): array {
                $portfolios = $member->portfolios->map(fn (Portfolio $portfolio): array => [
                    'judul' => $portfolio->title,
                    'tipe' => $portfolio->type,
                    'deskripsi' => $portfolio->description ?? '',
                    'tags' => $portfolio->tags ?? [],
                    'bukti_url' => Storage::disk('public')->url($portfolio->evidence_path),
                ]);
                $skills = $portfolios->flatMap(fn (array $portfolio): array => $portfolio['tags'])
                    ->filter()
                    ->unique(fn (string $skill): string => mb_strtolower($skill))
                    ->values()
                    ->all();
                $existingTeamIds = $existingTeamRequests->get($member->id, collect())
                    ->pluck('team_id')
                    ->all();

                return [
                    'id' => $member->id,
                    'nama' => $member->name,
                    'inisial' => mb_strtoupper(mb_substr($member->name, 0, 1)),
                    'prodi' => $member->prodi ?? $member->jurusan ?? 'Program studi belum diisi',
                    'angkatan' => $member->angkatan,
                    'bio' => $member->bio,
                    'skills' => $skills,
                    'portfolios' => $portfolios->all(),
                    'eligible_teams' => $ownedTeams
                        ->reject(fn (Team $team): bool => in_array($team->id, $existingTeamIds))
                        ->map(fn (Team $team): array => [
                            'id' => $team->id,
                            'nama' => $team->name,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values();
        $members = $allMembers->filter(function (array $member) use ($search): bool {
            if ($search === '') {
                return true;
            }

            $portfolioText = collect($member['portfolios'])
                ->flatMap(fn (array $portfolio): array => [
                    $portfolio['judul'],
                    $portfolio['tipe'],
                    $portfolio['deskripsi'],
                    ...$portfolio['tags'],
                ]);
            $searchable = mb_strtolower(implode(' ', [
                $member['nama'],
                $member['prodi'],
                $member['angkatan'] ?? '',
                $member['bio'] ?? '',
                ...$member['skills'],
                ...$portfolioText->all(),
            ]));

            return str_contains($searchable, $search);
        })->values();

        $teamSuggestions = $allTeams->flatMap(function (array $team): array {
            return array_merge(
                [
                    ['value' => $team['nama'], 'type' => 'Tim', 'mode' => 'tim'],
                    ['value' => $team['lomba'], 'type' => 'Lomba', 'mode' => 'tim'],
                ],
                array_map(fn (string $skill): array => [
                    'value' => $skill,
                    'type' => 'Skill',
                    'mode' => 'tim',
                ], $team['skills']),
            );
        })->take(50);
        $memberSuggestions = $allMembers->flatMap(function (array $member): array {
            return array_merge(
                [
                    ['value' => $member['nama'], 'type' => 'Anggota', 'mode' => 'anggota'],
                    ['value' => $member['prodi'], 'type' => 'Program studi', 'mode' => 'anggota'],
                ],
                array_map(fn (string $skill): array => [
                    'value' => $skill,
                    'type' => 'Skill',
                    'mode' => 'anggota',
                ], $member['skills']),
                collect($member['portfolios'])
                    ->map(fn (array $portfolio): array => [
                        'value' => $portfolio['judul'],
                        'type' => $portfolio['tipe'],
                        'mode' => 'anggota',
                    ])
                    ->all(),
            );
        })->take(50);
        $suggestions = $teamSuggestions
            ->concat($memberSuggestions)
            ->filter(fn (array $suggestion): bool => filled($suggestion['value']))
            ->unique(fn (array $suggestion): string => mb_strtolower($suggestion['value'].'|'.$suggestion['mode']))
            ->sortBy(fn (array $suggestion): int => $suggestion['mode'] === $mode ? 0 : 1)
            ->take(100)
            ->values();

        $receivedInvitations = TeamJoinRequest::with(['team.owner'])
            ->where('user_id', $u->id)
            ->where('type', 'invitation')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('mahasiswa.cari-tim', [
            'menu' => $menu,
            'user' => $user,
            'teams' => $teams->all(),
            'members' => $members->all(),
            'mode' => $mode,
            'suggestions' => $suggestions,
            'ownedTeams' => $ownedTeams,
            'receivedInvitations' => $receivedInvitations,
        ]);
    }

    public function filterRekrutmen(Request $request)
    {
        $u = $request->user();
        $search = mb_strtolower(trim((string) $request->query('q', '')));

        $menu = [
            ['Beranda',      route('dashboard'),          'layout-dashboard', 'dashboard*'],
            ['Profil Saya',  route('profile'),            'user',             'profile*'],
            ['Pencarian',    route('recruitment.index'),  'users',            '*find-teams*|*cari-tim*|*filter-rekrutmen*'],
            ['Filter Rekrutmen', route('filter-rekrutmen'), 'sliders-horizontal', 'filter-rekrutmen*'],
            ['Kelola Tim',   route('tim.index'),          'users-round',      'my-teams*'],
            ['Susunan Portofolio', route('portofolio.index'), 'briefcase',   'susunan-portofolio*'],
            ['Pesan',        route('messages.index'),     'message-circle',   'messages*'],
            ['Notifikasi',   route('notifications'),      'bell',             'notifications*'],
        ];

        $user = [
            'name' => $u->name ?? 'Dzaky',
            'prodi' => $u->prodi ?? 'TRPL',
            'angkatan' => $u->angkatan ?? (isset($u->npm) ? '20'.substr($u->npm, 0, 2) : '2025'),
            'inisial' => strtoupper(mb_substr($u->name ?? 'D', 0, 1)),
        ];

        // Tim yang membuka rekrutmen (sesuai desain Figma filter-rekrutmen)
        $teams = [
            [
                'id' => 1,
                'nama' => 'Byte Builders',
                'lomba' => 'Hackathon AI',
                'kategori' => 'Hackathon',
                'skills' => ['Mobile', 'backend', 'UI/UX'],
                'terisi' => 3,
                'total' => 5,
                'anggota' => [
                    ['inisial' => 'A', 'warna' => '#FF7616'],
                    ['inisial' => 'C', 'warna' => '#22C55E'],
                    ['inisial' => 'D', 'warna' => '#14B8A6'],
                ],
            ],
            [
                'id' => 2,
                'nama' => 'Visioner Squad',
                'lomba' => 'UI/UX Design',
                'kategori' => 'UI/UX Design',
                'skills' => ['IoT', 'Frontend'],
                'terisi' => 3,
                'total' => 3,
                'anggota' => [
                    ['inisial' => 'D', 'warna' => '#14B8A6'],
                    ['inisial' => 'S', 'warna' => '#ED409D'],
                    ['inisial' => 'D', 'warna' => '#3B82F6'],
                ],
            ],
            [
                'id' => 3,
                'nama' => 'Byte Builders',
                'lomba' => 'Hackathon AI',
                'kategori' => 'Hackathon',
                'skills' => ['Mobile', 'backend', 'UI/UX'],
                'terisi' => 3,
                'total' => 5,
                'anggota' => [
                    ['inisial' => 'M', 'warna' => '#FF7616'],
                    ['inisial' => 'G', 'warna' => '#22C55E'],
                    ['inisial' => 'I', 'warna' => '#14B8A6'],
                ],
            ],
            [
                'id' => 4,
                'nama' => 'Visioner Squad',
                'lomba' => 'Backend',
                'kategori' => 'Hackathon',
                'skills' => ['IoT', 'Frontend'],
                'terisi' => 3,
                'total' => 3,
                'anggota' => [
                    ['inisial' => 'Z', 'warna' => '#14B8A6'],
                    ['inisial' => 'P', 'warna' => '#ED409D'],
                    ['inisial' => 'C', 'warna' => '#3B82F6'],
                ],
            ],
        ];

        foreach (['Byte Builders', 'Visioner Squad'] as $teamName) {
            Team::firstOrCreate(
                ['name' => $teamName],
                [
                    'owner_id' => $teamName === 'Byte Builders' ? $u->id : null,
                    'competition' => $teamName === 'Byte Builders' ? 'Hackathon AI' : 'UI/UX Design',
                    'capacity' => $teamName === 'Byte Builders' ? 5 : 3,
                    'members_count' => 3,
                    'skills' => $teamName === 'Byte Builders' ? ['Mobile', 'Backend', 'UI/UX'] : ['IoT', 'Frontend'],
                ]
            );
        }

        $teamIds = Team::whereIn('name', ['Byte Builders', 'Visioner Squad'])->pluck('id', 'name');
        $teams = array_map(function (array $team) use ($teamIds): array {
            $team['id'] = $teamIds->get($team['nama'], $team['id']);

            return $team;
        }, $teams);

        if ($search !== '') {
            $teams = array_values(array_filter($teams, function (array $team) use ($search): bool {
                $searchable = mb_strtolower(implode(' ', [
                    $team['nama'],
                    $team['lomba'],
                    $team['kategori'],
                    ...$team['skills'],
                ]));

                return str_contains($searchable, $search);
            }));
        }

        return view('mahasiswa.filter-rekrutmen', compact('menu', 'user', 'teams'));
    }
}
