<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Transaction, TransactionItem, Patient, Product, MedicalRecord, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['patient', 'kasir']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                    ->orWhereHas('patient', fn($p) => $p->where('nama', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();
        return view('admin.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('stok', '>', 0)
            ->with('category')
            ->get();
        $patients = Patient::orderBy('nama')->get(['id', 'no_rm', 'nama', 'no_bpjs']);
        $medRecs = collect();

        return view('admin.transactions.create', compact('products', 'patients', 'medRecs'));
    }

    public function store(Request $request)
    {
        $data = $request->input('transaction_data');

        try {
            DB::beginTransaction();

            // =========================
            // 1. PATIENT
            // =========================
            $patientData = $data['patient'] ?? [];
            $patient = null;

            if (!empty($patientData['nama'])) {
                if (!empty($patientData['id'])) {
                    $patient = Patient::find($patientData['id']);
                    if ($patient) {
                        $patient->update([
                            'nama'    => $patientData['nama'],
                            'telp'    => $patientData['telp'] ?? null,
                            'alamat'  => $patientData['alamat'] ?? null,
                            'no_bpjs' => $patientData['no_bpjs'] ?? null,
                        ]);
                    }
                }
                
                if (!$patient) {
                    $patient = Patient::create([
                        'no_rm'   => Patient::generateNoRM(),
                        'nama'    => $patientData['nama'],
                        'telp'    => $patientData['telp'] ?? null,
                        'alamat'  => $patientData['alamat'] ?? null,
                        'no_bpjs' => $patientData['no_bpjs'] ?? null,
                    ]);
                }
            }

            // =========================
            // 2. PEMBAYARAN & STATUS
            // =========================
            $pembayaran = $data['pembayaran'] ?? [];
            $hargaJual = $pembayaran['harga_jual'] ?? 0;
            $potongan = $pembayaran['diskon'] ?? 0;
            $dp = $pembayaran['dp'] ?? 0;
            $sisa = $pembayaran['sisa'] ?? 0;
            $bayar = $pembayaran['bayar'] ?? 0; // Legacy
            if ($dp == 0 && $bayar > 0) $dp = $bayar; // Legacy support
            
            $totalBayar = max(0, $hargaJual - $potongan);
            $totalMasuk = $dp + $bayar;
            
            $kembalian = 0;
            if ($sisa < 0) {
                $kembalian = abs($sisa);
                $sisa = 0;
            }

            $status = 'pending';
            if ($totalMasuk == 0) {
                $status = 'unpaid';
            } elseif ($sisa > 0) {
                $status = 'dp';
            } else {
                $status = 'paid';
            }

            // =========================
            // 3. PRODUCT SNAPSHOT (First Item)
            // =========================
            $items = $data['items'] ?? [];
            $lensa = null;
            $frame = null;
            foreach ($items as $item) {
                $type = strtolower($item['type'] ?? '');
                if ($type == 'lensa' || str_contains(strtolower($item['nama']), 'lensa')) {
                    if (!$lensa) $lensa = $item['nama'];
                } elseif ($type == 'frame' || str_contains(strtolower($item['nama']), 'frame')) {
                    if (!$frame) $frame = $item['nama'];
                }
            }
            if (!$lensa) $lensa = $data['tambahan']['lensa'] ?? null;

            // =========================
            // 4. SIMPAN TRANSACTION
            // =========================
            $trxData = $data['transaksi'] ?? [];
            $resep = $data['resep'] ?? [];
            $mata = $resep['mata'] ?? [];
            $jadwal = $data['jadwal'] ?? [];
            $tambahan = $data['tambahan'] ?? [];

            $transaction = Transaction::create([
                // Identifier
                'no_transaksi' => $trxData['no_transaksi'],
                'patient_id'   => $patient ? $patient->id : null,
                'user_id'      => auth()->id(),
                
                // Dates
                'tgl_order'         => $trxData['tgl_order'] ?? date('Y-m-d'),
                'tgl_faktur'        => $trxData['tgl_faktur'] ?? date('Y-m-d'),
                'tgl_selesai_janji' => $jadwal['tgl_selesai_janji'] ?? null,

                // Status
                'typefaktur' => $trxData['tipe_faktur'] ?? 1,
                'diambil'    => $trxData['diambil'] ?? 2,
                'status'     => $status,

                // Money
                'harga_jual'  => $hargaJual,
                'potongan'    => $potongan,
                'total_bayar' => $totalBayar,
                'dp'          => $dp,
                'sisa'        => $sisa,
                'kembalian'   => $kembalian,

                // Snapshot Patient
                'nama_pasien'   => $patientData['nama'] ?? null,
                'no_bpjs'       => $patientData['no_bpjs'] ?? null,
                'telp_pasien'   => $patientData['telp'] ?? null,
                'alamat_pasien' => $patientData['alamat'] ?? null,
                'asal_resep'    => $resep['asal'] ?? null,

                // Snapshot Refraksi
                'od_sph'   => $mata['od']['sph'] ?? null,
                'od_cyl'   => $mata['od']['cyl'] ?? null,
                'od_axis'  => $mata['od']['axis'] ?? null,
                'od_add'   => $mata['od']['add'] ?? null,
                'od_mpd'   => $mata['od']['mpd'] ?? null,
                'od_prism' => $mata['od']['prism'] ?? null,
                'os_sph'   => $mata['os']['sph'] ?? null,
                'os_cyl'   => $mata['os']['cyl'] ?? null,
                'os_axis'  => $mata['os']['axis'] ?? null,
                'os_add'   => $mata['os']['add'] ?? null,
                'os_mpd'   => $mata['os']['mpd'] ?? null,
                'os_prism' => $mata['os']['prism'] ?? null,

                // Snapshot Product Info
                'lensa'            => $lensa,
                'nama_produk'      => $frame,
                'keterangan_frame' => $tambahan['keterangan_frame'] ?? null,

                // Lab & Production
                'no_legalisasi'     => $tambahan['no_legalisasi'] ?? null,
                'tgl_legalisasi'    => $tambahan['tgl_legalisasi'] ?? null,
                'lab'               => $tambahan['lab'] ?? null,
                'tempat_faset'      => $tambahan['tempat_faset'] ?? null,
                'tgl_faset'         => $jadwal['tgl_faset'] ?? null,
                'tgl_datang_faset'  => $jadwal['tgl_datang_faset'] ?? null,
                'tgl_selesai_faset' => $jadwal['tgl_selesai_faset'] ?? null,

                // Extra Data
                'catatan' => $tambahan['catatan'] ?? null,
                'resep'   => $resep, // Store raw JSON just in case
            ]);

            // =========================
            // 5. SIMPAN ITEMS
            // =========================
            foreach ($items as $item) {
                // Deduct Stoks
                if (!empty($item['product_id'])) {
                    $product = Product::lockForUpdate()->find($item['product_id']);
                    if ($product && $product->stok > 0) {
                        $product->decrement('stok', $item['qty']);
                    }
                }

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $item['product_id'] ?? null,
                    'nama_produk'    => $item['nama'],
                    'seri'           => $item['seri'] ?? null,
                    'warna'          => $item['warna'] ?? null,
                    'qty'            => $item['qty'],
                    'harga_satuan'   => $item['harga'],
                    'subtotal'       => $item['harga'] * $item['qty'],
                    'keterangan'     => $item['keterangan'] ?? null,
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success', 
                'message' => 'Transaksi berhasil disimpan.', 
                'data' => $transaction
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error', 
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['patient', 'kasir', 'medicalRecord', 'items.product.category']);
        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Render a specialized print template.
     * URL: /transactions/{transaction}/print?type=garansi
     *
     * === CARA MENAMBAHKAN TEMPLATE CETAK BARU ===
     * 1. Buat file blade di: resources/views/admin/transactions/print/{nama}.blade.php
     * 2. Tambahkan key baru di array $views di bawah ini.
     * 3. Di modal Print Hub (create.blade.php), tambahkan tombol:
     *      <button onclick="doPrint('{nama}')">Cetak {Nama}</button>
     */
    public function printView(Transaction $transaction, Request $request)
    {
        $transaction->load(['patient', 'kasir', 'medicalRecord', 'items.product.category']);

        $type = $request->query('type', 'garansi');

        // Daftar template — tambahkan di sini untuk template baru
        $views = [
            'garansi'        => 'admin.transactions.print.garansi',
            'fasetan'        => 'admin.transactions.print.fasetan',
            'bon_3_rangkap'  => 'admin.transactions.print.fasetan',
            'pesanan_besar'  => 'admin.transactions.print.pesanan_besar',
            'formulir_bpjs'  => 'admin.transactions.print.formulir_bpjs',
            // 'bon_1_rangkap'  => 'admin.transactions.print.bon_1_rangkap',
        ];

        $viewName = $views[$type] ?? $views['garansi'];
        $copies = ($type === 'bon_3_rangkap') ? 3 : 1;

        return view($viewName, compact('transaction', 'copies'));
    }

    public function cancel(Transaction $transaction)
    {
        if ($transaction->status === 'batal') {
            return back()->with('error', 'Transaksi sudah dibatalkan.');
        }

        DB::transaction(function () use ($transaction) {
            // Kembalikan stok
            foreach ($transaction->items as $item) {
                if ($item->product) {
                    $item->product->increment('stok', $item->qty);
                }
            }
            $transaction->update(['status' => 'batal']);
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
    }

    // AJAX — cari produk di POS
    public function searchProduct(Request $request)
    {
        $products = Product::where('is_active', true)
            ->where('stok', '>', 0)
            ->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->q}%")
                    ->orWhere('kode_produk', 'like', "%{$request->q}%")
                    ->orWhere('merek', 'like', "%{$request->q}%");
            })
            ->with('category')
            ->limit(10)
            ->get(['id', 'kode_produk', 'nama', 'merek', 'harga_jual', 'stok', 'category_id']);

        return response()->json($products);
    }

    // AJAX — ambil rekam medis berdasarkan pasien
    public function getMedicalRecords(Request $request)
    {
        if (!$request->patient_id) {
            return response()->json([]);
        }

        $records = MedicalRecord::where('patient_id', $request->patient_id)
            ->latest()
            ->take(10)
            ->get(['id', 'tanggal_kunjungan', 'od_sph', 'os_sph'])
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'tanggal_kunjungan' => $r->tanggal_kunjungan->format('d M Y'),
                    'od_sph' => $r->od_sph ?? '-',
                    'os_sph' => $r->os_sph ?? '-',
                ];
            });

        return response()->json($records);
    }

    // --- ADVANCED POS API ---

    public function posNav(Request $request)
    {
        $dir = $request->dir; // awal, sebelum, sesudah, akhir
        $currentId = $request->current_id;

        $query = Transaction::with(['patient', 'items.product', 'medicalRecord']);

        if ($dir == 'awal') {
            $trx = $query->orderBy('id', 'asc')->first();
        } elseif ($dir == 'akhir') {
            $trx = $query->orderBy('id', 'desc')->first();
        } elseif ($dir == 'sebelum' && $currentId) {
            $trx = $query->where('id', '<', $currentId)->orderBy('id', 'desc')->first();
        } elseif ($dir == 'sesudah' && $currentId) {
            $trx = $query->where('id', '>', $currentId)->orderBy('id', 'asc')->first();
        } else {
            $trx = null;
        }

        return response()->json($trx ?: null);
    }

    public function posSearch(Request $request)
    {
        $q = $request->q;
        $query = Transaction::with('patient')->latest();

        if ($q) {
            $query->where('no_transaksi', 'like', "%{$q}%")
                ->orWhereHas('patient', function ($p) use ($q) {
                    $p->where('nama', 'like', "%{$q}%")
                        ->orWhere('no_bpjs', 'like', "%{$q}%");
                });
        }

        $transactions = $query->paginate(10)->through(function ($t) {
            return [
                'id' => $t->id,
                'no_transaksi' => $t->no_transaksi,
                'tanggal' => $t->created_at->format('d/m/Y'),
                'pasien' => $t->patient ? $t->patient->nama : ($t->nama_pasien ?? '-'),
                'total' => 'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
            ];
        });

        return response()->json($transactions);
    }

    public function posSave(Request $request)
    {
        // Remove dots from currency
        $request->merge([
            'harga_jual' => (int) str_replace('.', '', $request->harga_jual),
            'dp' => (int) str_replace('.', '', $request->dp),
            'potongan' => (int) str_replace('.', '', $request->potongan),
        ]);

        try {
            DB::beginTransaction();

            // Handle Patient
            $patient = null;
            if ($request->patient_id) {
                $patient = Patient::find($request->patient_id);
                // Only update if nama is provided, otherwise keep existing patient but don't crash
                // or if name is cleared, treat as UMUM
                if ($patient && $request->nama) {
                    $patient->update([
                        'nama' => $request->nama,
                        'no_bpjs' => $request->no_bpjs,
                        'no_hp' => $request->telp,
                        'alamat' => $request->alamat,
                    ]);
                } elseif (!$request->nama) {
                    $patient = null; // Treat as UMUM if name is cleared
                }
            } elseif ($request->nama) {
                // Try to find if exists
                $patient = Patient::where('nama', $request->nama)->where('no_hp', $request->telp)->first();
                if (!$patient) {
                    $patient = Patient::create([
                        'no_rm' => Patient::generateNoRM(),
                        'nama' => $request->nama,
                        'no_bpjs' => $request->no_bpjs,
                        'no_hp' => $request->telp,
                        'alamat' => $request->alamat,
                    ]);
                }
            }

            // Product input array handling (first item to transaction fields)
            $kodeFrame = is_array($request->kode_frame) ? ($request->kode_frame[0] ?? null) : $request->kode_frame;
            $namaProduk = is_array($request->nama_produk) ? ($request->nama_produk[0] ?? null) : $request->nama_produk;
            $seri = is_array($request->seri) ? ($request->seri[0] ?? null) : $request->seri;
            $warna = is_array($request->warna) ? ($request->warna[0] ?? null) : $request->warna;
            $keterangan = is_array($request->keterangan) ? ($request->keterangan[0] ?? null) : $request->keterangan;

            // Transaction Data array
            $data = [
                'patient_id' => $patient ? $patient->id : null,
                'user_id' => auth()->id(),
                'status' => 'lunas',
                'metode_bayar' => 'tunai',

                // Fields
                'tgl_order' => $request->tgl_order ?? date('Y-m-d'),
                'no_legalisasi' => $request->no_legalisasi,
                'tgl_legalisasi' => $request->tgl_legalisasi,
                'tgl_faset' => $request->tgl_faset,
                'lab' => $request->lab,
                'tempat_faset' => $request->tempat_faset,
                'tgl_datang_faset' => $request->tgl_datang_faset,
                'tgl_selesai_faset' => $request->tgl_selesai_faset,
                'tgl_selesai_janji' => $request->tgl_selesai_janji,
                'catatan' => $request->catatan,

                'od_sph' => $request->od_sph,
                'od_cyl' => $request->od_cyl,
                'od_axis' => $request->od_axis,
                'od_add' => $request->od_add,
                'od_mpd' => $request->od_mpd,
                'od_prism' => $request->od_prism,
                'os_sph' => $request->os_sph,
                'os_cyl' => $request->os_cyl,
                'os_axis' => $request->os_axis,
                'os_add' => $request->os_add,
                'os_mpd' => $request->os_mpd,
                'os_prism' => $request->os_prism,

                'no_bpjs' => $request->no_bpjs,
                'nama_pasien' => $request->nama,
                'alamat_pasien' => $request->alamat,
                'telp_pasien' => $request->telp,
                'asal_resep' => $request->asal_resep,

                'lensa' => $request->lensa,
                'kode_frame' => $kodeFrame,
                'nama_produk' => $namaProduk,
                'keterangan_frame' => $keterangan,
                'seri' => $seri,
                'warna' => $warna,
                'typefaktur' => $request->typefaktur,
                'diambil' => $request->diambil,

                // Money
                'harga_jual' => $request->harga_jual,
                'dp' => $request->dp,
                'potongan' => $request->potongan,
                'sisa' => max(0, $request->harga_jual - $request->dp - $request->potongan),

                // Legacy fallback overrides mapping if empty (so show views don't break entirely)
                'total_harga' => $request->harga_jual ?? 0,
                'total_bayar' => max(0, ($request->harga_jual ?? 0) - ($request->potongan ?? 0)),
                'bayar' => $request->dp ?? 0,
                'diskon_nominal' => $request->potongan ?? 0,
            ];

            if ($request->id) {
                // Update
                $trx = Transaction::findOrFail($request->id);
                $trx->update($data);
                $msg = 'Transaksi berhasil diupdate';

                // For update, we might want to restore old stock before applying new ones
                // but for POS simplicity, we'll just handle the items clear/create
                foreach ($trx->items as $oldItem) {
                    if ($oldItem->product) {
                        $oldItem->product->increment('stok', $oldItem->qty);
                    }
                }
            } else {
                // Create with retry logic for duplicate numbers
                $maxRetries = 5;
                $retryCount = 0;
                $trx = null;

                while ($retryCount < $maxRetries) {
                    try {
                        $data['no_transaksi'] = Transaction::generateNomor();
                        $trx = Transaction::create($data);
                        break;
                    } catch (\Illuminate\Database\QueryException $e) {
                        if ($e->errorInfo[1] == 1062) { // Duplicate entry
                            $retryCount++;
                            if ($retryCount >= $maxRetries)
                                throw $e;
                            usleep(100000); // Wait 100ms before retry
                        } else {
                            throw $e;
                        }
                    }
                }
                $msg = 'Transaksi berhasil disimpan';
            }

            // Product / Items updating (from cart_data)
            $trx->items()->delete();

            $cartData = json_decode($request->cart_data, true) ?: [];
            if (is_array($cartData) && count($cartData) > 0) {
                foreach ($cartData as $item) {
                    if (empty($item['kode']))
                        continue;

                    $product = Product::where('kode_produk', $item['kode'])->lockForUpdate()->first();
                    $qty = max(1, intval($item['qty'] ?? 1));

                    if ($product) {
                        if ($product->stok < $qty) {
                            throw new \Exception("Stok produk '{$product->nama}' tidak mencukupi (Tersisa: {$product->stok})");
                        }
                        $product->decrement('stok', $qty);
                    }

                    $trx->items()->create([
                        'product_id' => $product ? $product->id : null,
                        'nama_produk' => $item['nama'] ?? ($product->nama ?? 'Unknown'),
                        'qty' => $qty,
                        'harga_satuan' => floatval($item['harga'] ?? 0),
                        'subtotal' => floatval($item['harga'] ?? 0) * $qty
                    ]);
                }
            } else {
                // Fallback to single frame/product if cart is empty
                $kodeFrames = is_array($request->kode_frame) ? $request->kode_frame : [$request->kode_frame];
                foreach ($kodeFrames as $idx => $kode) {
                    if (!$kode)
                        continue;
                    $product = Product::where('kode_produk', $kode)->lockForUpdate()->first();
                    if ($product) {
                        if ($product->stok < 1) {
                            throw new \Exception("Stok frame '{$product->nama}' habis.");
                        }
                        $product->decrement('stok', 1);
                    }

                    $trx->items()->create([
                        'product_id' => $product ? $product->id : null,
                        'nama_produk' => $product ? $product->nama : 'Frame/Produk',
                        'qty' => 1,
                        'harga_satuan' => $request->harga_jual,
                        'subtotal' => $request->harga_jual
                    ]);
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => $msg, 'data' => $trx]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function posDelete($id)
    {
        try {
            $trx = Transaction::findOrFail($id);
            if ($trx->items()->count() > 0) {
                $trx->items()->delete();
            }
            $trx->delete();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function patientAutocomplete(Request $request)
    {
        $q = $request->q;
        $patients = Patient::where('no_bpjs', 'like', "%{$q}%")
            ->orWhere('nama', 'like', "%{$q}%")
            ->orWhere('no_hp', 'like', "%{$q}%")
            ->take(10)->get();
        return response()->json($patients);
    }

    public function frameAutocomplete(Request $request)
    {
        $q = $request->q;
        $products = Product::where('category_id', 1) // Frame Kacamata
            ->where(function($query) use ($q) {
                $query->where('kode_produk', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%")
                    ->orWhere('merek', 'like', "%{$q}%");
            })
            ->take(10)->get();
        return response()->json($products);
    }

    public function lensaAutocomplete(Request $request)
    {
        $q = $request->q;
        $products = Product::whereIn('category_id', [2, 4]) // Lensa Kacamata & Lensa Kontak
            ->where(function($query) use ($q) {
                $query->where('kode_produk', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%")
                    ->orWhere('merek', 'like', "%{$q}%");
            })
            ->take(10)->get();
        return response()->json($products);
    }

    public function doctorAutocomplete(Request $request)
    {
        $q = $request->q;

        $doctors = User::where('role', 'doctor')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($doctors);
    }
}
