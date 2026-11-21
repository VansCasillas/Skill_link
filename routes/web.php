<?php

use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Mahasiswa\CariTimController;
use App\Http\Controllers\Mahasiswa\PortofolioController;
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

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/kelola-pusat-informasi', [CompetitionController::class, 'index'])->name('competition.manage.index');
    Route::post('/kelola-pusat-informasi', [CompetitionController::class, 'store'])->name('competition.manage.store');
    Route::put('/kelola-pusat-informasi/{competition}', [CompetitionController::class, 'update'])->name('competition.manage.update');
    Route::delete('/kelola-pusat-informasi/{competition}', [CompetitionController::class, 'destroy'])->name('competition.manage.destroy');
    Route::post('/kelola-pusat-informasi/{competition}/publish', [CompetitionController::class, 'publish'])->name('competition.manage.publish');
});

// ── Halaman yang dilindungi (login + role mahasiswa) ─────────────────────────
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashmahasiswa'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/find-competitions', [CompetitionController::class, 'publicIndex'])->name('competition.index');
    Route::get('/lomba', fn () => redirect()->route('competition.index'));
    Route::get('/find-teams', [CariTimController::class, 'index'])->name('recruitment.index');
    Route::get('/cari-tim', [CariTimController::class, 'index'])->name('cari-tim');
    Route::get('/filter-rekrutmen', [CariTimController::class, 'filterRekrutmen'])->name('filter-rekrutmen');
    Route::get('/my-teams', [ProfileController::class, 'index'])->name('tim.index');
    Route::get('/messages', [ProfileController::class, 'index'])->name('messages.index');
    Route::get('/notifications', [ProfileController::class, 'index'])->name('notifications');

    // ── Susunan Portofolio ────────────────────────────────────────────────────
    Route::get('/susunan-portofolio', [PortofolioController::class, 'index'])->name('portofolio.index');
    Route::post('/susunan-portofolio', [PortofolioController::class, 'store'])->name('portofolio.store');
    Route::post('/susunan-portofolio/{id}/toggle', [PortofolioController::class, 'toggleVisibility'])->name('portofolio.toggle');
    Route::post('/susunan-portofolio/reorder', [PortofolioController::class, 'reorder'])->name('portofolio.reorder');
    Route::put('/susunan-portofolio/{id}', [PortofolioController::class, 'update'])->name('portofolio.update');
    Route::delete('/susunan-portofolio/{id}', [PortofolioController::class, 'destroy'])->name('portofolio.destroy');
});
