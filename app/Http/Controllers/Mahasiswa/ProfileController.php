<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $u = $request->user();
        $menu = [
            ['Dashboard', route('dashboard'), 'layout-dashboard', 'dashboard'],
            ['Profile', route('profile'), 'user', 'profile'],
            ['Find Teams', route('recruitment.index'), 'users', 'recruitment*'],

            ['Messages', route('messages.index'), 'message-circle', 'messages*'],
            ['Notifications', route('notifications'), 'bell', 'notifications*'],
        ];
        return view('mahasiswa.profile', [
            'menu' => $menu,
            'user' => [
                'name'     => $u->name,
                'npm'      => $u->npm,
                'jurusan'  => $u->jurusan,
                'prodi'    => $u->prodi ?? 'TRPL',
                'email'    => $u->email,
                'angkatan' => $u->angkatan,
                'bio'      => $u->bio,
                'foto'     => $u->foto ? Storage::url($u->foto) : null,
                'inisial'  => strtoupper(mb_substr($u->name ?? 'U', 0, 1)),
            ],
            'stats' => ['profil' => $this->hitungKelengkapan($u)],
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
    public function edit(Request $request)
    {
        $u = $request->user();

        $user = [
            'name'     => $u->name,
            'npm'      => $u->npm,
            'jurusan'  => $u->jurusan,
            'prodi'    => $u->prodi ?? 'TRPL',
            'email'    => $u->email,
            'angkatan' => $u->angkatan,
            'bio'      => $u->bio,
            'foto'     => $u->foto ? Storage::url($u->foto) : null,
            'inisial'  => strtoupper(mb_substr($u->name ?? 'U', 0, 1)),
        ];

        $stats = ['profil' => $this->hitungKelengkapan($u)];

        return view('profil', compact('user', 'stats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $u = $request->user();

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'npm'      => ['required', 'digits_between:6,20', Rule::unique('users', 'npm')->ignore($u->id)],
            'jurusan'  => ['required', 'string', 'max:100'],
            'prodi'    => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($u->id)],
            'angkatan' => ['required', 'digits:4', 'integer', 'between:2000,' . (now()->year + 1)],
            'bio'      => ['required', 'string', 'max:300'],
            'foto'     => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'required'       => ':attribute wajib diisi.',
            'email'          => 'Format email tidak valid.',
            'unique'         => ':attribute sudah digunakan.',
            'digits'         => ':attribute harus :digits digit.',
            'digits_between' => ':attribute harus berupa angka (:min-:max digit).',
            'max.string'     => ':attribute maksimal :max karakter.',
            'mimes'          => 'Format foto harus JPG atau PNG.',
            'foto.max'       => 'Ukuran foto maksimal 2 MB.',
        ], [
            'name' => 'Nama lengkap',
            'npm' => 'NPM',
            'jurusan' => 'Jurusan',
            'prodi' => 'Program studi',
            'email' => 'Email',
            'angkatan' => 'Angkatan',
            'bio' => 'Bio singkat',
        ]);

        // Foto: ganti / hapus
        if ($request->hasFile('foto')) {
            if ($u->foto) {
                Storage::disk('public')->delete($u->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-profil', 'public');
        } elseif ($request->boolean('hapus_foto')) {
            if ($u->foto) {
                Storage::disk('public')->delete($u->foto);
            }
            $data['foto'] = null;
        } else {
            unset($data['foto']);
        }

        $u->update($data);

        return redirect()->route('profil.edit')->with('success', true);
    }

    private function hitungKelengkapan($u): int
    {
        $isian = ['name', 'npm', 'jurusan', 'prodi', 'email', 'angkatan', 'bio', 'foto'];
        $terisi = collect($isian)->filter(fn ($f) => filled($u->{$f}))->count();

        return (int) round($terisi / count($isian) * 100);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
