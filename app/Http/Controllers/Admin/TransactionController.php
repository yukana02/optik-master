<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTransactionRequest;
use App\Models\{Transaction, TransactionItem, Patient, Product, MedicalRecord, StockMovement, ActivityLog};
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

        if ($request->filled('status'))  { $query->where('status', $request->status); }
        if ($request->filled('from'))    { $query->whereDate('created_at', '>=', $request->from); }
        if ($request->filled('to'))      { $query->whereDate('created_at', '<=', $request->to); }

        $transactions = $query->latest()->paginate(15)->withQueryString();
        return view('admin.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $categories = \App\Models\Category::where('is_active', true)
            ->withCount(['activeProducts' => fn($q) => $q->where('stok', '>', 0)])
            ->having('active_products_count', '>', 0)
            ->get();

        $products = Product::where('is_active', true)
            ->where('stok', '>', 0)
            ->with('category')
            ->get();

        $patients = Patient::orderBy('nama')->get(['id', 'no_rm', 'nama']);

        return view('admin.transactions.create', compact('products', 'categories', 'patients'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $newTransaction = null;
        DB::transaction(function () use ($request, &$newTransaction) {
            $totalHarga     = 0;
            $itemsData      = [];
            $productsToMove = [];

            foreach ($request->items as $item) {
                $product    = Product::lockForUpdate()->findOrFail($item['product_id']);
                $diskonItem = (float) ($item['diskon'] ?? 0);

                if ($product->stok < $item['qty']) {
                    throw new \Exception(
                        "Stok produk '{$product->nama}' tidak mencukupi. Stok saat ini: {$product->stok}"
                    );
                }

                $subtotal    = ($item['harga_satuan'] * $item['qty']) - $diskonItem;
                $totalHarga += $subtotal;

                $itemsData[] = [
                    'product_id'   => $product->id,
                    'nama_produk'  => $product->nama,
                    'qty'          => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'diskon'       => $diskonItem,
                    'subtotal'     => $subtotal,
                ];

                $productsToMove[] = ['product' => $product, 'qty' => $item['qty']];
                $product->decrement('stok', $item['qty']);
            }

            $diskonPersen  = (float) ($request->diskon_persen  ?? 0);
            $diskonNominal = (float) ($request->diskon_nominal ?? 0);
            if ($diskonPersen > 0) {
                $diskonNominal = round($totalHarga * ($diskonPersen / 100));
            }

            $tipePasien  = $request->tipe_pasien ?? 'umum';
            $subsidiBpjs = ($tipePasien === 'bpjs') ? (float) ($request->subsidi_bpjs ?? 0) : 0;
            $totalBayar  = max(0, $totalHarga - $diskonNominal - $subsidiBpjs);
            $kembalian   = $request->bayar - $totalBayar;

            if ($request->metode_bayar === 'tunai' && $request->bayar < $totalBayar) {
                throw new \Exception('Jumlah uang bayar kurang dari total yang harus dibayar.');
            }

            $transaction = Transaction::create([
                'no_transaksi'      => Transaction::generateNomor(),
                'patient_id'        => $request->patient_id ?: null,
                'tipe_pasien'       => $tipePasien,
                'no_bpjs'           => ($tipePasien === 'bpjs') ? $request->no_bpjs : null,
                'subsidi_bpjs'      => $subsidiBpjs,
                'user_id'           => auth()->id(),
                'medical_record_id' => $request->medical_record_id ?: null,
                'total_harga'       => $totalHarga,
                'diskon_persen'     => $diskonPersen,
                'diskon_nominal'    => $diskonNominal,
                'total_bayar'       => $totalBayar,
                'bayar'             => $request->bayar,
                'kembalian'         => max(0, $kembalian),
                'metode_bayar'      => $request->metode_bayar,
                'status'            => 'lunas',
                'catatan'           => $request->catatan,
            ]);

            $transaction->items()->createMany($itemsData);
            $newTransaction = $transaction;

            foreach ($productsToMove as $move) {
                StockMovement::catat(
                    $move['product'],
                    'keluar',
                    $move['qty'],
                    "Penjualan #{$transaction->no_transaksi}",
                    $transaction
                );
            }

            ActivityLog::catat('transaksi', "Transaksi baru #{$transaction->no_transaksi}", $transaction->id);
        });

        return redirect()->route('transactions.invoice', $newTransaction)
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['patient', 'kasir', 'medicalRecord', 'items.product']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function invoice(Transaction $transaction)
    {
        $transaction->load(['patient', 'kasir', 'medicalRecord', 'items.product']);
        return view('admin.transactions.invoice', compact('transaction'));
    }

    public function cancel(Transaction $transaction)
    {
        if ($transaction->status === 'batal') {
            return back()->with('error', 'Transaksi sudah dibatalkan.');
        }

        DB::transaction(function () use ($transaction) {
            foreach ($transaction->items as $item) {
                if ($item->product) {
                    $item->product->increment('stok', $item->qty);
                    StockMovement::catat(
                        $item->product,
                        'retur',
                        $item->qty,
                        "Pembatalan transaksi #{$transaction->no_transaksi}",
                        $transaction
                    );
                }
            }
            $transaction->update(['status' => 'batal']);
            ActivityLog::catat('transaksi', "Transaksi dibatalkan #{$transaction->no_transaksi}", $transaction->id);
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
    }

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

    public function getMedicalRecords(Request $request)
    {
        if (!$request->patient_id) return response()->json([]);

        $records = MedicalRecord::where('patient_id', $request->patient_id)
            ->latest()
            ->take(10)
            ->get(['id', 'tanggal_kunjungan', 'od_sph', 'os_sph'])
            ->map(fn($r) => [
                'id'                => $r->id,
                'tanggal_kunjungan' => $r->tanggal_kunjungan->format('d M Y'),
                'od_sph'            => $r->od_sph ?? '-',
                'os_sph'            => $r->os_sph ?? '-',
            ]);

        return response()->json($records);
    }
}
