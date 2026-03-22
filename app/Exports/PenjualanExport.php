<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PenjualanExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected string $from;
    protected string $to;
    protected string $tipePasien;

    public function __construct(string $from, string $to, string $tipePasien = '')
    {
        $this->from       = $from;
        $this->to         = $to;
        $this->tipePasien = $tipePasien;
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }

    public function collection()
    {
        $query = Transaction::with(['patient', 'kasir', 'items'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$this->from, $this->to])
            ->where('status', 'lunas');

        if ($this->tipePasien) {
            $query->where('tipe_pasien', $this->tipePasien);
        }

        return $query->latest()->get()->map(fn($trx, $i) => [
            'No'            => $i + 1,
            'No. Transaksi' => $trx->no_transaksi,
            'Tanggal'       => $trx->created_at->format('d/m/Y H:i'),
            'Pasien'        => $trx->patient->nama ?? 'Umum',
            'Tipe'          => strtoupper($trx->tipe_pasien ?? 'umum'),
            'No. BPJS'      => $trx->no_bpjs ?? '-',
            'Items'         => $trx->items->sum('qty'),
            'Subtotal'      => (float) $trx->total_harga,
            'Diskon'        => (float) $trx->diskon_nominal,
            'Subsidi BPJS'  => (float) ($trx->subsidi_bpjs ?? 0),
            'Total Bayar'   => (float) $trx->total_bayar,
            'Metode'        => ucfirst($trx->metode_bayar),
            'Kasir'         => $trx->kasir->name ?? '-',
        ]);
    }

    public function headings(): array
    {
        $tipeLbl = $this->tipePasien ? ' [' . strtoupper($this->tipePasien) . ']' : '';
        return [
            ['Laporan Penjualan — Optik Store' . $tipeLbl],
            ['Periode: ' . $this->from . ' s/d ' . $this->to . '   |   Diekspor: ' . now()->format('d M Y H:i')],
            [],
            ['No', 'No. Transaksi', 'Tanggal', 'Pasien', 'Tipe', 'No. BPJS', 'Items',
             'Subtotal (Rp)', 'Diskon (Rp)', 'Subsidi BPJS (Rp)', 'Total Bayar (Rp)', 'Metode', 'Kasir'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Merge title rows
        $sheet->mergeCells('A1:M1');
        $sheet->mergeCells('A2:M2');

        return [
            1 => ['font' => ['bold' => true, 'size' => 14],
                  'alignment' => ['horizontal' => 'center']],
            2 => ['font' => ['size' => 10],
                  'alignment' => ['horizontal' => 'center'],
                  'font' => ['italic' => true, 'color' => ['rgb' => '666666']]],
            4 => ['font'       => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill'       => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E2A5E']],
                  'alignment'  => ['horizontal' => 'center']],
        ];
    }
}
