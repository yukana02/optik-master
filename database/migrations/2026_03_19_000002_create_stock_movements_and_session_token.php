<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel mutasi stok
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('tipe', ['masuk', 'keluar', 'retur', 'adjustment']);
            $table->integer('qty');
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->string('keterangan')->nullable();
            $table->nullableMorphs('referensi'); // referensi_type + referensi_id
            $table->timestamps();
        });

        // 2. Session token untuk deteksi login ganda
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'session_token')) {
                $table->string('session_token', 100)->nullable()->after('remember_token');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('session_token');
        });
    }
};
