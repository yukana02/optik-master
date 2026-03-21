<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Http\Request;

class MutasiStokExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function title(): string { return 'Mutasi Stok'; }

    public function collection()
    {
        $query = StockMovement::with(['product', 'user'])->latest();

        if ($this->request->filled('product_id')) $query->where('product_id', $this->request->product_id);
        if ($this->request->filled('tipe'))        $query->where('tipe', $this->request->tipe);
        if ($this->request->filled('from'))        $query->whereDate('created_at', '>=', $this->request->from);
        if ($this->request->filled('to'))          $query->whereDate('created_at', '<=', $this->request->to);

        return $query->get()->map(fn($m) => [
            'Tanggal'       => $m->created_at->format('d/m/Y H:i'),
            'Produk'        => $m->product->nama ?? '-',
            'Kode'          => $m->product->kode_produk ?? '-',
            'Tipe'          => ucfirst($m->tipe),
            'Qty'           => $m->qty,
            'Stok Sebelum'  => $m->stok_sebelum,
            'Stok Sesudah'  => $m->stok_sesudah,
            'Keterangan'    => $m->keterangan ?? '-',
            'Petugas'       => $m->user->name ?? '-',
        ]);
    }

    public function headings(): array
    {
        return [
            ['Laporan Mutasi Stok — Optik Store'],
            ['Diekspor: ' . now()->format('d M Y H:i')],
            [],
            ['Tanggal', 'Nama Produk', 'Kode', 'Tipe', 'Qty',
             'Stok Sebelum', 'Stok Sesudah', 'Keterangan', 'Petugas'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']],
            2 => ['font' => ['italic' => true, 'color' => ['rgb' => '666666']], 'alignment' => ['horizontal' => 'center']],
            4 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E2A5E']],
                  'alignment' => ['horizontal' => 'center']],
        ];
    }
}
