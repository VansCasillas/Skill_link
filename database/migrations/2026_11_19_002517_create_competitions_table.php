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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('organizer', 200);
            $table->text('description');
            $table->string('category', 100);
            $table->string('level', 20);
            $table->date('opens_at');
            $table->date('deadline');
            $table->unsignedInteger('fee')->default(0);
            $table->string('registration_url', 2048);
            $table->string('poster_path')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
