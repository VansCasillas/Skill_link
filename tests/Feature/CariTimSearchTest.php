<?php

namespace Tests\Feature;

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

        $response = $this->actingAs($student)->get(route('recruitment.index', [
            'q' => 'Flutter',
        ]));

        $response
            ->assertOk()
            ->assertViewIs('mahasiswa.cari-tim')
            ->assertViewHas('teams', fn (array $teams): bool => count($teams) === 1
                && $teams[0]['nama'] === 'Tim Garuda Code'
            )
            ->assertSee('Tim Garuda Code')
            ->assertDontSee('Tim PixelForce');
    }
}
