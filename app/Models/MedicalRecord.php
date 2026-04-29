<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id', 'user_id', 'nama_dokter', 'tanggal_kunjungan', 'keluhan', 'diagnosis',
        'od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_pd', 'od_vis', 'od_prism',
        'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_pd', 'os_vis', 'os_prism',
        'pd_total', 'jenis_lensa', 'rekomendasi_frame', 'catatan',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
    ];

    // Format nilai resep dengan tanda + atau -
    public function formatResep(?float $val): string
    {
        if (is_null($val)) return '-';
        return ($val >= 0 ? '+' : '') . number_format($val, 2);
    }

    // Relasi
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
