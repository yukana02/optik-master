<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $frame    = Category::where('nama', 'Frame Kacamata')->value('id');
        $lensa    = Category::where('nama', 'Lensa Kacamata')->value('id');
        $kontak   = Category::where('nama', 'Lensa Kontak')->value('id');
        $cairan   = Category::where('nama', 'Cairan Pembersih')->value('id');
        $aksesoris = Category::where('nama', 'Aksesoris')->value('id');
        $sunglasses = Category::where('nama', 'Kacamata Hitam')->value('id');

        $products = [
            [$frame,  'Frame Rayban RB5154',            'Rayban',        350000, 550000, 15, 3, 'pcs'],
            [$frame,  'Frame Oakley OX8046',             'Oakley',        450000, 750000, 10, 2, 'pcs'],
            [$frame,  'Frame Lokal Minimalis TR-90',     'Lokal',          80000, 180000, 25, 5, 'pcs'],
            [$lensa,  'Lensa SV 1.56 CR-39 Anti Refleksi','Essilor',      120000, 220000, 50, 10, 'pasang'],
            [$lensa,  'Lensa Progressive 1.67 HMC',      'Hoya',          450000, 850000, 20,  5, 'pasang'],
            [$lensa,  'Lensa Blueray 1.56',              'Nikon',         150000, 280000, 40,  8, 'pasang'],
            [$lensa,  'Lensa Photochromic 1.56',         'Transitions',   250000, 450000, 15,  3, 'pasang'],
            [$kontak, 'Acuvue Oasys 1-Day 30pcs',        'Acuvue',        220000, 320000, 30,  5, 'kotak'],
            [$kontak, 'Bausch & Lomb Ultra Monthly',     'B&L',           130000, 200000, 25,  5, 'pasang'],
            [$cairan, 'ReNu MPS 360ml',                  'B&L',            55000,  95000, 40,  8, 'botol'],
            [$cairan, 'Opti-Free Puremoist 300ml',       'Alcon',          60000, 105000, 35,  8, 'botol'],
            [$aksesoris,'Case Kacamata Premium',         'Generic',        15000,  35000, 50, 10, 'pcs'],
            [$aksesoris,'Kain Lap Microfiber 15x15cm',   'Generic',         3000,  10000,100, 20, 'pcs'],
            [$sunglasses,'Rayban Aviator RB3025',        'Rayban',        600000, 950000,  8,  2, 'pcs'],
            [$sunglasses,'Sunglasses Polarized Sport',   'Lokal',          90000, 185000, 20,  4, 'pcs'],
        ];

        $count = 0;
        foreach ($products as [$catId, $nama, $merek, $beli, $jual, $stok, $min, $satuan]) {
            if (!$catId) continue;
            if (!Product::where('nama', $nama)->exists()) {
                Product::create([
                    'category_id'  => $catId,
                    'kode_produk'  => Product::generateKode(),
                    'nama'         => $nama,
                    'merek'        => $merek,
                    'harga_beli'   => $beli,
                    'harga_jual'   => $jual,
                    'stok'         => $stok,
                    'stok_minimum' => $min,
                    'satuan'       => $satuan,
                    'is_active'    => true,
                ]);
                $count++;
            }
        }
        $this->command->info("✅ {$count} produk selesai.");
    }
}
