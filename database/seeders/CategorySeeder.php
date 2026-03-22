<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['nama'=>'Frame Kacamata',   'deskripsi'=>'Rangka kacamata fullrim, halfrim, rimless'],
            ['nama'=>'Lensa Kacamata',   'deskripsi'=>'Lensa single vision, progressive, bifocal, anti-reflective'],
            ['nama'=>'Lensa Kontak',     'deskripsi'=>'Soft lens harian, bulanan, tahunan'],
            ['nama'=>'Cairan Pembersih', 'deskripsi'=>'Cairan pembersih lensa kontak dan kacamata'],
            ['nama'=>'Aksesoris',        'deskripsi'=>'Tempat kacamata, kain lap, tali, sekrup'],
            ['nama'=>'Kacamata Hitam',   'deskripsi'=>'Sunglasses UV protection'],
            ['nama'=>'Kacamata Safety',  'deskripsi'=>'Kacamata pelindung industri dan olahraga'],
        ];

        foreach ($cats as $c) {
            Category::updateOrCreate(['nama' => $c['nama']], array_merge($c, ['is_active' => true]));
        }
        $this->command->info('✅ Kategori selesai.');
    }
}
