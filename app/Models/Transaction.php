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

        // Status tambahan
        'tipe_faktur',
        'diambil',

        // Harga
        'total_harga',
        'diskon_persen',
        'diskon_nominal',
        'total_bayar',

        // DP SYSTEM
        'dp',
        'sisa',

        // Pembayaran
        'bayar',
        'kembalian',

        // Metode & status
        'metode_bayar',
        'status',

        // Lainnya
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
