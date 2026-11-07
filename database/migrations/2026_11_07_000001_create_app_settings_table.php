<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('app_settings')) {
            Schema::create('app_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('group', 50)->default('general');
                $table->timestamps();
            });

            // Nilai awal identitas & branding aplikasi
            DB::table('app_settings')->insert([
                [
                    'key' => 'app_name',
                    'value' => 'Sekolahku',
                    'group' => 'branding',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'app_tagline',
                    'value' => 'v2.0',
                    'group' => 'branding',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'app_logo',
                    'value' => null,
                    'group' => 'branding',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
