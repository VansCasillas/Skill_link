<?php

use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Mahasiswa\CariTimController;
use App\Http\Controllers\Mahasiswa\MahasiswaPageController;
use App\Http\Controllers\Mahasiswa\PortofolioController;
use App\Http\Controllers\Mahasiswa\ProfileController;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Notifications\TeamActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Arahkan pengguna berdasarkan sesi dan peran ──────────────────────────────
Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route(
        Auth::user()->role === 'admin'
            ? 'competition.manage.index'
            : 'dashboard'
    );
});

// ── Halaman & proses auth (hanya untuk tamu / belum login) ───────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');

    // Proses masuk (email + password)
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Proses daftar (username, email, npm, password)
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::get('/forgot-password', [AuthController::class, 'requestPasswordReset'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showPasswordResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
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
    Route::get('/validasi-portofolio', [AdminPortfolioController::class, 'index'])->name('portfolio.validation.index');
    Route::post('/validasi-portofolio/{portfolio}/approve', [AdminPortfolioController::class, 'approve'])->name('portfolio.validation.approve');
    Route::post('/validasi-portofolio/{portfolio}/reject', [AdminPortfolioController::class, 'reject'])->name('portfolio.validation.reject');
});

// ── Halaman yang dilindungi (login + role mahasiswa) ─────────────────────────
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashmahasiswa'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil');
    Route::get('/profil/edit', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::match(['put', 'post'], '/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'changePassword'])->name('password.change');
    Route::get('/find-competitions', fn (Request $request) => redirect()->route('recruitment.index', array_filter(['q' => $request->query('q')])))->name('competition.index');
    Route::get('/lomba', fn (Request $request) => redirect()->route('recruitment.index', array_filter(['q' => $request->query('q')])));
    Route::get('/find-teams', [CariTimController::class, 'index'])->name('recruitment.index');
    Route::get('/cari-tim', [CariTimController::class, 'index'])->name('cari-tim');
    Route::get('/filter-rekrutmen', [CariTimController::class, 'filterRekrutmen'])->name('filter-rekrutmen');
    Route::get('/my-teams', [MahasiswaPageController::class, 'teams'])->name('tim.index');
    Route::post('/teams', [MahasiswaPageController::class, 'createTeam'])->name('teams.store');
    Route::get('/teams/{team}/detail', [MahasiswaPageController::class, 'teamDetail'])->name('teams.detail');
    Route::post('/teams/{team}/sync-stats', [MahasiswaPageController::class, 'syncStats'])->name('teams.sync-stats');
    Route::put('/teams/{team}', [MahasiswaPageController::class, 'updateTeam'])->name('teams.update');
    Route::delete('/teams/{team}/members/{joinRequest}', [MahasiswaPageController::class, 'removeMember'])->name('teams.members.remove');
    Route::post('/teams/{team}/join', function (Request $request, Team $team) {
        $user = $request->user();
        if ($team->owner_id === $user->id) {
            return back()->with('error', 'Anda sudah menjadi ketua tim ini.');
        }

        $existing = TeamJoinRequest::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $message = $existing->status === 'pending'
                ? ($existing->type === 'invitation'
                    ? 'Undangan dari tim ini sedang menunggu jawaban Anda.'
                    : 'Permintaan bergabung sudah menunggu persetujuan ketua tim.')
                : 'Permintaan bergabung telah diproses sebelumnya.';

            return back()->with('error', $message);
        }

        TeamJoinRequest::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'type' => 'application',
        ]);

        $team->owner?->notify(new TeamActivityNotification(
            'Permintaan gabung tim',
            $user->name.' ingin bergabung ke tim '.$team->name.'.',
            route('teams.detail', $team),
        ));

        return back()->with('success', 'Permintaan bergabung berhasil dikirim.');
    })->name('teams.join');
    Route::post('/team-invitations/{member}', [MahasiswaPageController::class, 'inviteMember'])->name('teams.invitations.store');
    Route::post('/team-invitations/{joinRequest}/accept', [MahasiswaPageController::class, 'acceptTeamInvitation'])->name('teams.invitations.accept');
    Route::post('/team-invitations/{joinRequest}/reject', [MahasiswaPageController::class, 'rejectTeamInvitation'])->name('teams.invitations.reject');
    Route::post('/team-requests/{joinRequest}/approve', [MahasiswaPageController::class, 'approveJoinRequest'])->name('teams.requests.approve');
    Route::post('/team-requests/{joinRequest}/reject', [MahasiswaPageController::class, 'rejectJoinRequest'])->name('teams.requests.reject');
    Route::get('/messages', [MahasiswaPageController::class, 'messages'])->name('messages.index');
    Route::post('/teams/{team}/messages', [MahasiswaPageController::class, 'sendMessage'])->name('teams.messages.store');
    Route::delete('/teams/{team}/messages/old', [MahasiswaPageController::class, 'clearOldMessages'])->name('teams.messages.clear-old');
    Route::get('/notifications', [MahasiswaPageController::class, 'notifications'])->name('notifications');
    Route::delete('/notifications/old', [MahasiswaPageController::class, 'clearOldNotifications'])->name('notifications.clear-old');

    // ── Susunan Portofolio ────────────────────────────────────────────────────
    Route::get('/susunan-portofolio', [PortofolioController::class, 'index'])->name('portofolio.index');
    Route::get('/susunan-portofolio/publik', [PortofolioController::class, 'publicIndex'])->name('portofolio.public');
    Route::post('/susunan-portofolio', [PortofolioController::class, 'store'])->name('portofolio.store');
    Route::post('/susunan-portofolio/{id}/toggle', [PortofolioController::class, 'toggleVisibility'])->name('portofolio.toggle');
    Route::post('/susunan-portofolio/reorder', [PortofolioController::class, 'reorder'])->name('portofolio.reorder');
    Route::put('/susunan-portofolio/{id}', [PortofolioController::class, 'update'])->name('portofolio.update');
    Route::delete('/susunan-portofolio/{id}', [PortofolioController::class, 'destroy'])->name('portofolio.destroy');
});
