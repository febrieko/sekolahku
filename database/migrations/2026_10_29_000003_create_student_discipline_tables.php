<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('student_discipline_records');
        Schema::dropIfExists('discipline_policies');
        Schema::dropIfExists('discipline_amnesty_rules');
        Schema::dropIfExists('discipline_thresholds');
        Schema::dropIfExists('discipline_items');
        Schema::dropIfExists('discipline_categories');

        // 1. Master Kategori Pelanggaran Siswa
        Schema::create('discipline_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('code', 30);
            $table->string('name', 150);
            $table->string('severity', 20)->default('RINGAN'); // RINGAN, SEDANG, BERAT
            $table->string('color', 25)->default('#f59e0b');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'code']);
        });

        // 2. Master Item Pasal Pelanggaran Siswa beserta Besar Poin
        Schema::create('discipline_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('discipline_categories')->cascadeOnDelete();
            $table->string('code', 30);
            $table->string('name', 255);
            $table->unsignedInteger('points')->default(5);
            $table->string('default_sanction', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'code']);
        });

        // 3. Pengaturan Ambang Batas Pelanggaran & Tindakan Sanksi (Diseting oleh Waka Kesiswaan)
        Schema::create('discipline_thresholds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('level_code', 30); // PEMBINAAN, SP1, SP2, SP3, KONFERENSI
            $table->string('name', 150);
            $table->unsignedInteger('min_points');
            $table->unsignedInteger('max_points')->nullable();
            $table->text('sanction_action')->nullable();
            $table->boolean('require_parent_call')->default(false);
            $table->boolean('require_bk_counseling')->default(false);
            $table->string('color', 25)->default('#f59e0b');
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'level_code']);
        });

        // 4. Pengaturan Jenis & Syarat Pemutihan / Pengurangan Poin (Diseting oleh Waka Kesiswaan)
        Schema::create('discipline_amnesty_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('code', 30);
            $table->string('name', 200);
            $table->unsignedInteger('credit_points')->default(10); // Besar poin pengurangan/pemutihan
            $table->unsignedInteger('min_violation_points_required')->default(0);
            $table->unsignedSmallInteger('clean_days_required')->default(0); // Hari bebas pelanggaran baru
            $table->unsignedSmallInteger('max_claims_per_semester')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'code']);
        });

        // 5. Kebijakan Umum Ambang Batas & Pemutihan Kesiswaan per Sekolah (Diseting oleh Waka Kesiswaan)
        Schema::create('discipline_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->unique()->constrained('schools')->cascadeOnDelete();
            $table->unsignedInteger('max_violation_points')->default(100);
            $table->string('reset_period', 30)->default('ACADEMIC_YEAR'); // SEMESTER, ACADEMIC_YEAR, NEVER
            $table->boolean('amnesty_enabled')->default(true);
            $table->unsignedInteger('max_amnesty_points_per_semester')->default(50);
            $table->unsignedSmallInteger('min_clean_days_before_amnesty')->default(7);
            $table->unsignedInteger('auto_refer_bk_threshold')->default(50);
            $table->boolean('allow_amnesty_for_heavy_violations')->default(false);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 6. Buku Kredit Poin Siswa (Riwayat Pelanggaran & Pemutihan Poin)
        Schema::create('student_discipline_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('academic_semester', 10)->default('odd');
            $table->string('record_type', 20)->default('VIOLATION'); // VIOLATION, AMNESTY
            $table->foreignId('discipline_item_id')->nullable()->constrained('discipline_items')->nullOnDelete();
            $table->foreignId('amnesty_rule_id')->nullable()->constrained('discipline_amnesty_rules')->nullOnDelete();
            $table->date('record_date');
            $table->unsignedInteger('points');
            $table->string('title', 255);
            $table->text('notes')->nullable();
            $table->string('location', 150)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('APPROVED'); // APPROVED, PENDING, REJECTED
            $table->timestamps();

            $table->index(['school_id', 'student_id', 'academic_year_id'], 'sdr_school_student_year_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_discipline_records');
        Schema::dropIfExists('discipline_policies');
        Schema::dropIfExists('discipline_amnesty_rules');
        Schema::dropIfExists('discipline_thresholds');
        Schema::dropIfExists('discipline_items');
        Schema::dropIfExists('discipline_categories');
    }
};
