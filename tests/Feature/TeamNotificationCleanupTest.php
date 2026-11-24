<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\TeamMessage;
use App\Models\User;
use App\Notifications\TeamActivityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class TeamNotificationCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_join_request_and_decision_notify_the_right_users(): void
    {
        $owner = User::factory()->create(['role' => 'mahasiswa']);
        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $team = $this->createTeam($owner);

        $this->actingAs($applicant)
            ->post(route('teams.join', $team))
            ->assertRedirect();

        $this->assertSame('Permintaan gabung tim', $owner->notifications()->first()->data['title']);

        $joinRequest = TeamJoinRequest::where('team_id', $team->id)
            ->where('user_id', $applicant->id)
            ->firstOrFail();

        $this->actingAs($owner)
            ->post(route('teams.requests.approve', $joinRequest))
            ->assertRedirect();

        $this->assertSame('Permintaan gabung diterima', $applicant->fresh()->notifications()->first()->data['title']);

        $rejectedApplicant = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($rejectedApplicant)
            ->post(route('teams.join', $team))
            ->assertRedirect();

        $rejectedRequest = TeamJoinRequest::where('team_id', $team->id)
            ->where('user_id', $rejectedApplicant->id)
            ->firstOrFail();

        $this->actingAs($owner)
            ->post(route('teams.requests.reject', $rejectedRequest))
            ->assertRedirect();

        $this->assertSame('Permintaan gabung ditolak', $rejectedApplicant->fresh()->notifications()->first()->data['title']);
    }

    public function test_new_messages_notify_other_team_members_but_not_the_sender(): void
    {
        $owner = User::factory()->create(['role' => 'mahasiswa']);
        $sender = User::factory()->create(['role' => 'mahasiswa']);
        $recipient = User::factory()->create(['role' => 'mahasiswa']);
        $team = $this->createTeam($owner, membersCount: 3);

        TeamJoinRequest::create([
            'team_id' => $team->id,
            'user_id' => $sender->id,
            'status' => 'approved',
        ]);
        TeamJoinRequest::create([
            'team_id' => $team->id,
            'user_id' => $recipient->id,
            'status' => 'approved',
        ]);

        $this->actingAs($sender)
            ->post(route('teams.messages.store', $team), ['body' => 'Rapat dimulai pukul 16.00.'])
            ->assertRedirect(route('messages.index', ['team' => $team->id]));

        $this->assertSame(1, $owner->fresh()->notifications()->count());
        $this->assertSame(1, $recipient->fresh()->notifications()->count());
        $this->assertSame(0, $sender->fresh()->notifications()->count());
        $this->assertSame(
            'Pesan baru di '.$team->name,
            $recipient->fresh()->notifications()->first()->data['title'],
        );
    }

    public function test_unread_notifications_show_a_badge_until_the_notification_page_is_opened(): void
    {
        $member = User::factory()->create(['role' => 'mahasiswa']);
        $notification = $this->createNotification($member, 'Pembaruan tim');

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<span class="dot"></span>', false);

        $this->actingAs($member)
            ->get(route('notifications'))
            ->assertOk();

        $this->assertNotNull($notification->fresh()->read_at);

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('<span class="dot"></span>', false);
    }

    public function test_old_messages_can_be_hidden_by_one_member_without_deleting_them_for_others(): void
    {
        $owner = User::factory()->create(['role' => 'mahasiswa']);
        $member = User::factory()->create(['role' => 'mahasiswa']);
        $team = $this->createTeam($owner, membersCount: 2);
        TeamJoinRequest::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'status' => 'approved',
        ]);
        $oldMessage = $this->createMessage($team, $owner, 'Pesan lama');

        $this->actingAs($member)
            ->get(route('messages.index', ['team' => $team->id]))
            ->assertOk()
            ->assertSee('Bersihkan 1 pesan lama dari tampilan saya');

        $this->actingAs($member)
            ->delete(route('teams.messages.clear-old', $team))
            ->assertRedirect(route('messages.index', ['team' => $team->id]));

        $this->assertDatabaseHas('team_messages', ['id' => $oldMessage->id]);
        $this->assertDatabaseHas('hidden_team_messages', [
            'team_message_id' => $oldMessage->id,
            'user_id' => $member->id,
        ]);

        $this->actingAs($member)
            ->get(route('messages.index', ['team' => $team->id]))
            ->assertOk()
            ->assertDontSee('Pesan lama');

        $this->actingAs($owner)
            ->get(route('messages.index', ['team' => $team->id]))
            ->assertOk()
            ->assertSee('Pesan lama');
    }

    public function test_non_members_cannot_hide_a_teams_old_messages(): void
    {
        $owner = User::factory()->create(['role' => 'mahasiswa']);
        $outsider = User::factory()->create(['role' => 'mahasiswa']);
        $team = $this->createTeam($owner);
        $message = $this->createMessage($team, $owner, 'Pesan milik tim');

        $this->actingAs($outsider)
            ->delete(route('teams.messages.clear-old', $team))
            ->assertForbidden();

        $this->assertDatabaseMissing('hidden_team_messages', [
            'team_message_id' => $message->id,
            'user_id' => $outsider->id,
        ]);
    }

    public function test_member_can_clear_only_their_own_notifications_older_than_thirty_days(): void
    {
        $member = User::factory()->create(['role' => 'mahasiswa']);
        $otherMember = User::factory()->create(['role' => 'mahasiswa']);
        $oldNotification = $this->createNotification($member, 'Notifikasi lama');
        $recentNotification = $this->createNotification($member, 'Notifikasi baru');
        $otherOldNotification = $this->createNotification($otherMember, 'Notifikasi anggota lain');
        $this->setNotificationAge($oldNotification, 31);
        $this->setNotificationAge($otherOldNotification, 31);
        $this->setNotificationAge($recentNotification, 29);

        $this->actingAs($member)
            ->get(route('notifications'))
            ->assertOk()
            ->assertViewHas('oldNotificationCount', 1)
            ->assertSee('Notifikasi lama')
            ->assertSee('Bersihkan 1 notifikasi');

        $this->actingAs($member)
            ->delete(route('notifications.clear-old'))
            ->assertRedirect(route('notifications'));

        $this->assertDatabaseMissing('notifications', ['id' => $oldNotification->id]);
        $this->assertDatabaseHas('notifications', ['id' => $recentNotification->id]);
        $this->assertDatabaseHas('notifications', ['id' => $otherOldNotification->id]);
    }

    private function createTeam(User $owner, int $membersCount = 1): Team
    {
        return Team::create([
            'owner_id' => $owner->id,
            'name' => 'Tim Uji',
            'competition' => 'Gemastik',
            'capacity' => 4,
            'members_count' => $membersCount,
        ]);
    }

    private function createMessage(Team $team, User $sender, string $body): TeamMessage
    {
        $message = TeamMessage::create([
            'team_id' => $team->id,
            'user_id' => $sender->id,
            'body' => $body,
        ]);
        $message->forceFill([
            'created_at' => now()->subDays(31),
            'updated_at' => now()->subDays(31),
        ])->save();

        return $message;
    }

    private function createNotification(User $user, string $title): DatabaseNotification
    {
        $existingNotificationIds = $user->notifications()->pluck('id');
        $user->notify(new TeamActivityNotification($title, 'Deskripsi', route('notifications')));

        return $user->notifications()
            ->whereNotIn('id', $existingNotificationIds)
            ->firstOrFail();
    }

    private function setNotificationAge(DatabaseNotification $notification, int $days): void
    {
        $notification->forceFill([
            'created_at' => now()->subDays($days),
            'updated_at' => now()->subDays($days),
        ])->save();
    }
}
