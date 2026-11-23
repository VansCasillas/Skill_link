<?php

namespace Database\Seeders;

use App\Models\Competition;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoCompetitions = [
            [
                'name' => 'Gemastik 2026',
                'organizer' => 'Kemdikbudristek',
                'description' => 'Kompetisi nasional bidang TIK bagi mahasiswa Indonesia untuk menyalurkan bakat dan inovasi teknologi informasi.',
                'category' => 'Hackathon',
                'level' => 'Nasional',
                'opens_at' => Carbon::now()->subDays(10)->toDateString(),
                'deadline' => Carbon::now()->addDays(14)->toDateString(), // 14 hari lagi
                'fee' => 0,
                'registration_url' => 'https://gemastik.kemdikbud.go.id',
                'status' => 'active',
            ],
            [
                'name' => 'Kompetisi UI/UX Nasional',
                'organizer' => 'Himpunan Desain Indonesia',
                'description' => 'Kompetisi perancangan antarmuka dan pengalaman pengguna tingkat nasional untuk memecahkan problem sosial.',
                'category' => 'UI/UX Design',
                'level' => 'Nasional',
                'opens_at' => Carbon::now()->subDays(5)->toDateString(),
                'deadline' => Carbon::now()->addDays(24)->toDateString(), // 24 hari lagi
                'fee' => 50000,
                'registration_url' => 'https://uiux.desain.id',
                'status' => 'active',
            ],
            [
                'name' => 'Hackathon Smart City',
                'organizer' => 'Pemprov Lampung',
                'description' => 'Inovasi solusi digital dan IoT untuk tata kelola kota cerdas dan pelayanan publik Lampung.',
                'category' => 'Hackathon',
                'level' => 'Regional',
                'opens_at' => Carbon::now()->toDateString(),
                'deadline' => Carbon::now()->addDays(30)->toDateString(),
                'fee' => 0,
                'registration_url' => 'https://smartcity.lampungprov.go.id',
                'status' => 'draft',
            ],
            [
                'name' => 'Business Case Competition',
                'organizer' => 'BEM FEB',
                'description' => 'Kompetisi studi kasus bisnis nyata dan perumusan strategi akselerasi UMKM di era digital.',
                'category' => 'Business Case',
                'level' => 'Regional',
                'opens_at' => Carbon::now()->subDays(2)->toDateString(),
                'deadline' => Carbon::now()->addDays(45)->toDateString(),
                'fee' => 75000,
                'registration_url' => 'https://bcc.feb.ac.id',
                'status' => 'draft',
            ],
            [
                'name' => 'Data Science Challenge',
                'organizer' => 'Kaggle Indonesia',
                'description' => 'Tantangan analisis data dan machine learning berskala internasional.',
                'category' => 'Data Science',
                'level' => 'Internasional',
                'opens_at' => Carbon::now()->subDays(60)->toDateString(),
                'deadline' => Carbon::now()->subDays(5)->toDateString(), // Sudah lewat deadline
                'fee' => 0,
                'registration_url' => 'https://kaggle.com',
                'status' => 'ended',
            ],
        ];

        foreach ($demoCompetitions as $item) {
            Competition::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
