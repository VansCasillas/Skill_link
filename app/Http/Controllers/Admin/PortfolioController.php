<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'pending');
        if (! in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = 'pending';
        }

        $portfolios = Portfolio::with('user')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('mahasiswa.kelola-validasi-portofolio', [
            'menu' => [
                ['Dashboard', route('dashboard'), 'layout-dashboard', 'dashboard*'],
                ['Profil Saya', route('profile'), 'user', 'profile*|profil*'],
                ['Pencarian', route('recruitment.index'), 'users', ['find-teams*', 'cari-tim*', 'recruitment*']],
                ['Filter Rekrutmen', route('filter-rekrutmen'), 'sliders-horizontal', 'filter-rekrutmen*'],
                ['Kelola Tim', route('tim.index'), 'users-round', 'my-teams*'],
                ['Susunan Portofolio', route('portofolio.index'), 'briefcase', 'susunan-portofolio*'],
                ['Kelola Pusat Informasi Lomba', route('competition.manage.index'), 'trophy', 'admin/kelola-pusat-informasi*'],
                ['Validasi Nilai & Sertifikat', route('portfolio.validation.index'), 'badge-check', 'admin/validasi-portofolio*'],
            ],
            'user' => [
                'name' => $request->user()->name,
                'prodi' => 'Admin',
                'angkatan' => 'Skill Link',
                'subline' => 'Admin · Skill Link',
                'inisial' => mb_strtoupper(mb_substr($request->user()->name, 0, 1)),
            ],
            'portfolios' => $portfolios,
            'status' => $status,
        ]);
    }

    public function approve(Portfolio $portfolio): RedirectResponse
    {
        $portfolio->update(['status' => 'approved']);

        return back()->with('success', 'Portofolio berhasil divalidasi.');
    }

    public function reject(Portfolio $portfolio): RedirectResponse
    {
        $portfolio->update(['status' => 'rejected', 'is_visible' => false]);

        return back()->with('success', 'Portofolio ditolak dan disembunyikan.');
    }
}
