<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashmahasiswa(Request $request)
    {
        $u = $request->user();
        $menu = [
            ['Dashboard', route('dashboard'), 'layout-dashboard', 'dashboard'],
            ['Profile', route('profile'), 'user', 'profile'],
            ['Find Teams', route('recruitment.index'), 'users', ['find-teams*', 'cari-tim*', 'recruitment*']],
            ['Pusat Lomba', route('competition.index'), 'trophy', 'find-competitions*'],
            ['Susunan Portofolio', route('portofolio.index'), 'briefcase', 'susunan-portofolio*'],
            ['Messages', route('messages.index'), 'message-circle', 'messages*'],
            ['Notifications', route('notifications'), 'bell', 'notifications*'],
        ];

        $competitions = Competition::query()
            ->where('status', 'active')
            ->whereDate('deadline', '>=', today())
            ->orderBy('deadline')
            ->limit(3)
            ->get();
        $profileFields = ['name', 'npm', 'jurusan', 'prodi', 'email', 'angkatan', 'bio', 'foto'];
        $profilePercent = (int) round(
            collect($profileFields)->filter(fn (string $field): bool => filled($u->{$field}))->count()
            / count($profileFields) * 100
        );

        return view('mahasiswa.dashboard', [
            'menu' => $menu,
            'user' => [
                'name' => $u->name,
                'prodi' => $u->prodi ?? 'TRPL',
                'angkatan' => $u->angkatan ?? substr((string) $u->npm, 0, 2),
                'inisial' => strtoupper(substr($u->name, 0, 1)),
            ],

            // Ganti dengan query ke database, misalnya $u->teams()->count()
            'stats' => [
                'lomba_aktif' => $competitions->count(),
                'tim_saya' => 0,
                'undangan' => 0,
                'profil' => $profilePercent,
            ],

            'lombas' => $competitions->map(fn (Competition $competition): array => [
                'nama' => $competition->name,
                'desc' => $competition->organizer.' · '.$competition->level,
                'warna' => '#0A87D9',
                'url' => route('competition.index'),
            ])->all(),

            'tims' => [
                ['nama' => 'Tim Garuda Code', 'lomba' => 'Gemastik 2026',    'butuh' => 2, 'warna' => '#FF7F50', 'url' => url('/tim/garuda-code')],
                ['nama' => 'Tim PixelForce',  'lomba' => 'Kompetisi UI/UX',  'butuh' => 2, 'warna' => '#0369A1', 'url' => url('/tim/pixelforce')],
            ],

            'deadlines' => [
                ['tgl' => '15', 'bln' => 'DES', 'nama' => 'Gemastik 2026'],
                ['tgl' => '22', 'bln' => 'DES', 'nama' => 'Hackathon Kampus Digital'],
                ['tgl' => '05', 'bln' => 'JAN', 'nama' => 'Kompetisi UI/UX'],
            ],

            'notifs' => [
                'Tim Garuda Code membuka rekrutmen baru.',
                'Profil Anda baru 70% lengkap.',
                'Lomba baru ditambahkan: Kompetisi UI/UX Mahasiswa.',
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
