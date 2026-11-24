<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_topbar_displays_the_saved_profile_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('foto-profil/akun.jpg', 'image-content');

        $student = User::factory()->create([
            'foto' => 'foto-profil/akun.jpg',
            'role' => 'mahasiswa',
        ]);
        $photoUrl = Storage::disk('public')->url($student->foto);

        $response = $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk();

        $response->assertSee('<img src="'.$photoUrl.'" alt="">', false);
        $this->assertSame(2, substr_count($response->getContent(), '<img src="'.$photoUrl.'" alt="">'));
    }
}
