<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan PENTING — jangan diubah:
     * 1. RolesAndPermissionsSeeder  (harus pertama)
     * 2. UserSeeder                 (butuh roles)
     * 3. CategorySeeder             (harus sebelum ProductSeeder)
     * 4. ProductSeeder              (butuh category_id)
     * 5. PatientSeeder              (independen)
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            PatientSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('🎉 Semua seeder berhasil! Akun login:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['super_admin', 'superadmin@optik.com', 'password'],
                ['admin',       'admin@optik.com',      'password'],
                ['dokter',      'dokter@optik.com',     'password'],
                ['kasir',       'kasir@optik.com',      'password'],
            ]
        );
    }
}
