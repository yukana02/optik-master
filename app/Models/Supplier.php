<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_supplier', 'nama', 'kontak_person', 'telepon', 'email', 'alamat', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function generateKode(): string
    {
        $last = static::withTrashed()->latest('id')->first();
        $num  = $last ? ((int) substr($last->kode_supplier, 3)) + 1 : 1;
        return 'SUP' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
