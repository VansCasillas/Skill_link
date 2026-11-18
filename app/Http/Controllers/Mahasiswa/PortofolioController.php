<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PortofolioController extends Controller
{
    /**
     * Menu sidebar yang digunakan di semua method.
     */
    private function buildMenu(): array
    {
        return [
            ['Dashboard',           route('dashboard'),           'layout-dashboard', 'dashboard*'],
            ['Profil Saya',         route('profile'),             'user',             'profile*'],
            ['Cari Tim',            route('recruitment.index'),   'users',            '*find-teams*|*cari-tim*|*filter-rekrutmen*'],
            ['Kelola Tim',          route('tim.index'),           'users-round',      'my-teams*'],
            ['Pusat Lomba',         route('competition.index'),   'trophy',           'find-competitions*'],
            ['Susunan Portofolio',  route('portofolio.index'),    'briefcase',        'susunan-portofolio*'],
            ['Pesan',               route('messages.index'),      'message-circle',   'messages*'],
            ['Notifikasi',          route('notifications'),       'bell',             'notifications*'],
        ];
    }

    /**
     * Default portofolio data (session-backed for demo).
     */
    private function defaultPortfolios(): array
    {
        return [
            [
                'id'          => 1,
                'judul'       => 'Aplikasi Skill Link',
                'tipe'        => 'Proyek',
                'deskripsi'   => 'Aplikasi mobile untuk menghubungkan mahasiswa dengan peluang lomba & tim.',
                'tags'        => ['Flutter', 'Firebase', 'UI/UX'],
                'warna'       => '#2563EB',
                'inisial'     => 'A',
                'ditampilkan' => true,
                'urutan'      => 1,
            ],
            [
                'id'          => 2,
                'judul'       => 'Juara 2 Gemastik 2025',
                'tipe'        => 'Prestasi',
                'deskripsi'   => 'Meraih Juara 2 kategori Pengembangan Perangkat Lunak tingkat nasional.',
                'tags'        => ['Gemastik', 'UI/UX'],
                'warna'       => '#F59E0B',
                'inisial'     => 'J',
                'ditampilkan' => true,
                'urutan'      => 2,
            ],
            [
                'id'          => 3,
                'judul'       => 'Sertifikat: Belajar Pengembangan Flutter',
                'tipe'        => 'Sertifikat',
                'deskripsi'   => 'Menyelesaikan kursus Dicoding dengan nilai sempurna.',
                'tags'        => ['Flutter', 'Dicoding'],
                'warna'       => '#16A34A',
                'inisial'     => 'S',
                'ditampilkan' => true,
                'urutan'      => 3,
            ],
            [
                'id'          => 4,
                'judul'       => 'Redesign Portal Informasi Kampus',
                'tipe'        => 'Proyek',
                'deskripsi'   => 'Proyek redesain UI/UX portal informasi kampus berbasis riset pengguna.',
                'tags'        => ['Figma', 'Riset UX'],
                'warna'       => '#8B5CF6',
                'inisial'     => 'R',
                'ditampilkan' => true,
                'urutan'      => 4,
            ],
            [
                'id'          => 5,
                'judul'       => 'Sistem Absensi Berbasis QR',
                'tipe'        => 'Proyek',
                'deskripsi'   => 'Aplikasi web absensi mahasiswa menggunakan QR Code dinamis.',
                'tags'        => ['Laravel', 'MySQL'],
                'warna'       => '#EC4899',
                'inisial'     => 'S',
                'ditampilkan' => false,
                'urutan'      => 5,
            ],
        ];
    }

    /**
     * Tampilkan halaman Susunan Portofolio.
     */
    public function index(Request $request)
    {
        $u = $request->user();

        $user = [
            'name'     => $u->name     ?? 'Dzaky',
            'prodi'    => $u->prodi    ?? 'TRPL',
            'angkatan' => $u->angkatan ?? (isset($u->npm) ? '20' . substr($u->npm, 0, 2) : '2025'),
            'inisial'  => strtoupper(mb_substr($u->name ?? 'D', 0, 1)),
        ];

        // Gunakan session untuk menyimpan data portofolio (demo tanpa DB)
        if (! session()->has('portfolios')) {
            session(['portfolios' => $this->defaultPortfolios()]);
        }

        $portfolios = collect(session('portfolios'))->sortBy('urutan')->values()->all();

        return view('mahasiswa.susunan-portofolio', [
            'menu'       => $this->buildMenu(),
            'user'       => $user,
            'portfolios' => $portfolios,
        ]);
    }

    /**
     * Simpan portofolio baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'    => 'required|string|max:200',
            'tipe'     => 'required|in:Proyek,Prestasi,Sertifikat',
            'deskripsi'=> 'nullable|string|max:500',
        ]);

        $portfolios = session('portfolios', $this->defaultPortfolios());

        $warnaMap = [
            'Proyek'     => '#2563EB',
            'Prestasi'   => '#F59E0B',
            'Sertifikat' => '#16A34A',
        ];

        $newId = collect($portfolios)->max('id') + 1;
        $portfolios[] = [
            'id'          => $newId,
            'judul'       => $request->judul,
            'tipe'        => $request->tipe,
            'deskripsi'   => $request->deskripsi ?? '',
            'tags'        => array_filter(array_map('trim', explode(',', $request->tags ?? ''))),
            'warna'       => $warnaMap[$request->tipe],
            'inisial'     => strtoupper(mb_substr($request->judul, 0, 1)),
            'ditampilkan' => $request->boolean('ditampilkan'),
            'urutan'      => count($portfolios) + 1,
        ];

        session(['portfolios' => $portfolios]);

        return redirect()->route('portofolio.index')
            ->with('success', 'Portofolio berhasil ditambahkan!');
    }

    /**
     * Update portofolio yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul'    => 'required|string|max:200',
            'tipe'     => 'required|in:Proyek,Prestasi,Sertifikat',
            'deskripsi'=> 'nullable|string|max:500',
        ]);

        $portfolios = session('portfolios', $this->defaultPortfolios());
        $warnaMap   = [
            'Proyek'     => '#2563EB',
            'Prestasi'   => '#F59E0B',
            'Sertifikat' => '#16A34A',
        ];

        $portfolios = array_map(function ($p) use ($id, $request, $warnaMap) {
            if ($p['id'] == $id) {
                $p['judul']       = $request->judul;
                $p['tipe']        = $request->tipe;
                $p['deskripsi']   = $request->deskripsi ?? '';
                $p['tags']        = array_values(array_filter(array_map('trim', explode(',', $request->tags ?? ''))));
                $p['warna']       = $warnaMap[$request->tipe];
                $p['inisial']     = strtoupper(mb_substr($request->judul, 0, 1));
                $p['ditampilkan'] = $request->boolean('ditampilkan');
            }
            return $p;
        }, $portfolios);

        session(['portfolios' => $portfolios]);

        return redirect()->route('portofolio.index')
            ->with('success', 'Portofolio berhasil diperbarui!');
    }

    /**
     * Hapus portofolio.
     */
    public function destroy($id)
    {
        $portfolios = session('portfolios', $this->defaultPortfolios());
        $portfolios = array_values(array_filter($portfolios, fn($p) => $p['id'] != $id));
        session(['portfolios' => $portfolios]);

        return redirect()->route('portofolio.index')
            ->with('success', 'Portofolio berhasil dihapus.');
    }

    /**
     * Toggle visibilitas portofolio (ditampilkan / draft).
     */
    public function toggleVisibility(Request $request, $id)
    {
        $portfolios = session('portfolios', $this->defaultPortfolios());
        $portfolios = array_map(function ($p) use ($id) {
            if ($p['id'] == $id) {
                $p['ditampilkan'] = ! $p['ditampilkan'];
            }
            return $p;
        }, $portfolios);

        session(['portfolios' => $portfolios]);

        if ($request->expectsJson()) {
            $p = collect($portfolios)->firstWhere('id', $id);
            return response()->json(['ditampilkan' => $p['ditampilkan']]);
        }

        return redirect()->route('portofolio.index');
    }

    /**
     * Simpan ulang urutan portofolio (drag & drop).
     */
    public function reorder(Request $request)
    {
        $order      = $request->input('order', []);
        $portfolios = session('portfolios', $this->defaultPortfolios());

        $portfolios = array_map(function ($p) use ($order) {
            $pos = array_search($p['id'], $order);
            if ($pos !== false) {
                $p['urutan'] = $pos + 1;
            }
            return $p;
        }, $portfolios);

        usort($portfolios, fn($a, $b) => $a['urutan'] <=> $b['urutan']);
        session(['portfolios' => $portfolios]);

        return response()->json(['ok' => true]);
    }
}
