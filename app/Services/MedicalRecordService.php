<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Models\Refraction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MedicalRecordService
{
    public function __construct()
    {
        //
    }

    /**
     * Create a medical record + refraction if the refraction data differs from the latest.
     */
    public function createIfDifferent($patientId, array $data)
    {
        // Get the latest medical record with its refraction
        $lastRecord = MedicalRecord::where('patient_id', $patientId)
            ->latest('visit_date')
            ->with('refraction')
            ->first();

        // Extract refraction fields from input
        $refractionData = $this->normalize([
            'doctor_name' => $data['nama_dokter'] ?? null,
            'diagnosis' => $data['diagnosis'] ?? null,
            'exam_date' => $data['tgl_resep'] ?? null,
            'od_sph' => $data['od_sph'] ?? null,
            'od_cyl' => $data['od_cyl'] ?? null,
            'od_axis' => $data['od_axis'] ?? null,
            'od_add' => $data['od_add'] ?? null,
            'od_pd' => $data['od_mpd'] ?? null,
            'od_prism' => $data['od_prism'] ?? null,
            'os_sph' => $data['os_sph'] ?? null,
            'os_cyl' => $data['os_cyl'] ?? null,
            'os_axis' => $data['os_axis'] ?? null,
            'os_add' => $data['os_add'] ?? null,
            'os_pd' => $data['os_mpd'] ?? null,
            'os_prism' => $data['os_prism'] ?? null,
        ]);

        // Compare with latest refraction if exists
        if ($lastRecord && $lastRecord->refraction) {
            $lastRefraction = $this->normalize($lastRecord->refraction->only([
                'doctor_name',
                'od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_pd', 'od_prism',
                'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_pd', 'os_prism',
            ]));

            $compareFields = [
                'doctor_name', 
                'od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_pd', 'od_prism',
                'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_pd', 'os_prism',
            ];

            $isDifferent = false;
            foreach ($compareFields as $field) {
                if (($refractionData[$field] ?? null) != ($lastRefraction[$field] ?? null)) {
                    $isDifferent = true;
                    break;
                }
            }

            if (!$isDifferent) {
                return $lastRecord;
            }
        }

        // Create new medical record
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $patientId,
            'user_id' => Auth::id(),
            'visit_date' => $data['tgl_resep'] ?? now()->toDateString(),
            'complaint' => $data['complaint'] ?? null,
        ]);

        // Create refraction sub-record
        $medicalRecord->refraction()->create($refractionData);

        return $medicalRecord;
    }

    private function normalize(array $data)
    {
        foreach ($data as $key => $value) {
            if ($value instanceof Carbon) {
                $data[$key] = $value->format('Y-m-d');
            } elseif (is_numeric($value)) {
                $data[$key] = (float) $value;
            } elseif ($value === null || $value === '') {
                $data[$key] = null;
            }
        }

        return $data;
    }
}
