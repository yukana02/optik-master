<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Pasien
            'patient.view','patient.create','patient.edit','patient.delete',
            // Rekam Medis
            'medical_record.view','medical_record.create','medical_record.edit','medical_record.delete',
            // Produk & Kategori
            'product.view','product.create','product.edit','product.delete',
            'category.view','category.create','category.edit','category.delete',
            // Transaksi
            'transaction.view','transaction.create','transaction.edit',
            // Laporan
            'report.view',
            // Supplier & PO
            'supplier.view','supplier.create','supplier.edit','supplier.delete',
            'purchase_order.view','purchase_order.create','purchase_order.edit',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // Super Admin — semua permission
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions($permissions);

        // Admin — hampir semua, kecuali delete pasien & hapus user
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'patient.view','patient.create','patient.edit',
            'medical_record.view','medical_record.create','medical_record.edit',
            'product.view','product.create','product.edit',
            'category.view','category.create','category.edit',
            'transaction.view','transaction.create','transaction.edit',
            'report.view',
            'supplier.view','supplier.create','supplier.edit',
            'purchase_order.view','purchase_order.create','purchase_order.edit',
        ]);

        // Dokter — pasien + rekam medis
        $dokter = Role::firstOrCreate(['name' => 'dokter']);
        $dokter->syncPermissions([
            'patient.view','patient.create','patient.edit',
            'medical_record.view','medical_record.create','medical_record.edit',
        ]);

        // Kasir — transaksi + lihat data
        $kasir = Role::firstOrCreate(['name' => 'kasir']);
        $kasir->syncPermissions([
            'patient.view','patient.create',
            'medical_record.view',
            'product.view','category.view',
            'transaction.view','transaction.create',
        ]);

        $this->command->info('✅ Roles & permissions selesai (termasuk supplier & PO).');
    }
}
