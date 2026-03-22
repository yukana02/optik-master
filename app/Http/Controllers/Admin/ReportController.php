<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Transaction, Product, TransactionItem, StockMovement, Patient};
use App\Exports\{PenjualanExport, StokExport, MutasiStokExport, PatientExport, ProdukTerlarisExport};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    // ─── Laporan Penjualan ─────────────────────────────────────────
    public function penjualan(Request $request)
    {
        $from       = $request->from       ?? now()->startOfMonth()->format('Y-m-d');
        $to         = $request->to         ?? now()->format('Y-m-d');
        $tipePasien = $request->tipe_pasien ?? '';

        // [4] Export Excel
        if ($request->export === 'excel') {
            $filename = 'laporan-penjualan-' . $from . '-sd-' . $to
                      . ($tipePasien ? "-{$tipePasien}" : '') . '.xlsx';
            return Excel::download(new PenjualanExport($from, $to, $tipePasien), $filename);
        }

        $query = Transaction::with(['patient', 'kasir', 'items'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->where('status', 'lunas');

        // [5] Filter tipe pasien
        if ($tipePasien) {
            $query->where('tipe_pasien', $tipePasien);
        }

        $transactions = $query->latest()->get();

        $summary = [
            'total_transaksi' => $transactions->count(),
            'total_omzet'     => $transactions->sum('total_bayar'),
            'total_diskon'    => $transactions->sum('diskon_nominal'),
            'total_subsidi'   => $transactions->sum('subsidi_bpjs'),
            'rata_per_trx'    => $transactions->count()
                ? round($transactions->sum('total_bayar') / $transactions->count()) : 0,
        ];

        $grafik = $transactions->groupBy(fn($t) => $t->created_at->format('d/m'))
            ->map(fn($g) => $g->sum('total_bayar'));

        return view('admin.reports.penjualan',
            compact('transactions', 'summary', 'from', 'to', 'grafik', 'tipePasien'));
    }

    // ─── Produk Terlaris ──────────────────────────────────────────
    public function produkTerlaris(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to   ?? now()->format('Y-m-d');

        // [4] Export Excel
        if ($request->export === 'excel') {
            return Excel::download(new ProdukTerlarisExport($from, $to),
                "produk-terlaris-{$from}-sd-{$to}.xlsx");
        }

        $products = TransactionItem::with('product.category')
            ->whereHas('transaction', fn($q) =>
                $q->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
                  ->where('status', 'lunas')
            )
            ->select('product_id',
                DB::raw('SUM(qty) as total_terjual'),
                DB::raw('SUM(subtotal) as total_pendapatan')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_terjual')
            ->take(15)
            ->get();

        return view('admin.reports.produk-terlaris', compact('products', 'from', 'to'));
    }

    // ─── Laporan Stok ─────────────────────────────────────────────
    public function stok(Request $request)
    {
        // [4] Export Excel
        if ($request->export === 'excel') {
            return Excel::download(new StokExport(), 'laporan-stok-' . now()->format('Ymd') . '.xlsx');
        }

        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('stok')
            ->get();

        $summary = [
            'total_produk' => $products->count(),
            'stok_menipis' => $products->filter(fn($p) => $p->stok <= $p->stok_minimum && $p->stok > 0)->count(),
            'stok_habis'   => $products->where('stok', 0)->count(),
            'nilai_stok'   => $products->sum(fn($p) => $p->stok * $p->harga_beli),
        ];

        return view('admin.reports.stok', compact('products', 'summary'));
    }

    // ─── Mutasi Stok ─────────────────────────────────────────────
    public function mutasiStok(Request $request)
    {
        // [4] Export Excel
        if ($request->export === 'excel') {
            return Excel::download(new MutasiStokExport($request),
                'mutasi-stok-' . now()->format('Ymd-His') . '.xlsx');
        }

        $query = StockMovement::with(['product', 'user'])->latest();

        if ($request->filled('product_id')) $query->where('product_id', $request->product_id);
        if ($request->filled('tipe'))        $query->where('tipe', $request->tipe);
        if ($request->filled('from'))        $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))          $query->whereDate('created_at', '<=', $request->to);

        $movements   = $query->paginate(20)->withQueryString();
        $productList = Product::where('is_active', true)->orderBy('nama')->get(['id', 'nama', 'kode_produk']);

        return view('admin.reports.mutasi-stok', compact('movements', 'productList'));
    }

    // ─── Export Pasien Excel (dipanggil dari route export.pasien) ──
    public function exportPasienExcel(Request $request)
    {
        return Excel::download(new PatientExport($request),
            'data-pasien-' . now()->format('Ymd') . '.xlsx');
    }

    // ─── Print Views ─────────────────────────────────────────────
    public function printPenjualan(Request $request)
    {
        $from       = $request->from       ?? now()->startOfMonth()->format('Y-m-d');
        $to         = $request->to         ?? now()->format('Y-m-d');
        $tipePasien = $request->tipe_pasien ?? '';

        $query = Transaction::with(['patient', 'kasir', 'items'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->where('status', 'lunas');

        if ($tipePasien) $query->where('tipe_pasien', $tipePasien);

        $transactions = $query->latest()->get();

        $summary = [
            'total_transaksi' => $transactions->count(),
            'total_omzet'     => $transactions->sum('total_bayar'),
            'total_diskon'    => $transactions->sum('diskon_nominal'),
            'total_subsidi'   => $transactions->sum('subsidi_bpjs'),
        ];

        return view('admin.reports.print-penjualan',
            compact('transactions', 'summary', 'from', 'to', 'tipePasien'));
    }

    public function printStok()
    {
        $products = Product::with('category')->where('is_active', true)->orderBy('stok')->get();
        $summary  = [
            'total_produk' => $products->count(),
            'stok_menipis' => $products->filter(fn($p) => $p->stok <= $p->stok_minimum && $p->stok > 0)->count(),
            'stok_habis'   => $products->where('stok', 0)->count(),
            'nilai_stok'   => $products->sum(fn($p) => $p->stok * $p->harga_beli),
        ];
        return view('admin.reports.print-stok', compact('products', 'summary'));
    }

    public function printMutasiStok(Request $request)
    {
        $query = StockMovement::with(['product', 'user'])->latest();
        if ($request->filled('product_id')) $query->where('product_id', $request->product_id);
        if ($request->filled('tipe'))        $query->where('tipe', $request->tipe);
        if ($request->filled('from'))        $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))          $query->whereDate('created_at', '<=', $request->to);

        $movements   = $query->get();
        $productList = Product::orderBy('nama')->get(['id', 'nama', 'kode_produk']);
        return view('admin.reports.print-mutasi-stok', compact('movements', 'productList'));
    }
}
