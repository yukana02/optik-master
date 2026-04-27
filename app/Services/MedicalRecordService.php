<?php

namespace App\Services;

use App\Models\MedicalRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MedicalRecordService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createIfDifferent($patientId, array $data)
    {
        // Mapping field agar konsisten
        $data['tanggal_kunjungan'] = $data['tgl_resep'] ?? null;
        unset($data['tgl_resep']);

        $lastRecord = MedicalRecord::where('patient_id', $patientId)
            ->latest()
            ->first();

        // Normalisasi
        $data = $this->normalize($data);

        if ($lastRecord) {
            $lastData = $this->normalize($lastRecord->only([
                'od_sph',
                'od_cyl',
                'od_axis',
                'od_add',
                'od_pd',
                'od_vis',
                'os_sph',
                'os_cyl',
                'os_axis',
                'os_add',
                'os_pd',
                'os_vis',
                'nama_dokter',
                'tanggal_kunjungan',
            ]));

            $compareData = [
                'od_sph' => $data['od_sph'] ?? null,
                'od_cyl' => $data['od_cyl'] ?? null,
                'od_axis' => $data['od_axis'] ?? null,
                'od_add' => $data['od_add'] ?? null,
                'od_pd' => $data['od_pd'] ?? null,
                'od_vis' => $data['od_vis'] ?? null,

                'os_sph' => $data['os_sph'] ?? null,
                'os_cyl' => $data['os_cyl'] ?? null,
                'os_axis' => $data['os_axis'] ?? null,
                'os_add' => $data['os_add'] ?? null,
                'os_pd' => $data['os_pd'] ?? null,
                'os_vis' => $data['os_vis'] ?? null,

                'nama_dokter' => $data['nama_dokter'] ?? null,
                'tanggal_kunjungan' => $data['tanggal_kunjungan'] ?? null,
            ];

            if ($lastData == $compareData) {
                return $lastRecord;
            }
        }

        $data['user_id'] = Auth::id();
        $data['patient_id'] = $patientId;

        return MedicalRecord::create($data);
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
