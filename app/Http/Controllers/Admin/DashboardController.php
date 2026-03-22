<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Patient, Product, Transaction, MedicalRecord};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Stat cards
        $totalPasien   = Patient::count();
        $totalProduk   = Product::where('is_active', true)->count();
        $transaksiHari = Transaction::whereDate('created_at', today())
            ->where('status', 'lunas')->count();
        $omzetHari     = Transaction::whereDate('created_at', today())
            ->where('status', 'lunas')->sum('total_bayar');

        // Stok menipis
        $stokMenipis = Product::where('is_active', true)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->with('category')
            ->get();

        // Grafik omzet 7 hari — 1 query, bukan 7 query terpisah
        $startDate = now()->subDays(6)->startOfDay();
        $omzetRaw  = Transaction::where('status', 'lunas')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as tgl, SUM(total_bayar) as total')
            ->groupBy('tgl')
            ->pluck('total', 'tgl');

        $grafikOmzet = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date  = now()->subDays($i);
            $key   = $date->format('D');
            $dateStr = $date->format('Y-m-d');
            $grafikOmzet->put($key, $omzetRaw->get($dateStr, 0));
        }

        // Transaksi terbaru
        $transaksiTerbaru = Transaction::with(['patient', 'kasir'])
            ->latest()
            ->take(8)
            ->get();

        // Rekam medis terbaru
        $rekamTerbaru = MedicalRecord::with(['patient', 'dokter'])
            ->latest()
            ->take(5)
            ->get();

        // Top produk terjual bulan ini
        $topProduk = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereMonth('transactions.created_at', now()->month)
            ->where('transactions.status', 'lunas')
            ->whereNull('transactions.deleted_at')
            ->select('products.nama', DB::raw('SUM(transaction_items.qty) as total_qty'))
            ->groupBy('products.id', 'products.nama')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPasien', 'totalProduk', 'transaksiHari', 'omzetHari',
            'stokMenipis', 'grafikOmzet', 'transaksiTerbaru', 'rekamTerbaru', 'topProduk'
        ));
    }
}
