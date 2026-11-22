<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortofolioController extends Controller
{
    /**
     * Menu sidebar yang digunakan di semua method.
     */
    private function buildMenu(): array
    {
        return [
            ['Beranda',             route('dashboard'),           'layout-dashboard', 'dashboard*'],
            ['Profil Saya',         route('profile'),             'user',             'profile*'],
            ['Cari Tim',            route('recruitment.index'),   'users',            '*find-teams*|*cari-tim*|*filter-rekrutmen*'],
            ['Filter Rekrutmen',    route('filter-rekrutmen'),   'sliders-horizontal', 'filter-rekrutmen*'],
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
                'id' => 1,
                'judul' => 'Aplikasi Skill Link',
                'tipe' => 'Proyek',
                'deskripsi' => 'Aplikasi mobile untuk menghubungkan mahasiswa dengan peluang lomba & tim.',
                'tags' => ['Flutter', 'Firebase', 'UI/UX'],
                'warna' => '#2563EB',
                'inisial' => 'A',
                'ditampilkan' => true,
                'urutan' => 1,
            ],
            [
                'id' => 2,
                'judul' => 'Juara 2 Gemastik 2025',
                'tipe' => 'Prestasi',
                'deskripsi' => 'Meraih Juara 2 kategori Pengembangan Perangkat Lunak tingkat nasional.',
                'tags' => ['Gemastik', 'UI/UX'],
                'warna' => '#F59E0B',
                'inisial' => 'J',
                'ditampilkan' => true,
                'urutan' => 2,
            ],
            [
                'id' => 3,
                'judul' => 'Sertifikat: Belajar Pengembangan Flutter',
                'tipe' => 'Sertifikat',
                'deskripsi' => 'Menyelesaikan kursus Dicoding dengan nilai sempurna.',
                'tags' => ['Flutter', 'Dicoding'],
                'warna' => '#16A34A',
                'inisial' => 'S',
                'ditampilkan' => true,
                'urutan' => 3,
            ],
            [
                'id' => 4,
                'judul' => 'Redesign Portal Informasi Kampus',
                'tipe' => 'Proyek',
                'deskripsi' => 'Proyek redesain UI/UX portal informasi kampus berbasis riset pengguna.',
                'tags' => ['Figma', 'Riset UX'],
                'warna' => '#8B5CF6',
                'inisial' => 'R',
                'ditampilkan' => true,
                'urutan' => 4,
            ],
            [
                'id' => 5,
                'judul' => 'Sistem Absensi Berbasis QR',
                'tipe' => 'Proyek',
                'deskripsi' => 'Aplikasi web absensi mahasiswa menggunakan QR Code dinamis.',
                'tags' => ['Laravel', 'MySQL'],
                'warna' => '#EC4899',
                'inisial' => 'S',
                'ditampilkan' => false,
                'urutan' => 5,
            ],
        ];
    }

    /**
     * Tampilkan portofolio yang sudah dipublikasikan.
     */
    public function publicIndex(Request $request): View
    {
        $u = $request->user();
        $portfolios = Portfolio::where('user_id', $u->id)
            ->where('status', 'approved')
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Portfolio $portfolio): array => $this->toViewData($portfolio));

        return view('mahasiswa.portofolio-publik', [
            'menu' => $this->buildMenu(),
            'user' => [
                'name' => $u->name,
                'prodi' => $u->prodi ?? 'TRPL',
                'angkatan' => $u->angkatan ?? substr((string) $u->npm, 0, 2),
                'inisial' => strtoupper(mb_substr($u->name, 0, 1)),
            ],
            'portfolios' => $portfolios,
        ]);
    }

    /**
     * Tampilkan halaman Susunan Portofolio.
     */
    public function index(Request $request)
    {
        $u = $request->user();
        $portfolios = Portfolio::where('user_id', $u->id)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Portfolio $portfolio): array => $this->toViewData($portfolio));

        return view('mahasiswa.susunan-portofolio', [
            'menu' => $this->buildMenu(),
            'user' => [
                'name' => $u->name,
                'prodi' => $u->prodi ?? 'TRPL',
                'angkatan' => $u->angkatan ?? substr((string) $u->npm, 0, 2),
                'inisial' => strtoupper(mb_substr($u->name, 0, 1)),
            ],
            'portfolios' => $portfolios->all(),
        ]);
    }

    /**
     * Simpan portofolio baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:200',
            'tipe' => 'required|in:Proyek,Prestasi,Sertifikat',
            'deskripsi' => 'nullable|string|max:500',
            'tags' => 'nullable|string|max:500',
            'ditampilkan' => 'nullable|boolean',
            'bukti' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $warnaMap = [
            'Proyek' => '#2563EB',
            'Prestasi' => '#F59E0B',
            'Sertifikat' => '#16A34A',
        ];
        $tags = array_values(array_filter(array_map('trim', explode(',', $request->tags ?? ''))));
        $evidencePath = $request->file('bukti')->store('portfolio-evidence', 'public');

        Portfolio::create([
            'user_id' => $request->user()->id,
            'title' => $request->judul,
            'type' => $request->tipe,
            'description' => $request->deskripsi,
            'tags' => $tags,
            'evidence_path' => $evidencePath,
            'status' => 'pending',
            'is_visible' => $request->boolean('ditampilkan'),
            'sort_order' => (Portfolio::where('user_id', $request->user()->id)->max('sort_order') ?? 0) + 1,
        ]);

        return redirect()->route('portofolio.index')
            ->with('success', 'Portofolio berhasil dikirim dan menunggu validasi admin.');
    }

    /**
     * Update portofolio yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:200',
            'tipe' => 'required|in:Proyek,Prestasi,Sertifikat',
            'deskripsi' => 'nullable|string|max:500',
            'tags' => 'nullable|string|max:500',
            'ditampilkan' => 'nullable|boolean',
            'bukti' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $portfolio = Portfolio::where('user_id', $request->user()->id)->findOrFail($id);

        if ($request->hasFile('bukti')) {
            Storage::disk('public')->delete($portfolio->evidence_path);
            $evidencePath = $request->file('bukti')->store('portfolio-evidence', 'public');
            $portfolio->evidence_path = $evidencePath;
        }

        $portfolio->title = $request->judul;
        $portfolio->type = $request->tipe;
        $portfolio->description = $request->deskripsi;
        $portfolio->tags = array_values(array_filter(array_map('trim', explode(',', $request->tags ?? ''))));
        $portfolio->is_visible = $request->boolean('ditampilkan');
        $portfolio->status = 'pending';
        $portfolio->save();

        return redirect()->route('portofolio.index')
            ->with('success', 'Perubahan portofolio dikirim kembali untuk validasi admin.');
    }

    /**
     * Hapus portofolio.
     */
    public function destroy(Request $request, $id)
    {
        $portfolio = Portfolio::where('user_id', $request->user()->id)->findOrFail($id);
        Storage::disk('public')->delete($portfolio->evidence_path);
        $portfolio->delete();

        return redirect()->route('portofolio.index')
            ->with('success', 'Portofolio berhasil dihapus.');
    }

    /**
     * Toggle visibilitas portofolio (ditampilkan / draft).
     */
    public function toggleVisibility(Request $request, $id)
    {
        $portfolio = Portfolio::where('user_id', $request->user()->id)->findOrFail($id);
        $portfolio->is_visible = ! $portfolio->is_visible;
        $portfolio->save();

        if ($request->expectsJson()) {
            return response()->json(['ditampilkan' => $portfolio->is_visible]);
        }

        return redirect()->route('portofolio.index');
    }

    /**
     * Simpan ulang urutan portofolio (drag & drop).
     */
    public function reorder(Request $request)
    {
        $order = $request->validate(['order' => 'required|array'])['order'];
        $portfolios = Portfolio::where('user_id', $request->user()->id)
            ->whereIn('id', $order)
            ->get()
            ->keyBy('id');

        foreach ($order as $position => $id) {
            if ($portfolio = $portfolios->get((int) $id)) {
                $portfolio->update(['sort_order' => $position + 1]);
            }
        }

        return response()->json(['ok' => true]);
    }

    private function toViewData(Portfolio $portfolio): array
    {
        $colors = [
            'Proyek' => '#2563EB',
            'Prestasi' => '#F59E0B',
            'Sertifikat' => '#16A34A',
        ];

        return [
            'id' => $portfolio->id,
            'judul' => $portfolio->title,
            'tipe' => $portfolio->type,
            'deskripsi' => $portfolio->description ?? '',
            'tags' => $portfolio->tags ?? [],
            'warna' => $colors[$portfolio->type],
            'inisial' => strtoupper(mb_substr($portfolio->title, 0, 1)),
            'ditampilkan' => $portfolio->is_visible,
            'urutan' => $portfolio->sort_order,
            'status' => $portfolio->status,
            'bukti_url' => Storage::url($portfolio->evidence_path),
        ];
    }
}
