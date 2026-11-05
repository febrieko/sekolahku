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
        Schema::create('time_schedule_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name', 100); // e.g. "Jam KBM Normal", "Jam Khusus Ramadhan", "Jam Pekan Ujian"
            $table->string('code', 30); // NORMAL, RAMADHAN, EXAM
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('effective_start_date')->nullable();
            $table->date('effective_end_date')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'is_active']);
            $table->index(['school_id', 'is_default']);
        });

        Schema::create('lesson_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('time_schedule_categories')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1: Senin, 2: Selasa, ..., 5: Jumat, 6: Sabtu
            $table->string('name', 50); // "Jam Ke-1", "Istirahat 1", "Upacara", "Sholat Dhuhur"
            $table->integer('period_number')->nullable(); // 1, 2, 3... (NULL jika istirahat)
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_break')->default(false);
            $table->timestamps();

            $table->index(['category_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_periods');
        Schema::dropIfExists('time_schedule_categories');
    }
};
