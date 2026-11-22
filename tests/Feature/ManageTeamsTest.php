<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageTeamsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_team_information(): void
    {
        $owner = User::factory()->create(['role' => 'mahasiswa']);
        $team = Team::create([
            'owner_id' => $owner->id,
            'name' => 'Tim Lama',
            'competition' => 'Lomba Lama',
            'capacity' => 4,
            'members_count' => 1,
        ]);

        $this->actingAs($owner)
            ->put(route('teams.update', $team), [
                'name' => 'Tim Baru',
                'competition' => 'Lomba Baru',
                'description' => 'Deskripsi baru',
                'skills' => 'Laravel, UI/UX',
                'capacity' => 5,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Tim Baru',
            'competition' => 'Lomba Baru',
            'capacity' => 5,
        ]);
    }

    public function test_owner_can_remove_an_approved_member(): void
    {
        $owner = User::factory()->create(['role' => 'mahasiswa']);
        $member = User::factory()->create(['role' => 'mahasiswa']);
        $team = Team::create([
            'owner_id' => $owner->id,
            'name' => 'Tim Uji',
            'competition' => 'Lomba Uji',
            'capacity' => 4,
            'members_count' => 2,
        ]);
        $joinRequest = TeamJoinRequest::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'status' => 'approved',
        ]);

        $this->actingAs($owner)
            ->delete(route('teams.members.remove', [$team, $joinRequest]))
            ->assertRedirect();

        $this->assertDatabaseMissing('team_join_requests', ['id' => $joinRequest->id]);
        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'members_count' => 1,
        ]);
    }

    public function test_owner_can_open_team_detail_with_members_and_join_requests(): void
    {
        $owner = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Ketua Uji']);
        $member = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Anggota Uji']);
        $applicant = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Pendaftar Uji']);
        $team = Team::create([
            'owner_id' => $owner->id,
            'name' => 'Tim Detail',
            'competition' => 'Gemastik',
            'capacity' => 4,
            'members_count' => 2,
        ]);

        TeamJoinRequest::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'status' => 'approved',
        ]);
        TeamJoinRequest::create([
            'team_id' => $team->id,
            'user_id' => $applicant->id,
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->get(route('teams.detail', $team))
            ->assertOk()
            ->assertSee('Tim Detail')
            ->assertSee('Anggota Uji')
            ->assertSee('Pendaftar Uji')
            ->assertSee('Edit Tim');
    }
}
