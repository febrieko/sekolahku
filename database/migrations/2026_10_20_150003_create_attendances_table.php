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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in_at')->nullable();
            $table->time('check_out_at')->nullable();
            $table->enum('status', ['present', 'late', 'sick', 'excused', 'absent'])->default('present');
            $table->enum('method', ['selfie', 'qr_code', 'rfid', 'manual'])->default('selfie');
            $table->string('selfie_url', 500)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('gps_accuracy', 8, 2)->nullable();
            $table->decimal('distance_meters', 10, 2)->nullable();
            $table->boolean('is_within_radius')->default(false);
            $table->boolean('is_mock_location')->default(false);
            $table->string('device_info', 255)->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'student_id', 'date']);
            $table->index(['school_id', 'date']);
            $table->index(['student_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
