<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Mahasiswa\CariTimController;
use App\Http\Controllers\Mahasiswa\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Redirect root ke login ────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ── Halaman & proses auth (hanya untuk tamu / belum login) ───────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');

    // Proses masuk (email + password)
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Proses daftar (username, email, npm, password)
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// ── Logout (butuh sesi yang aktif) ───────────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ── Halaman yang dilindungi (login + role mahasiswa) ─────────────────────────
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard',         [DashboardController::class, 'dashmahasiswa'])->name('dashboard');
    Route::get('/profile',           [ProfileController::class, 'index'])->name('profile');
    Route::get('/find-competitions', [ProfileController::class, 'index'])->name('competition.index');
    Route::get('/find-teams',        [CariTimController::class, 'index'])->name('recruitment.index');
    Route::get('/cari-tim',          [CariTimController::class, 'index'])->name('cari-tim');
    Route::get('/filter-rekrutmen',  [CariTimController::class, 'filterRekrutmen'])->name('filter-rekrutmen');
    Route::get('/my-teams',          [ProfileController::class, 'index'])->name('tim.index');
    Route::get('/messages',          [ProfileController::class, 'index'])->name('messages.index');
    Route::get('/notifications',     [ProfileController::class, 'index'])->name('notifications');
});
