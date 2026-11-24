<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserProfileCompletionTest extends TestCase
{
    public function test_it_calculates_profile_completion_percentage(): void
    {
        $user = new User([
            'name' => 'Ani',
            'npm' => '123456',
            'jurusan' => 'Teknologi Informasi',
            'prodi' => 'TRPL',
            'email' => 'ani@example.com',
            'angkatan' => '2026',
            'bio' => 'Mahasiswa aktif',
            'foto' => 'foto-profil/ani.jpg',
        ]);

        $this->assertSame(100, $user->profileCompletionPercent());

        $user->foto = null;
        $this->assertSame(88, $user->profileCompletionPercent());
    }
}
