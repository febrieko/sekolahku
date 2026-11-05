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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('nis', 30)->nullable();
            $table->string('nisn', 30)->nullable();
            $table->string('full_name', 255);
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->date('birth_date')->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('parent_phone', 30)->nullable();
            $table->string('parent_name', 150)->nullable();
            $table->string('photo_url', 500)->nullable();
            $table->enum('status', ['active', 'graduated', 'transferred', 'dropout'])->default('active');
            $table->decimal('wallet_balance', 15, 2)->default(0);
            $table->date('enrollment_date')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'class_id']);
            $table->index(['school_id', 'nis']);
            $table->index(['school_id', 'nisn']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
