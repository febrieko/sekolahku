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
            $table->string('additional_role', 50)->nullable()->after('role'); // waka_kurikulum, waka_kesiswaan, waka_sarpras, waka_humas, kepala_lab, kepala_perpus, pembina_osis
            $table->index(['school_id', 'role']);
            $table->index(['school_id', 'additional_role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['school_id', 'role']);
            $table->dropIndex(['school_id', 'additional_role']);
            $table->dropColumn('additional_role');
        });
    }
};

