<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected ?Request $request;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Product::with('category')
            ->when($this->request?->filled('category_id'), function ($q) {
                $q->where('category_id', $this->request->category_id);
            })
            ->when($this->request?->filled('search'), function ($q) {
                $q->where('nama', 'like', '%' . $this->request->search . '%');
            })
            ->when($this->request?->filled('stok'), function ($q) {
                if ($this->request->stok === 'menipis') {
                    $q->whereColumn('stok', '<=', 'stok_minimum')->where('stok', '>', 0);
                } elseif ($this->request->stok === 'habis') {
                    $q->where('stok', 0);
                }
            })
            ->orderBy('category_id')->orderBy('nama')
            ->get();

        return $query->map(function ($p) {
            return [
                $p->kode_produk,
                $p->category->nama ?? '-',
                $p->nama,
                $p->merek ?? '',
                $p->deskripsi ?? '',
                $p->harga_beli,
                $p->harga_jual,
                $p->stok,
                $p->stok_minimum,
                $p->satuan,
                $p->is_active ? 'Aktif' : 'Nonaktif',
                $p->stok * $p->harga_beli,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Kode Produk',
            'Kategori',
            'Nama Produk',
            'Merek',
            'Deskripsi',
            'Harga Beli',
            'Harga Jual',
            'Stok',
            'Stok Minimum',
            'Satuan',
            'Status',
            'Nilai Stok (Rp)',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E2A5E']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 20,
            'C' => 30,
            'D' => 16,
            'E' => 30,
            'F' => 14,
            'G' => 14,
            'H' => 10,
            'I' => 14,
            'J' => 10,
            'K' => 12,
            'L' => 18,
        ];
    }
}
