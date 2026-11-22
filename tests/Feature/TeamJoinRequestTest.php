<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamJoinRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_owner_can_send_and_view_team_message(): void
    {
        $owner = User::factory()->create(['role' => 'mahasiswa']);
        $team = Team::create([
            'owner_id' => $owner->id,
            'name' => 'Tim Komunikasi',
            'competition' => 'Gemastik',
            'capacity' => 4,
            'members_count' => 1,
        ]);

        $this->actingAs($owner)
            ->post(route('teams.messages.store', $team), [
                'body' => 'Rapat tim pukul 16.00.',
            ])
            ->assertRedirect(route('messages.index', ['team' => $team->id]));

        $this->assertDatabaseHas('team_messages', [
            'team_id' => $team->id,
            'user_id' => $owner->id,
            'body' => 'Rapat tim pukul 16.00.',
        ]);

        $this->actingAs($owner)
            ->get(route('messages.index', ['team' => $team->id]))
            ->assertOk()
            ->assertSee('Rapat tim pukul 16.00.');
    }

    public function test_student_can_create_team_from_dashboard_link(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($student)
            ->get(route('recruitment.index', ['buat' => 1]))
            ->assertOk()
            ->assertSee('modalCreate.classList.add');

        $this->actingAs($student)
            ->post(route('teams.store'), [
                'name' => 'Tim Inovasi',
                'competition' => 'Gemastik 2026',
                'description' => 'Membuat aplikasi inovatif.',
                'skills' => 'Laravel, UI/UX',
                'capacity' => 4,
            ])
            ->assertRedirect(route('tim.index'));

        $this->assertDatabaseHas('teams', [
            'owner_id' => $student->id,
            'name' => 'Tim Inovasi',
            'members_count' => 1,
        ]);
    }

    public function test_member_can_request_to_join_team(): void
    {
        $owner = User::factory()->create(['role' => 'mahasiswa']);
        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $team = Team::create([
            'owner_id' => $owner->id,
            'name' => 'Tim Garuda',
            'competition' => 'Gemastik',
            'capacity' => 4,
            'members_count' => 2,
        ]);

        $this->actingAs($applicant)
            ->post(route('teams.join', $team))
            ->assertRedirect();

        $this->assertDatabaseHas('team_join_requests', [
            'team_id' => $team->id,
            'user_id' => $applicant->id,
            'status' => 'pending',
        ]);
    }

    public function test_owner_can_approve_join_request(): void
    {
        $owner = User::factory()->create(['role' => 'mahasiswa']);
        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $team = Team::create([
            'owner_id' => $owner->id,
            'name' => 'Tim Pixel',
            'competition' => 'UI/UX',
            'capacity' => 4,
            'members_count' => 2,
        ]);
        $joinRequest = TeamJoinRequest::create([
            'team_id' => $team->id,
            'user_id' => $applicant->id,
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->post(route('teams.requests.approve', $joinRequest))
            ->assertRedirect();

        $this->assertDatabaseHas('team_join_requests', [
            'id' => $joinRequest->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'members_count' => 3,
        ]);
    }

    public function test_teams_page_lists_pending_requests_to_owner(): void
    {
        $owner = User::factory()->create(['role' => 'mahasiswa']);
        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $team = Team::create([
            'owner_id' => $owner->id,
            'name' => 'Tim Garuda Code',
            'competition' => 'Gemastik',
            'capacity' => 4,
            'members_count' => 2,
        ]);
        TeamJoinRequest::create([
            'team_id' => $team->id,
            'user_id' => $applicant->id,
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->get(route('tim.index'))
            ->assertOk()
            ->assertSee('Permintaan Masuk Tim')
            ->assertSee($applicant->name);
    }
}
