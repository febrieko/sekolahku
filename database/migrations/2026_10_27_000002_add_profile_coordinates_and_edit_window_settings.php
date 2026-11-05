<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 1. Kontak HP siswa & koordinat rumah pada tabel students.
     * 2. Pengaturan rentang tanggal pemutakhiran profil siswa mandiri pada tabel schools.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_phone', 25)->nullable()->after('parent_phone');
            $table->decimal('home_latitude', 10, 7)->nullable()->after('postal_code');
            $table->decimal('home_longitude', 10, 7)->nullable()->after('home_latitude');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->boolean('student_profile_edit_enabled')->default(true)->after('is_active');
            $table->date('student_profile_edit_start')->nullable()->after('student_profile_edit_enabled');
            $table->date('student_profile_edit_end')->nullable()->after('student_profile_edit_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'student_profile_edit_enabled',
                'student_profile_edit_start',
                'student_profile_edit_end',
            ]);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'student_phone',
                'home_latitude',
                'home_longitude',
            ]);
        });
    }
};
