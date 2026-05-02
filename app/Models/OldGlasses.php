<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OldGlasses extends Model
{
    protected $fillable = [
        'medical_record_id',
        'od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_pd', 'od_prism',
        'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_pd', 'os_prism',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
