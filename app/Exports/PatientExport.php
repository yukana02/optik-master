<?php

namespace App\Exports;

use App\Models\Patient;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PatientExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected ?Request $request;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Patient::query()
            ->when($this->request?->filled('search'), function ($q) {
                $q->where('nama', 'like', '%' . $this->request->search . '%');
            })
            ->orderBy('nama')
            ->get();

        return $query->map(function ($p) {
            return [
                $p->no_rm,
                $p->nama,
                $p->tanggal_lahir?->format('Y-m-d') ?? '',
                $p->jenis_kelamin ?? '',
                $p->no_hp ?? '',
                $p->no_bpjs ?? '',
                $p->email ?? '',
                $p->alamat ?? '',
                $p->riwayat_penyakit ?? '',
                $p->medicalRecords()->count(),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No. RM',
            'Nama Pasien',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'No. HP',
            'No. BPJS',
            'Email',
            'Alamat',
            'Riwayat Penyakit',
            'Jumlah Kunjungan',
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
            'A' => 14, 'B' => 28, 'C' => 14,
            'D' => 14, 'E' => 16, 'F' => 18,
            'G' => 26, 'H' => 30, 'I' => 30,
            'J' => 18,
        ];
    }
}