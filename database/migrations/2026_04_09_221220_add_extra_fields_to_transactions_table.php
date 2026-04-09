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
        Schema::table('transactions', function (Blueprint $table) {
            // Informasi Faktur & Order
            $table->date('tgl_order')->nullable()->after('no_transaksi');
            $table->string('no_legalisasi')->nullable()->after('tgl_order');
            $table->date('tgl_legalisasi')->nullable()->after('no_legalisasi');
            $table->date('tgl_faset')->nullable()->after('tgl_legalisasi');
            $table->string('lab')->nullable()->after('tgl_faset');
            $table->string('tempat_faset')->nullable()->after('lab');
            $table->date('tgl_datang_faset')->nullable()->after('tempat_faset');
            $table->date('tgl_selesai_faset')->nullable()->after('tgl_datang_faset');
            $table->date('tgl_selesai_janji')->nullable()->after('tgl_selesai_faset');

            // Refraksi OD
            $table->string('od_sph', 20)->nullable()->after('tgl_selesai_janji');
            $table->string('od_cyl', 20)->nullable()->after('od_sph');
            $table->string('od_axis', 20)->nullable()->after('od_cyl');
            $table->string('od_add', 20)->nullable()->after('od_axis');
            $table->string('od_mpd', 20)->nullable()->after('od_add');

            // Refraksi OS
            $table->string('os_sph', 20)->nullable()->after('od_mpd');
            $table->string('os_cyl', 20)->nullable()->after('os_sph');
            $table->string('os_axis', 20)->nullable()->after('os_cyl');
            $table->string('os_add', 20)->nullable()->after('os_axis');
            $table->string('os_mpd', 20)->nullable()->after('os_add');

            // Data Pasien (Snapshot)
            $table->string('no_bpjs')->nullable()->after('os_mpd');
            $table->string('nama_pasien')->nullable()->after('no_bpjs');
            $table->text('alamat_pasien')->nullable()->after('nama_pasien');
            $table->string('telp_pasien')->nullable()->after('alamat_pasien');
            $table->string('asal_resep')->nullable()->after('telp_pasien');

            // Produk Detail
            $table->string('lensa')->nullable()->after('asal_resep');
            $table->string('kode_frame')->nullable()->after('lensa');
            $table->string('nama_produk')->nullable()->after('kode_frame');
            $table->text('keterangan_frame')->nullable()->after('nama_produk');
            $table->string('seri')->nullable()->after('keterangan_frame');
            $table->string('warna')->nullable()->after('seri');
            $table->integer('typefaktur')->nullable()->after('warna');
            $table->integer('diambil')->nullable()->after('typefaktur');

            // Finansial
            $table->decimal('harga_jual', 15, 2)->default(0)->after('diambil');
            $table->decimal('dp', 15, 2)->default(0)->after('harga_jual');
            $table->decimal('potongan', 15, 2)->default(0)->after('dp');
            $table->decimal('sisa', 15, 2)->default(0)->after('potongan');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'tgl_order', 'no_legalisasi', 'tgl_legalisasi', 'tgl_faset', 'lab', 'tempat_faset',
                'tgl_datang_faset', 'tgl_selesai_faset', 'tgl_selesai_janji',
                'od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_mpd',
                'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_mpd',
                'no_bpjs', 'nama_pasien', 'alamat_pasien', 'telp_pasien', 'asal_resep',
                'lensa', 'kode_frame', 'nama_produk', 'keterangan_frame', 'seri', 'warna', 'typefaktur', 'diambil',
                'harga_jual', 'dp', 'potongan', 'sisa'
            ]);
        });
    }
};
