<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class CariTimController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        $search = mb_strtolower(trim((string) $request->query('q', '')));
        $teamNames = [
            'Tim Garuda Code',
            'Tim PixelForce',
            'Tim Nusa Backend',
            'Tim IoT Lampung',
        ];

        foreach ($teamNames as $index => $teamName) {
            $team = Team::firstOrCreate(
                ['name' => $teamName],
                [
                    'owner_id' => $teamName === 'Tim Garuda Code' ? $u->id : null,
                    'capacity' => 4,
                    'members_count' => $index === 1 ? 3 : 2,
                    'competition' => match ($index) {
                        0 => 'Gemastik 2026',
                        1 => 'Kompetisi UI/UX Mahasiswa 2026',
                        2 => 'Hackathon Nasional Kampus Digital',
                        default => 'Innovation Challenge 2026',
                    },
                ]
            );
        }

        $menu = [
            ['Beranda', route('dashboard'), 'layout-dashboard', 'dashboard*'],
            ['Profil Saya', route('profile'), 'user', 'profile*'],
            ['Cari Tim', route('recruitment.index'), 'users', '*find-teams*|*cari-tim*'],
            ['Kelola Tim', route('tim.index'), 'users-round', 'my-teams*'],
            ['Pusat Lomba', route('competition.index'), 'trophy', 'find-competitions*'],
            ['Pesan', route('messages.index'), 'message-circle', 'messages*'],
            ['Notifikasi', route('notifications'), 'bell', 'notifications*'],
        ];

        $user = [
            'name' => $u->name ?? 'Dzaky',
            'prodi' => $u->prodi ?? 'TRPL',
            'angkatan' => $u->angkatan ?? (isset($u->npm) ? '20'.substr($u->npm, 0, 2) : '2025'),
            'inisial' => strtoupper(mb_substr($u->name ?? 'D', 0, 1)),
        ];

        // Data Tim yang mencari anggota (sesuai desain Figma)
        $teams = [
            [
                'id' => 1,
                'nama' => 'Tim Garuda Code',
                'inisial' => 'G',
                'warna' => '#FF7F50',
                'lomba' => 'Gemastik 2026 · Pengembangan Perangkat Lunak',
                'kategori' => 'Software Development',
                'butuh_teks' => 'Butuh 2 anggota',
                'deskripsi' => 'Mencari developer mobile dan UI designer untuk membangun aplikasi layanan publik berbasis Flutter.',
                'skills' => ['Flutter', 'UI/UX', 'Mobile'],
                'terisi' => 2,
                'total' => 4,
                'deadline' => '30 Nov 2026',
                'anggota' => [
                    ['inisial' => 'S', 'warna' => '#14B8A6'],
                    ['inisial' => 'R', 'warna' => '#7C5CFC'],
                ],
            ],
            [
                'id' => 2,
                'nama' => 'Tim PixelForce',
                'inisial' => 'P',
                'warna' => '#0369A1',
                'lomba' => 'Kompetisi UI/UX Mahasiswa 2026',
                'kategori' => 'UI/UX Design',
                'butuh_teks' => 'Butuh 2 anggota',
                'deskripsi' => 'Butuh UI/UX researcher dan frontend developer untuk merancang ulang portal informasi kampus.',
                'skills' => ['UI/UX', 'Frontend'],
                'terisi' => 3,
                'total' => 5,
                'deadline' => '20 Des 2026',
                'anggota' => [
                    ['inisial' => 'D', 'warna' => '#FF7F50'],
                    ['inisial' => 'C', 'warna' => '#14B8A6'],
                    ['inisial' => 'O', 'warna' => '#7C5CFC'],
                ],
            ],
            [
                'id' => 3,
                'nama' => 'Tim Nusa Backend',
                'inisial' => 'N',
                'warna' => '#14B8A6',
                'lomba' => 'Hackathon Nasional Kampus Digital',
                'kategori' => 'Hackathon',
                'butuh_teks' => 'Butuh 2 anggota',
                'deskripsi' => 'Mencari backend developer yang menguasai Laravel atau Node.js untuk sistem manajemen UMKM.',
                'skills' => ['Backend', 'Laravel'],
                'terisi' => 2,
                'total' => 4,
                'deadline' => '12 Des 2026',
                'anggota' => [
                    ['inisial' => 'L', 'warna' => '#0369A1'],
                    ['inisial' => 'G', 'warna' => '#FF7F50'],
                ],
            ],
            [
                'id' => 4,
                'nama' => 'Tim IoT Lampung',
                'inisial' => 'I',
                'warna' => '#7C5CFC',
                'lomba' => 'Innovation Challenge 2026 · Smart Farming',
                'kategori' => 'IoT',
                'butuh_teks' => 'Butuh 1 anggota',
                'deskripsi' => 'Butuh anggota yang paham mikrokontroler dan pemrograman sensor untuk proyek pertanian pintar.',
                'skills' => ['IoT', 'Mobile'],
                'terisi' => 3,
                'total' => 4,
                'deadline' => '5 Jan 2027',
                'anggota' => [
                    ['inisial' => 'O', 'warna' => '#14B8A6'],
                    ['inisial' => 'T', 'warna' => '#FF7F50'],
                    ['inisial' => 'S', 'warna' => '#0369A1'],
                ],
            ],
        ];

        $teamIds = Team::whereIn('name', collect($teams)->pluck('nama'))->pluck('id', 'name');
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
                    $team['deskripsi'],
                    ...$team['skills'],
                ]));

                return str_contains($searchable, $search);
            }));
        }

        // Data Tim Saya (side card)
        $myTeam = [
            'nama' => 'Tim Garuda Code',
            'inisial' => 'G',
            'warna' => '#FF7F50',
            'role' => 'Ketua',
            'anggota' => 3,
            'lomba' => 'Gemastik 2026',
            'terisi' => 3,
            'total' => 4,
            'total_tim' => 2,
        ];

        return view('mahasiswa.cari-tim', compact('menu', 'user', 'teams', 'myTeam'));
    }

    public function filterRekrutmen(Request $request)
    {
        $u = $request->user();

        $menu = [
            ['Beranda',      route('dashboard'),          'layout-dashboard', 'dashboard*'],
            ['Profil Saya',  route('profile'),            'user',             'profile*'],
            ['Cari Tim',     route('recruitment.index'),  'users',            '*find-teams*|*cari-tim*|*filter-rekrutmen*'],
            ['Kelola Tim',   route('tim.index'),          'users-round',      'my-teams*'],
            ['Pusat Lomba',  route('competition.index'),  'trophy',           'find-competitions*'],
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

        return view('mahasiswa.filter-rekrutmen', compact('menu', 'user', 'teams'));
    }
}
