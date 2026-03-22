<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. SoftDeletes untuk categories
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'deleted_at')) {
                $table->softDeletes()->after('is_active');
            }
        });

        // 2. Field BPJS pada transactions
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'tipe_pasien')) {
                $table->enum('tipe_pasien', ['umum', 'bpjs'])->default('umum')->after('patient_id');
                $table->string('no_bpjs', 30)->nullable()->after('tipe_pasien');
                $table->decimal('subsidi_bpjs', 15, 2)->default(0)->after('no_bpjs');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['tipe_pasien', 'no_bpjs', 'subsidi_bpjs']);
        });
    }
};
