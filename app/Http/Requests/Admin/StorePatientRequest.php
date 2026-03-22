<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama'             => 'required|string|max:100',
            'tanggal_lahir'    => 'nullable|date|before:today',
            'jenis_kelamin'    => 'nullable|in:L,P',
            'no_hp'            => 'nullable|string|max:20',
            'no_bpjs'          => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'alamat'           => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'         => 'Nama pasien wajib diisi.',
            'tanggal_lahir.before'  => 'Tanggal lahir harus sebelum hari ini.',
            'email.email'           => 'Format email tidak valid.',
        ];
    }
}
