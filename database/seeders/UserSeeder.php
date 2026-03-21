<?php
// ─────────────────────────────────────────────────────────────
// UserSeeder.php
// ─────────────────────────────────────────────────────────────
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name'=>'Super Admin',     'email'=>'superadmin@optik.com', 'role'=>'super_admin'],
            ['name'=>'Admin Optik',     'email'=>'admin@optik.com',      'role'=>'admin'],
            ['name'=>'Dr. Budi Santoso','email'=>'dokter@optik.com',     'role'=>'dokter'],
            ['name'=>'Siti Kasir',      'email'=>'kasir@optik.com',      'role'=>'kasir'],
        ];

        foreach ($users as $data) {
            $role = $data['role']; unset($data['role']);
            $data['password'] = Hash::make('password');
            $user = User::updateOrCreate(['email' => $data['email']], $data);
            $user->syncRoles([$role]);
        }
        $this->command->info('✅ User default selesai. Password: password');
    }
}
