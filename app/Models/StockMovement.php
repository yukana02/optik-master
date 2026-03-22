<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'user_id', 'tipe', 'qty',
        'stok_sebelum', 'stok_sesudah', 'keterangan',
        'referensi_type', 'referensi_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referensi()
    {
        return $this->morphTo();
    }

    // Helper: catat mutasi secara langsung
    public static function catat(
        Product $product,
        string  $tipe,
        int     $qty,
        string  $keterangan = '',
        mixed   $referensi  = null
    ): self {
        $stokSebelum = $product->stok;

        $movement = self::create([
            'product_id'      => $product->id,
            'user_id'         => auth()->id(),
            'tipe'            => $tipe,
            'qty'             => $qty,
            'stok_sebelum'    => $stokSebelum,
            'stok_sesudah'    => $tipe === 'keluar'
                ? $stokSebelum - $qty
                : $stokSebelum + $qty,
            'keterangan'      => $keterangan,
            'referensi_type'  => $referensi ? get_class($referensi) : null,
            'referensi_id'    => $referensi?->id,
        ]);

        return $movement;
    }
}
