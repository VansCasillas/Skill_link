<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_login_always_redirects_to_dashboard(): void
    {
        $student = User::factory()->create([
            'password' => 'password',
            'role' => 'mahasiswa',
        ]);

        $this->withSession(['url.intended' => route('profile')])
            ->post(route('login.post'), [
                'email' => $student->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_login_redirects_to_competition_management(): void
    {
        $admin = User::factory()->create([
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->post(route('login.post'), [
            'email' => $admin->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('competition.manage.index'));
    }

    public function test_authenticated_root_redirects_by_role(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($student)
            ->get('/')
            ->assertRedirect(route('dashboard'));
    }

    public function test_student_placeholder_navigation_opens_its_own_pages(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($student)
            ->get(route('tim.index'))
            ->assertOk()
            ->assertViewIs('mahasiswa.kelola-tim');

        $this->actingAs($student)
            ->get(route('messages.index'))
            ->assertOk()
            ->assertViewIs('mahasiswa.pesan');

        $this->actingAs($student)
            ->get(route('notifications'))
            ->assertOk()
            ->assertViewIs('mahasiswa.notifikasi');
    }

    public function test_student_topbar_links_to_profile_and_notifications(): void
    {
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Dzaky',
        ]);

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('profile'), false)
            ->assertSee(route('notifications'), false);
    }

    public function test_profile_page_has_a_logout_action_that_requires_login_again(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($student)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('>Keluar</button>', false);

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();

        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_updated_profile_name_is_used_in_the_topbar_across_student_pages(): void
    {
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'jurusan' => 'Teknologi Informasi',
            'prodi' => 'TRPL',
            'angkatan' => '2022',
            'bio' => 'Mahasiswa aktif',
        ]);

        $this->actingAs($student)
            ->put(route('profile.update'), [
                'name' => 'Nama Profil Terbaru',
                'npm' => $student->npm,
                'jurusan' => $student->jurusan,
                'prodi' => $student->prodi,
                'email' => $student->email,
                'angkatan' => $student->angkatan,
                'bio' => $student->bio,
            ])
            ->assertRedirect(route('profile'));

        $studentPages = [
            route('dashboard'),
            route('recruitment.index'),
            route('tim.index'),
            route('portofolio.index'),
            route('messages.index'),
            route('notifications'),
        ];

        foreach ($studentPages as $page) {
            $this->get($page)
                ->assertOk()
                ->assertSee('<span class="me-name">Nama Profil Terbaru</span>', false);
        }
    }
}
