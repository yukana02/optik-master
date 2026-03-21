<?php

namespace App\Exports;

use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProdukTerlarisExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected string $from;
    protected string $to;

    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    public function title(): string { return 'Produk Terlaris'; }

    public function collection()
    {
        return TransactionItem::with('product.category')
            ->whereHas('transaction', fn($q) =>
                $q->whereBetween(DB::raw('DATE(created_at)'), [$this->from, $this->to])
                  ->where('status', 'lunas')
            )
            ->select('product_id',
                DB::raw('SUM(qty) as total_terjual'),
                DB::raw('SUM(subtotal) as total_pendapatan')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_terjual')
            ->take(15)
            ->get()
            ->map(fn($p, $i) => [
                'Rank'              => $i + 1,
                'Kode Produk'       => $p->product->kode_produk ?? '-',
                'Nama Produk'       => $p->product->nama ?? '-',
                'Kategori'          => $p->product->category->nama ?? '-',
                'Total Terjual'     => (int) $p->total_terjual,
                'Total Pendapatan'  => (float) $p->total_pendapatan,
            ]);
    }

    public function headings(): array
    {
        return [
            ['Laporan Produk Terlaris — Optik Store'],
            ['Periode: ' . $this->from . ' s/d ' . $this->to . '   |   Diekspor: ' . now()->format('d M Y H:i')],
            [],
            ['Rank', 'Kode Produk', 'Nama Produk', 'Kategori', 'Total Terjual', 'Total Pendapatan (Rp)'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']],
            2 => ['font' => ['italic' => true, 'color' => ['rgb' => '666666']], 'alignment' => ['horizontal' => 'center']],
            4 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E2A5E']],
                  'alignment' => ['horizontal' => 'center']],
        ];
    }
}
