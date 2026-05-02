<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'medical_record_id',
        'doctor_name',
        'diagnosis',
        'exam_date',
        'notes',
        // OD
        'od_sph', 'od_cyl', 'od_axis', 'od_prism', 'od_add', 'od_mpd', 'od_cc',
        // OS
        'os_sph', 'os_cyl', 'os_axis', 'os_prism', 'os_add', 'os_pd', 'os_cc',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
