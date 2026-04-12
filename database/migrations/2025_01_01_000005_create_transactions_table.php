<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // identitas transaksi
            $table->string('no_transaksi', 25)->unique();
            $table->foreignId('patient_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('medical_record_id')->nullable()->constrained()->onDelete('set null');

            // tanggal transaksi
            $table->date('tgl_order');
            $table->date('tgl_faktur');

            // status tambahan
            $table->tinyInteger('tipe_faktur')->nullable();
            $table->tinyInteger('diambil')->default(0);

            // harga pembayaran
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->decimal('diskon_nominal', 15, 2)->default(0);
            $table->decimal('total_bayar', 15, 2)->default(0);

            // dp system
            $table->decimal('dp', 15, 2)->default(0);
            $table->decimal('sisa', 15, 2)->default(0);

            // pembayaran langsung
            $table->decimal('bayar', 15, 2)->default(0);
            $table->decimal('kembalian', 15, 2)->default(0);

            // metode pembayaran & status
            $table->enum('metode_bayar', ['tunai', 'transfer', 'qris', 'debit', 'kredit'])->default('tunai');
            $table->enum('status', ['pending', 'dp', 'lunas', 'batal']);
            $table->text('catatan')->nullable();
            
            // JSON fields untuk data resep, jadwal, dan tambahan
            $table->json('resep')->nullable();
            $table->json('jadwal')->nullable();
            $table->json('tambahan')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nama_produk', 150);
            $table->string('seri', 100)->nullable();
            $table->string('warna', 100)->nullable();
            $table->integer('qty');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transactions');
    }
};
