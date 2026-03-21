<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id', 'product_id', 'nama_produk', 'qty', 'harga_beli', 'subtotal',
    ];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function product()       { return $this->belongsTo(Product::class); }
}
