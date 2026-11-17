<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
                'name' => 'Muhammad Ivan Alfaridzi Casillas',
                'email' => 'm.ivanalfaridzi@gmail.com',
                'npm' => '26783059',
                'password' => Hash::make('skilllink'),
                'role' => 'mahasiswa',
            ]);
    }
}
