<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageCompetitionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_search_competitions(): void
    {
        $admin = User::factory()->create([
            'npm' => '20250001',
            'role' => 'admin',
        ]);
        $this->createCompetition(['name' => 'Gemastik 2026']);
        $this->createCompetition(['name' => 'Lomba UI/UX']);

        $response = $this->actingAs($admin)->get(route('competition.manage.index', [
            'q' => 'Gemastik',
            'status' => 'active',
        ]));

        $response
            ->assertOk()
            ->assertViewIs('mahasiswa.kelola-pusat-informasi-lomba')
            ->assertViewHas('competitions', fn ($competitions): bool => $competitions->total() === 1)
            ->assertViewHas('filters', fn (array $filters): bool => $filters['search'] === 'Gemastik' && $filters['status'] === 'active')
            ->assertSee('Gemastik 2026');
    }

    public function test_admin_can_create_publish_update_and_delete_a_competition(): void
    {
        $admin = User::factory()->create([
            'npm' => '20250002',
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        $this->post(route('competition.manage.store'), $this->competitionAttributes([
            'name' => 'Lomba Baru',
            'status' => 'active',
        ]))->assertRedirect(route('competition.manage.index'));

        $competition = Competition::query()->where('name', 'Lomba Baru')->firstOrFail();
        $this->assertSame('draft', $competition->status);

        $this->post(route('competition.manage.publish', $competition))
            ->assertRedirect(route('competition.manage.index'));
        $this->assertDatabaseHas('competitions', [
            'id' => $competition->id,
            'status' => 'active',
        ]);

        $this->put(route('competition.manage.update', $competition), $this->competitionAttributes([
            'name' => 'Lomba Diperbarui',
            'description' => 'Informasi lomba yang sudah diperbarui.',
            'status' => 'active',
        ]))->assertRedirect(route('competition.manage.index'));
        $this->assertDatabaseHas('competitions', [
            'id' => $competition->id,
            'name' => 'Lomba Diperbarui',
            'description' => 'Informasi lomba yang sudah diperbarui.',
        ]);

        $this->delete(route('competition.manage.destroy', $competition))
            ->assertRedirect(route('competition.manage.index'));
        $this->assertDatabaseMissing('competitions', ['id' => $competition->id]);
    }

    public function test_students_cannot_access_competition_management(): void
    {
        $student = User::factory()->create([
            'npm' => '20250003',
            'role' => 'mahasiswa',
        ]);

        $this->actingAs($student)
            ->get(route('competition.manage.index'))
            ->assertForbidden();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createCompetition(array $overrides = []): Competition
    {
        return Competition::create($this->competitionAttributes($overrides));
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function competitionAttributes(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Kompetisi TRPL',
            'organizer' => 'Himpunan Mahasiswa',
            'description' => 'Kompetisi untuk mahasiswa teknologi informasi.',
            'category' => 'Hackathon',
            'level' => 'Nasional',
            'opens_at' => today()->toDateString(),
            'deadline' => today()->addDays(14)->toDateString(),
            'fee' => 0,
            'registration_url' => 'https://example.test/daftar',
            'status' => 'active',
        ], $overrides);
    }
}
