<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('competition_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('competition_submissions')->cascadeOnDelete();
            $table->foreignId('competition_judge_id')->constrained('competition_judges')->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->unique(['submission_id', 'competition_judge_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_scores');
    }
};
