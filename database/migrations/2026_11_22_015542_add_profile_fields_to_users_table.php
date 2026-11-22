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
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('jurusan', 100)->nullable()->after('npm');
                $table->string('prodi', 100)->nullable()->after('jurusan');
                $table->string('angkatan', 4)->nullable()->after('prodi');
                $table->text('bio')->nullable()->after('angkatan');
                $table->string('foto')->nullable()->after('bio');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['jurusan', 'prodi', 'angkatan', 'bio', 'foto']);
            });
        });
    }
};
