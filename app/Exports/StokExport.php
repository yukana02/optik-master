<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StokExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string { return 'Laporan Stok'; }

    public function collection()
    {
        return Product::with('category')
            ->where('is_active', true)
            ->orderBy('stok')
            ->get()
            ->map(fn($p) => [
                'Kode'        => $p->kode_produk,
                'Nama'        => $p->nama,
                'Kategori'    => $p->category->nama ?? '-',
                'Merek'       => $p->merek ?? '-',
                'Stok'        => $p->stok,
                'Min. Stok'   => $p->stok_minimum,
                'Status'      => $p->stok == 0 ? 'Habis' : ($p->stok_menipis ? 'Menipis' : 'Aman'),
                'Harga Beli'  => (float) $p->harga_beli,
                'Harga Jual'  => (float) $p->harga_jual,
                'Nilai Stok'  => (float) ($p->stok * $p->harga_beli),
            ]);
    }

    public function headings(): array
    {
        return [
            ['Laporan Stok Produk — Optik Store'],
            ['Diekspor: ' . now()->format('d M Y H:i')],
            [],
            ['Kode', 'Nama Produk', 'Kategori', 'Merek', 'Stok', 'Min. Stok',
             'Status', 'Harga Beli (Rp)', 'Harga Jual (Rp)', 'Nilai Stok (Rp)'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']],
            2 => ['font' => ['italic' => true, 'color' => ['rgb' => '666666']], 'alignment' => ['horizontal' => 'center']],
            4 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E2A5E']],
                  'alignment' => ['horizontal' => 'center']],
        ];
    }
}
