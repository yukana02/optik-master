<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'no_po', 'supplier_id', 'user_id', 'tanggal_po', 'tanggal_terima',
        'total_harga', 'status', 'catatan',
    ];

    protected $casts = [
        'tanggal_po'     => 'date',
        'tanggal_terima' => 'date',
        'total_harga'    => 'decimal:2',
    ];

    public static function generateNomor(): string
    {
        $prefix = 'PO' . date('Ymd');
        $last   = static::where('no_po', 'like', $prefix . '%')->latest('id')->first();
        $num    = $last ? ((int) substr($last->no_po, -4)) + 1 : 1;
        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function supplier()   { return $this->belongsTo(Supplier::class); }
    public function user()       { return $this->belongsTo(User::class); }
    public function items()      { return $this->hasMany(PurchaseOrderItem::class); }
}
