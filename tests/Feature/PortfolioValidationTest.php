<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortfolioValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_portfolio_with_evidence(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($student)
            ->post(route('portofolio.store'), [
                'judul' => 'Sertifikat Laravel',
                'tipe' => 'Sertifikat',
                'deskripsi' => 'Sertifikat penyelesaian pelatihan Laravel.',
                'tags' => 'Laravel, PHP',
                'ditampilkan' => '1',
                'bukti' => UploadedFile::fake()->image('sertifikat.jpg'),
            ])
            ->assertRedirect(route('portofolio.index'));

        $portfolio = Portfolio::firstOrFail();
        $this->assertSame('pending', $portfolio->status);
        Storage::disk('public')->assertExists($portfolio->evidence_path);
    }

    public function test_admin_can_approve_portfolio(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $admin = User::factory()->create(['role' => 'admin']);
        $portfolio = Portfolio::create([
            'user_id' => $student->id,
            'title' => 'Juara UI/UX',
            'type' => 'Prestasi',
            'description' => 'Juara lomba desain.',
            'tags' => ['UI/UX'],
            'evidence_path' => UploadedFile::fake()->image('juara.jpg')->store('portfolio-evidence', 'public'),
            'status' => 'pending',
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)
            ->post(route('portfolio.validation.approve', $portfolio))
            ->assertRedirect();

        $this->assertDatabaseHas('portfolios', [
            'id' => $portfolio->id,
            'status' => 'approved',
        ]);
    }
}
