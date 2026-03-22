<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'modul', 'aksi', 'referensi_id', 'ip_address', 'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper statis untuk mencatat aktivitas dari controller manapun.
     */
    public static function catat(string $modul, string $aksi, ?int $referensiId = null): self
    {
        return self::create([
            'user_id'       => auth()->id(),
            'modul'         => $modul,
            'aksi'          => $aksi,
            'referensi_id'  => $referensiId,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);
    }
}
