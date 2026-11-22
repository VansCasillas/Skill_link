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
}
