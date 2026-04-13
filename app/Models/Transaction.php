<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Identitas
        'no_transaksi',
        'patient_id',
        'user_id',
        'medical_record_id',

        // Tanggal
        'tgl_order',
        'tgl_faktur',
        'tgl_selesai_janji',

        // Status tambahan
        'typefaktur',
        'diambil',

        // Harga
        'harga_jual',
        'potongan',
        'potongan_bpjs',
        'total_bayar',

        // DP SYSTEM
        'dp',
        'sisa',

        // Pembayaran
        'bayar',
        'kembalian',
        'diskon_persen',

        // Metode & status
        'metode_bayar',
        'status',

        // Snapshot Pasien
        'no_bpjs',
        'nama_pasien',
        'alamat_pasien',
        'telp_pasien',
        'asal_resep',

        // Snapshot Refraksi
        'od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_mpd', 'od_prism',
        'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_mpd', 'os_prism',

        // Snapshot Product
        'lensa', 'kode_frame', 'nama_produk', 'keterangan_frame', 'seri', 'warna',

        // Snapshot Produksi Lab
        'no_legalisasi', 'tgl_legalisasi', 'tgl_faset', 'lab', 'tempat_faset',
        'tgl_datang_faset', 'tgl_selesai_faset',

        // JSON/Extra
        'catatan',
        'resep',
        'jadwal',
        'tambahan',
    ];

    protected $casts = [
        'resep' => 'array',
        'jadwal' => 'array',
        'tambahan' => 'array',

        'tgl_order' => 'date',
        'tgl_faktur' => 'date',
    ];

    // Auto-generate nomor transaksi
    public static function generateNomor(): string
    {
        $prefix = 'TRX' . date('Ymd');
        // Using withTrashed to ensure we don't duplicate numbers even with soft-deleted records
        $last = static::withTrashed()->where('no_transaksi', 'like', $prefix . '%')
            ->orderBy('no_transaksi', 'desc')
            ->first();
            
        $num = 1;
        if ($last) {
            $lastNum = (int)substr($last->no_transaksi, -4);
            $num = $lastNum + 1;
        }
        
        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    // Relasi
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function kasir()
    {
        return $this->belongsTo(User::class , 'user_id');
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
