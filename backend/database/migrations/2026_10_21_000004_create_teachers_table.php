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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // Identitas Resmi Dapodik
            $table->string('nip', 30)->nullable()->unique();
            $table->string('nuptk', 30)->nullable()->unique();
            $table->string('nik', 16)->nullable();
            $table->string('full_name', 255);
            $table->string('title_prefix', 50)->nullable(); // Dr., Drs., Prof.
            $table->string('title_suffix', 50)->nullable(); // S.Pd., M.Pd.
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('mother_maiden_name', 255)->nullable(); // Kunci validasi Dapodik
            $table->enum('religion', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'])->default('Islam');
            // Alamat Domisili
            $table->text('address')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('sub_district', 100)->nullable(); // Kelurahan/Desa
            $table->string('district', 100)->nullable(); // Kecamatan
            $table->string('city', 100)->nullable(); // Kab/Kota
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            // Kepegawaian & Penugasan Dapodik
            $table->enum('employment_type', [
                'GURU_KELAS', 'GURU_MAPEL', 'GURU_BK', 'TENAGA_ADMINISTRASI',
                'LABORAN', 'PUSTAKAWAN', 'KEPALA_SEKOLAH'
            ])->default('GURU_MAPEL');
            $table->enum('employment_status', [
                'PNS', 'CPNS', 'PPPK', 'GTY', 'GTT', 'HONOR_SEKOLAH'
            ])->default('GTT');
            $table->string('sk_appointment_number', 100)->nullable();
            $table->date('sk_appointment_date')->nullable();
            $table->string('institution_assignment', 150)->nullable();
            $table->boolean('is_certified')->default(false);
            $table->string('certification_number', 50)->nullable();
            $table->enum('last_education', ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'])->default('S1');
            $table->string('major_study', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['school_id', 'is_active']);
            $table->index(['school_id', 'employment_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
