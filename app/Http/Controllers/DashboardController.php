<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashmahasiswa(Request $request)
    {
        $u = $request->user();
        $menu = [
            ['Beranda', route('dashboard'), 'layout-dashboard', 'dashboard*'],
            ['Profil Saya', route('profile'), 'user', 'profile*|profil*'],
            ['Pencarian', route('recruitment.index'), 'users', ['find-teams*', 'cari-tim*', 'recruitment*']],
            ['Filter Rekrutmen', route('filter-rekrutmen'), 'sliders-horizontal', 'filter-rekrutmen*'],
            ['Kelola Tim', route('tim.index'), 'users-round', 'my-teams*'],
            ['Susunan Portofolio', route('portofolio.index'), 'briefcase', 'susunan-portofolio*'],
            ['Pesan', route('messages.index'), 'message-circle', 'messages*'],
            ['Notifikasi', route('notifications'), 'bell', 'notifications*'],
        ];

        $competitions = Competition::query()
            ->where('status', 'active')
            ->whereDate('deadline', '>=', today())
            ->orderBy('deadline')
            ->limit(3)
            ->get();
        $profilePercent = $u->profileCompletionPercent();
        $recentNotifications = $u->notifications()
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (DatabaseNotification $notification): string => (string) ($notification->data['title'] ?? 'Notifikasi baru'))
            ->all();

        return view('mahasiswa.dashboard', [
            'menu' => $menu,
            'user' => [
                'name' => $u->name,
                'prodi' => $u->prodi ?? 'TRPL',
                'angkatan' => $u->angkatan ?? substr((string) $u->npm, 0, 2),
                'inisial' => strtoupper(substr($u->name, 0, 1)),
                'foto' => $u->foto ? Storage::disk('public')->url($u->foto) : null,
            ],

            // Ganti dengan query ke database, misalnya $u->teams()->count()
            'stats' => [
                'lomba_aktif' => $competitions->count(),
                'tim_saya' => Team::where('owner_id', $u->id)
                    ->orWhereHas('joinRequests', fn ($query) => $query
                        ->where('user_id', $u->id)
                        ->where('status', 'approved'))
                    ->count(),
                'undangan' => TeamJoinRequest::where('user_id', $u->id)->where('status', 'pending')->count(),
                'profil' => $profilePercent,
            ],

            'lombas' => $competitions->map(fn (Competition $competition): array => [
                'nama' => $competition->name,
                'desc' => $competition->organizer.' · '.$competition->level,
                'warna' => '#0A87D9',
                'url' => route('recruitment.index', ['q' => $competition->name]),
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

            'notifs' => $recentNotifications,
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
