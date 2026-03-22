<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            ['nama'=>'Budi Prasetyo',    'tanggal_lahir'=>'1985-03-15','jenis_kelamin'=>'L','no_hp'=>'08123456789','email'=>'budi@email.com',    'alamat'=>'Jl. Merdeka 12, Jakarta Pusat','riwayat_penyakit'=>'Hipertensi ringan'],
            ['nama'=>'Sari Dewi',        'tanggal_lahir'=>'1992-07-22','jenis_kelamin'=>'P','no_hp'=>'08234567890','email'=>'sari@email.com',     'alamat'=>'Jl. Sudirman 45, Jakarta Selatan','riwayat_penyakit'=>null],
            ['nama'=>'Ahmad Fauzi',      'tanggal_lahir'=>'1978-11-08','jenis_kelamin'=>'L','no_hp'=>'08345678901','email'=>null,                 'alamat'=>'Jl. Kebon Jeruk 7, Jakarta Barat','riwayat_penyakit'=>'Diabetes Mellitus tipe 2'],
            ['nama'=>'Rina Kusumawati',  'tanggal_lahir'=>'2000-04-17','jenis_kelamin'=>'P','no_hp'=>'08456789012','email'=>'rina@email.com',     'alamat'=>'Jl. Pahlawan 33, Bekasi','riwayat_penyakit'=>null],
            ['nama'=>'Hendra Gunawan',   'tanggal_lahir'=>'1970-09-30','jenis_kelamin'=>'L','no_hp'=>'08567890123','email'=>'hendra@email.com',   'alamat'=>'Jl. Raya Bogor 88, Depok','riwayat_penyakit'=>'Glaukoma stadium awal'],
        ];

        $count = 0;
        foreach ($patients as $data) {
            $exists = $data['email']
                ? Patient::where('email', $data['email'])->exists()
                : Patient::where('nama', $data['nama'])->exists();
            if (!$exists) {
                $data['no_rm'] = Patient::generateNoRM();
                Patient::create($data);
                $count++;
            }
        }
        $this->command->info("✅ {$count} pasien selesai.");
    }
}
