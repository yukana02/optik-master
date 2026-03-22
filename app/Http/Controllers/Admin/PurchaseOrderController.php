<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{PurchaseOrder, PurchaseOrderItem, Supplier, Product, StockMovement, ActivityLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'user']);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_po', 'like', "%$s%")
                  ->orWhereHas('supplier', fn($sq) => $sq->where('nama', 'like', "%$s%"));
            });
        }
        if ($request->filled('status')) { $query->where('status', $request->status); }
        $purchaseOrders = $query->latest()->paginate(15)->withQueryString();
        return view('admin.purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $products  = Product::where('is_active', true)->with('category')->get();
        return view('admin.purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'          => 'required|exists:suppliers,id',
            'tanggal_po'           => 'required|date',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.harga_beli'   => 'required|numeric|min:0',
            'catatan'              => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $totalHarga = 0;
            $itemsData  = [];

            foreach ($request->items as $item) {
                $product    = Product::findOrFail($item['product_id']);
                $subtotal   = $item['qty'] * $item['harga_beli'];
                $totalHarga += $subtotal;

                $itemsData[] = [
                    'product_id'  => $product->id,
                    'nama_produk' => $product->nama,
                    'qty'         => $item['qty'],
                    'harga_beli'  => $item['harga_beli'],
                    'subtotal'    => $subtotal,
                ];
            }

            $po = PurchaseOrder::create([
                'no_po'       => PurchaseOrder::generateNomor(),
                'supplier_id' => $request->supplier_id,
                'user_id'     => auth()->id(),
                'tanggal_po'  => $request->tanggal_po,
                'total_harga' => $totalHarga,
                'status'      => 'draft',
                'catatan'     => $request->catatan,
            ]);

            $po->items()->createMany($itemsData);
            ActivityLog::catat('purchase_order', "Buat PO #{$po->no_po}", $po->id);
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dibuat.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'user', 'items.product']);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'dikirim' && $purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Hanya PO berstatus draft atau dikirim yang bisa diterima.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            foreach ($purchaseOrder->items as $item) {
                if ($item->product) {
                    $stokSebelum = $item->product->stok;
                    $item->product->increment('stok', $item->qty);

                    StockMovement::create([
                        'product_id'     => $item->product_id,
                        'user_id'        => auth()->id(),
                        'tipe'           => 'masuk',
                        'qty'            => $item->qty,
                        'stok_sebelum'   => $stokSebelum,
                        'stok_sesudah'   => $stokSebelum + $item->qty,
                        'keterangan'     => "Penerimaan PO #{$purchaseOrder->no_po}",
                        'referensi_type' => PurchaseOrder::class,
                        'referensi_id'   => $purchaseOrder->id,
                    ]);

                    // Update harga beli produk
                    $item->product->update(['harga_beli' => $item->harga_beli]);
                }
            }

            $purchaseOrder->update([
                'status'         => 'diterima',
                'tanggal_terima' => now(),
            ]);

            ActivityLog::catat('purchase_order', "Terima PO #{$purchaseOrder->no_po}", $purchaseOrder->id);
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order berhasil diterima. Stok produk telah diperbarui.');
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'diterima') {
            return back()->with('error', 'PO yang sudah diterima tidak bisa dibatalkan.');
        }
        $purchaseOrder->update(['status' => 'batal']);
        ActivityLog::catat('purchase_order', "Batal PO #{$purchaseOrder->no_po}", $purchaseOrder->id);

        return back()->with('success', 'Purchase Order berhasil dibatalkan.');
    }
}
