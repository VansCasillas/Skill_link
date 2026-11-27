<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CariTimSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_search_teams_by_name_lomba_or_skill(): void
    {
        $student = User::factory()->create([
            'npm' => '20250001',
            'role' => 'mahasiswa',
        ]);
        $otherStudent = User::factory()->create(['role' => 'mahasiswa']);
        Team::create([
            'owner_id' => $otherStudent->id,
            'name' => 'Tim Garuda Code',
            'competition' => 'Gemastik 2026',
            'description' => 'Membangun aplikasi layanan publik.',
            'skills' => ['Flutter', 'UI/UX'],
            'capacity' => 4,
            'members_count' => 2,
        ]);
        Team::create([
            'owner_id' => $otherStudent->id,
            'name' => 'Tim PixelForce',
            'competition' => 'Kompetisi UI/UX 2026',
            'skills' => ['UI/UX'],
            'capacity' => 4,
            'members_count' => 2,
        ]);

        $response = $this->actingAs($student)->get(route('recruitment.index', [
            'q' => 'Flutter',
        ]));

        $response
            ->assertOk()
            ->assertViewIs('mahasiswa.cari-tim')
            ->assertViewHas('teams', fn (array $teams): bool => count($teams) === 1
                && $teams[0]['nama'] === 'Tim Garuda Code'
            )
            ->assertViewHas('suggestions', fn ($suggestions): bool => $suggestions->contains(
                fn (array $suggestion): bool => $suggestion['value'] === 'Tim Garuda Code'
                    && $suggestion['type'] === 'Tim'
            ) && $suggestions->contains(
                fn (array $suggestion): bool => $suggestion['value'] === 'Gemastik 2026'
                    && $suggestion['type'] === 'Lomba'
            ) && $suggestions->contains(
                fn (array $suggestion): bool => $suggestion['value'] === 'Flutter'
                    && $suggestion['type'] === 'Skill'
            ) && $suggestions->contains(
                fn (array $suggestion): bool => $suggestion['value'] === $otherStudent->name
                    && $suggestion['type'] === 'Anggota'
                    && $suggestion['mode'] === 'anggota'
            ))
            ->assertSee('Tim Garuda Code');
    }

    public function test_student_can_find_members_by_partial_portfolio_skill_and_only_see_public_work(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $candidate = User::factory()->create([
            'name' => 'Nadia Kusuma',
            'role' => 'mahasiswa',
            'prodi' => 'Teknologi Informasi',
        ]);

        Portfolio::create([
            'user_id' => $candidate->id,
            'title' => 'Aplikasi Mobile Flutter',
            'type' => 'Proyek',
            'description' => 'Proyek layanan kampus.',
            'tags' => ['Flutter', 'Dart'],
            'evidence_path' => 'portfolio-evidence/flutter.jpg',
            'status' => 'approved',
            'is_visible' => true,
            'sort_order' => 1,
        ]);
        Portfolio::create([
            'user_id' => $candidate->id,
            'title' => 'Sertifikat Tersembunyi',
            'type' => 'Sertifikat',
            'tags' => ['Flutter'],
            'evidence_path' => 'portfolio-evidence/hidden.jpg',
            'status' => 'approved',
            'is_visible' => false,
            'sort_order' => 2,
        ]);
        Portfolio::create([
            'user_id' => $candidate->id,
            'title' => 'Sertifikat Belum Divalidasi',
            'type' => 'Sertifikat',
            'tags' => ['Flutter'],
            'evidence_path' => 'portfolio-evidence/pending.jpg',
            'status' => 'pending',
            'is_visible' => true,
            'sort_order' => 3,
        ]);

        $this->actingAs($student)
            ->get(route('recruitment.index', [
                'mode' => 'anggota',
                'q' => 'flut',
            ]))
            ->assertOk()
            ->assertViewHas('members', fn (array $members): bool => count($members) === 1
                && $members[0]['nama'] === 'Nadia Kusuma'
            )
            ->assertViewHas('suggestions', fn ($suggestions): bool => $suggestions->contains(
                fn (array $suggestion): bool => $suggestion['value'] === 'Flutter'
                    && $suggestion['type'] === 'Skill'
                    && $suggestion['mode'] === 'anggota'
            ) && $suggestions->contains(
                fn (array $suggestion): bool => $suggestion['value'] === 'Aplikasi Mobile Flutter'
                    && $suggestion['type'] === 'Proyek'
                    && $suggestion['mode'] === 'anggota'
            ))
            ->assertSee('Aplikasi Mobile Flutter')
            ->assertSee('ct-suggestions')
            ->assertSee('ct-global-search-input')
            ->assertSee('ct-global-search-suggestions')
            ->assertSee('Flutter')
            ->assertDontSee('Sertifikat Tersembunyi')
            ->assertDontSee('Sertifikat Belum Divalidasi');
    }
}
