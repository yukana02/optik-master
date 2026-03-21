<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PatientExport;
use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Models\{Category, Patient, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// ─── Template inline classes ────────────────────────────────────────
class TemplateProdukExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected string $kategoris;

    public function __construct(string $kategoris)
    {
        $this->kategoris = $kategoris;
    }

    public function array(): array
    {
        return [
            [
                $this->kategoris ? explode(' | ', $this->kategoris)[0] : 'Contoh Kategori',
                'Contoh Nama Produk',
                'Ray-Ban',
                'Deskripsi opsional',
                50000,
                100000,
                10,
                5,
                'pcs',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nama_kategori',
            'nama',
            'merek',
            'deskripsi',
            'harga_beli',
            'harga_jual',
            'stok',
            'stok_minimum',
            'satuan',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header baris
        $sheet->getRowDimension(1)->setRowHeight(22);
        // Keterangan tersedia kategori di baris 3
        $sheet->setCellValue('A3', '* Kolom nama_kategori wajib sesuai nama kategori di sistem: ' . $this->kategoris);
        $sheet->mergeCells('A3:I3');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('A3')->getFont()->getColor()->setRGB('888888');

        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E2A5E']],
                'alignment' => ['horizontal' => 'center'],
            ],
            2 => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EFF3FF']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, 'B' => 30, 'C' => 16,
            'D' => 28, 'E' => 14, 'F' => 14,
            'G' => 10, 'H' => 14, 'I' => 10,
        ];
    }
}

class TemplatePasienExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                'Budi Santoso',
                '1990-05-15',
                'L',
                '08123456789',
                '',
                'budi@email.com',
                'Jl. Merdeka No. 1, Jakarta',
                'Mata minus',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nama',
            'tanggal_lahir',
            'jenis_kelamin',
            'no_hp',
            'no_bpjs',
            'email',
            'alamat',
            'riwayat_penyakit',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->setCellValue('A3', '* jenis_kelamin: L (Laki-laki) atau P (Perempuan). tanggal_lahir format: YYYY-MM-DD.');
        $sheet->mergeCells('A3:H3');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('A3')->getFont()->getColor()->setRGB('888888');

        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E2A5E']],
                'alignment' => ['horizontal' => 'center'],
            ],
            2 => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EFF3FF']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 28, 'B' => 14, 'C' => 14,
            'D' => 16, 'E' => 18, 'F' => 26,
            'G' => 32, 'H' => 28,
        ];
    }
}

// ────────────────────────────────────────────────────────────────────

class ImportExportController extends Controller
{
    // ══════════════════════════════════════════════════════
    //  HALAMAN UTAMA IMPORT
    // ══════════════════════════════════════════════════════
    public function index()
    {
        return view('admin.import.index');
    }

    // ══════════════════════════════════════════════════════
    //  DOWNLOAD TEMPLATE (EXCEL)
    // ══════════════════════════════════════════════════════
    public function downloadTemplate(string $type)
    {
        return match ($type) {
            'produk' => $this->templateProduk(),
            'pasien' => $this->templatePasien(),
            default  => abort(404),
        };
    }

    private function templateProduk()
    {
        $kategoris = Category::where('is_active', true)->pluck('nama')->implode(' | ');
        return Excel::download(
            new TemplateProdukExport($kategoris),
            'template-import-produk.xlsx'
        );
    }

    private function templatePasien()
    {
        return Excel::download(
            new TemplatePasienExport(),
            'template-import-pasien.xlsx'
        );
    }

    // ══════════════════════════════════════════════════════
    //  IMPORT PRODUK (dari Excel .xlsx)
    // ══════════════════════════════════════════════════════
    public function importProduk(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
            'mode' => 'required|in:tambah,update,replace',
        ]);

        $mode = $request->mode;

        // Baca xlsx jadi array (baris 0 = header, mulai data dari baris 1)
        $rows = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
            public function array(array $array): array { return $array; }
        }, $request->file('file'));

        if (empty($rows[0])) {
            return back()->with('error', 'File Excel kosong atau tidak dapat dibaca.');
        }

        $allRows = $rows[0];
        if (count($allRows) < 1) {
            return back()->with('error', 'File Excel tidak memiliki baris data.');
        }

        // Baris pertama = header
        $rawHeader = $allRows[0];
        $header    = array_map(fn($h) => strtolower(trim((string)$h)), $rawHeader);

        $required = ['nama_kategori', 'nama', 'harga_beli', 'harga_jual', 'stok', 'stok_minimum', 'satuan'];
        $missing  = array_diff($required, $header);
        if (!empty($missing)) {
            return back()->with('error',
                'Kolom wajib tidak ditemukan: ' . implode(', ', $missing) .
                '. Pastikan menggunakan template yang benar.'
            );
        }

        // Cache kategori
        $categoryMap = Category::where('is_active', true)
            ->pluck('id', 'nama')
            ->mapWithKeys(fn($id, $nama) => [strtolower($nama) => $id])
            ->toArray();

        $sukses = 0;
        $gagal  = [];
        $rowNum = 1;

        if ($mode === 'replace') {
            Product::query()->delete();
        }

        foreach (array_slice($allRows, 1) as $row) {
            $rowNum++;

            // Skip baris kosong atau baris keterangan template (baris contoh)
            $rowStr = implode('', array_map('strval', $row));
            if (empty(trim($rowStr))) continue;

            // Map kolom
            $data = [];
            foreach ($header as $i => $col) {
                $data[$col] = trim((string)($row[$i] ?? ''));
            }

            // Skip baris keterangan (baris 2 template mengandung *...)
            $first = trim($data[$header[0]] ?? '');
            if (str_starts_with($first, '*') || str_starts_with($first, '#')) continue;

            // Validasi baris
            $validator = Validator::make($data, [
                'nama_kategori' => 'required|string',
                'nama'          => 'required|string|max:150',
                'harga_beli'    => 'required|numeric|min:0',
                'harga_jual'    => 'required|numeric|min:0',
                'stok'          => 'required|integer|min:0',
                'stok_minimum'  => 'required|integer|min:0',
                'satuan'        => 'required|string|max:20',
            ]);

            if ($validator->fails()) {
                $gagal[] = [
                    'baris'  => $rowNum,
                    'data'   => $data['nama'] ?? '(kosong)',
                    'alasan' => implode(', ', $validator->errors()->all()),
                ];
                continue;
            }

            $catKey = strtolower($data['nama_kategori']);
            $catId  = $categoryMap[$catKey] ?? null;

            if (!$catId) {
                $gagal[] = [
                    'baris'  => $rowNum,
                    'data'   => $data['nama'],
                    'alasan' => "Kategori \"{$data['nama_kategori']}\" tidak ditemukan. Pastikan nama kategori persis sama.",
                ];
                continue;
            }

            try {
                $productData = [
                    'category_id'  => $catId,
                    'nama'         => $data['nama'],
                    'merek'        => $data['merek']     ?? null,
                    'deskripsi'    => $data['deskripsi'] ?? null,
                    'harga_beli'   => (float) $data['harga_beli'],
                    'harga_jual'   => (float) $data['harga_jual'],
                    'stok'         => (int)   $data['stok'],
                    'stok_minimum' => (int)   $data['stok_minimum'],
                    'satuan'       => $data['satuan'],
                    'is_active'    => true,
                ];

                if ($mode === 'update') {
                    $existing = Product::whereRaw('LOWER(nama) = ?', [strtolower($data['nama'])])->first();
                    if ($existing) {
                        $existing->update($productData);
                    } else {
                        $productData['kode_produk'] = Product::generateKode();
                        Product::create($productData);
                    }
                } else {
                    $productData['kode_produk'] = Product::generateKode();
                    Product::create($productData);
                }

                $sukses++;
            } catch (\Exception $e) {
                $gagal[] = [
                    'baris'  => $rowNum,
                    'data'   => $data['nama'],
                    'alasan' => 'Error sistem: ' . $e->getMessage(),
                ];
            }
        }

        return redirect()->route('import.index')->with('import_result', [
            'tipe'   => 'Produk',
            'mode'   => $mode,
            'sukses' => $sukses,
            'gagal'  => $gagal,
        ]);
    }

    // ══════════════════════════════════════════════════════
    //  IMPORT PASIEN (dari Excel .xlsx)
    // ══════════════════════════════════════════════════════
    public function importPasien(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
            'mode' => 'required|in:tambah,update',
        ]);

        $mode = $request->mode;

        $rows = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
            public function array(array $array): array { return $array; }
        }, $request->file('file'));

        if (empty($rows[0])) {
            return back()->with('error', 'File Excel kosong atau tidak dapat dibaca.');
        }

        $allRows   = $rows[0];
        $rawHeader = $allRows[0] ?? [];
        $header    = array_map(fn($h) => strtolower(trim((string)$h)), $rawHeader);

        $required = ['nama'];
        $missing  = array_diff($required, $header);
        if (!empty($missing)) {
            return back()->with('error',
                'Kolom wajib tidak ditemukan: ' . implode(', ', $missing) .
                '. Pastikan menggunakan template yang benar.'
            );
        }

        $sukses = 0;
        $gagal  = [];
        $rowNum = 1;

        foreach (array_slice($allRows, 1) as $row) {
            $rowNum++;
            $rowStr = implode('', array_map('strval', $row));
            if (empty(trim($rowStr))) continue;

            $data = [];
            foreach ($header as $i => $col) {
                $data[$col] = trim((string)($row[$i] ?? ''));
            }

            $first = $data[$header[0]] ?? '';
            if (str_starts_with($first, '*') || str_starts_with($first, '#')) continue;

            $validator = Validator::make($data, [
                'nama'            => 'required|string|max:100',
                'tanggal_lahir'   => 'nullable|date',
                'jenis_kelamin'   => 'nullable|in:L,P',
                'no_hp'           => 'nullable|string|max:20',
                'no_bpjs'         => 'nullable|string|max:20',
                'email'           => 'nullable|email|max:100',
                'alamat'          => 'nullable|string',
                'riwayat_penyakit' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $gagal[] = [
                    'baris'  => $rowNum,
                    'data'   => $data['nama'] ?? '(kosong)',
                    'alasan' => implode(', ', $validator->errors()->all()),
                ];
                continue;
            }

            try {
                $tanggalLahir = null;
                if (!empty($data['tanggal_lahir'])) {
                    try {
                        $tanggalLahir = \Carbon\Carbon::parse($data['tanggal_lahir'])->format('Y-m-d');
                    } catch (\Exception $ex) {
                        // tanggal tidak valid, biarkan null
                    }
                }

                $patientData = [
                    'nama'             => $data['nama'],
                    'tanggal_lahir'    => $tanggalLahir,
                    'jenis_kelamin'    => $data['jenis_kelamin'] ?? null,
                    'no_hp'            => $data['no_hp']    ?? null,
                    'no_bpjs'          => $data['no_bpjs']  ?? null,
                    'email'            => $data['email']    ?? null,
                    'alamat'           => $data['alamat']   ?? null,
                    'riwayat_penyakit' => $data['riwayat_penyakit'] ?? null,
                ];

                if ($mode === 'update') {
                    $existing = Patient::whereRaw('LOWER(nama) = ?', [strtolower($data['nama'])])->first();
                    if ($existing) {
                        $existing->update($patientData);
                    } else {
                        $patientData['no_rm'] = Patient::generateNoRM();
                        Patient::create($patientData);
                    }
                } else {
                    $patientData['no_rm'] = Patient::generateNoRM();
                    Patient::create($patientData);
                }

                $sukses++;
            } catch (\Exception $e) {
                $gagal[] = [
                    'baris'  => $rowNum,
                    'data'   => $data['nama'],
                    'alasan' => 'Error sistem: ' . $e->getMessage(),
                ];
            }
        }

        return redirect()->route('import.index')->with('import_result', [
            'tipe'   => 'Pasien',
            'mode'   => $mode,
            'sukses' => $sukses,
            'gagal'  => $gagal,
        ]);
    }

    // ══════════════════════════════════════════════════════
    //  EXPORT PRODUK (Excel)
    // ══════════════════════════════════════════════════════
    public function exportProduk(Request $request)
    {
        $filename = 'data-produk-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new ProductExport($request), $filename);
    }

    // ══════════════════════════════════════════════════════
    //  EXPORT PASIEN (Excel)
    // ══════════════════════════════════════════════════════
    public function exportPasien(Request $request)
    {
        $filename = 'data-pasien-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new PatientExport($request), $filename);
    }
}
