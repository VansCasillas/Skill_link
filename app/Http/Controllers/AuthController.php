<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    // ── Tampilkan halaman login/register ─────────────────────────────────────
    public function index()
    {
        return view('auth.login');
    }

    // ── Proses Masuk (login via email) ───────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()
            ->withErrors([
                'email' => 'Email atau kata sandi yang Anda masukkan salah.',
            ], 'loginBag')
            ->withInput($request->only('email'))
            ->with('_tab', 'login');
    }

    // ── Proses Daftar (register) ──────────────────────────────────────────────
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'npm'      => ['required', 'string', 'max:20', 'unique:users,npm'],
            'password' => ['required', 'string', PasswordRule::min(8)],
        ], [
            'name.required'     => 'Username wajib diisi.',
            'name.max'          => 'Username maksimal 255 karakter.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar.',
            'npm.required'      => 'NPM wajib diisi.',
            'npm.unique'        => 'NPM sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal 8 karakter.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'registerBag')
                ->withInput($request->except('password'));
        }

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'npm'      => $request->npm,
            'password' => Hash::make($request->password),
            'role'     => 'mahasiswa',
        ]);

        // Jangan auto-login — redirect ke halaman login dengan notifikasi sukses
        return redirect()->route('login')->with('registerOk', true);
    }

    // ── Logout ────────────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
