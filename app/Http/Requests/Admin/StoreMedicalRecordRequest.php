<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'patient_id'          => 'required|exists:patients,id',
            'tanggal_kunjungan'   => 'required|date',
            'keluhan'             => 'nullable|string|max:255',
            'od_sph'              => 'nullable|numeric|between:-30,30',
            'od_cyl'              => 'nullable|numeric|between:-10,10',
            'od_axis'             => 'nullable|integer|between:0,180',
            'od_add'              => 'nullable|numeric|between:0,5',
            'od_pd'               => 'nullable|numeric|between:20,40',
            'od_vis'              => 'nullable|numeric|between:0,2',
            'os_sph'              => 'nullable|numeric|between:-30,30',
            'os_cyl'              => 'nullable|numeric|between:-10,10',
            'os_axis'             => 'nullable|integer|between:0,180',
            'os_add'              => 'nullable|numeric|between:0,5',
            'os_pd'               => 'nullable|numeric|between:20,40',
            'os_vis'              => 'nullable|numeric|between:0,2',
            'pd_total'            => 'nullable|numeric|between:40,80',
            'jenis_lensa'         => 'nullable|string|max:100',
            'rekomendasi_frame'   => 'nullable|string|max:100',
            'catatan'             => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required'        => 'Pasien wajib dipilih.',
            'tanggal_kunjungan.required' => 'Tanggal kunjungan wajib diisi.',
        ];
    }
}
