<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Spektrum Keahlian SMK (Kemendikbudristek).
     */
    public function up(): void
    {
        // 1. Bidang Keahlian (Vocational Fields - e.g. Teknologi Informasi, Bisnis Manajemen)
        Schema::create('vocational_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('code', 20); // TI, TMR, BM, PAR, dll.
            $table->string('name', 150); // e.g. "Teknologi Informasi"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'code']);
        });

        // 2. Program Keahlian (Vocational Programs - Tingkat 10 / Fase E)
        Schema::create('vocational_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('vocational_field_id')->constrained('vocational_fields')->cascadeOnDelete();
            $table->string('code', 30); // PPLG, TJKT, AKL, MPLB, TO
            $table->string('name', 150); // e.g. "Pengembangan Perangkat Lunak dan Gim"
            $table->tinyInteger('program_duration_years')->default(3); // 3 Tahun atau 4 Tahun (Fast Track)
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'code']);
        });

        // 3. Konsentrasi Keahlian (Competency Concentrations - Tingkat 11-12 / Fase F)
        Schema::create('vocational_concentrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('vocational_program_id')->constrained('vocational_programs')->cascadeOnDelete();
            $table->string('code', 30); // RPL, GIM, TKJ, TJAT, TKR, TSM, AK
            $table->string('dapodik_code', 20)->nullable(); // Kode Dapodik resmi 6 digit
            $table->string('name', 150); // e.g. "Rekayasa Perangkat Lunak"
            $table->text('description')->nullable();
            $table->text('competencies_summary')->nullable(); // Profil kompetensi lulusan
            $table->text('industry_partners')->nullable(); // Mitra Dunia Usaha / Dunia Industri (DUDI)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'code']);
        });

        // 4. Update Classes table to associate with Vocational Program and Concentration
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('vocational_program_id')->nullable()->after('department_id')
                ->constrained('vocational_programs')->nullOnDelete();
            $table->foreignId('vocational_concentration_id')->nullable()->after('vocational_program_id')
                ->constrained('vocational_concentrations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['vocational_concentration_id']);
            $table->dropForeign(['vocational_program_id']);
            $table->dropColumn(['vocational_concentration_id', 'vocational_program_id']);
        });

        Schema::dropIfExists('vocational_concentrations');
        Schema::dropIfExists('vocational_programs');
        Schema::dropIfExists('vocational_fields');
    }
};
