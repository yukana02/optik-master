<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Supplier, ActivityLog};
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%$s%")
                  ->orWhere('kode_supplier', 'like', "%$s%")
                  ->orWhere('telepon', 'like', "%$s%");
            });
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }
        $suppliers = $query->latest()->paginate(15)->withQueryString();
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'           => 'required|string|max:150',
            'kontak_person'  => 'nullable|string|max:100',
            'telepon'        => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'alamat'         => 'nullable|string',
            'is_active'      => 'boolean',
        ]);
        $validated['kode_supplier'] = Supplier::generateKode();
        $validated['is_active']     = $request->boolean('is_active', true);

        $supplier = Supplier::create($validated);
        ActivityLog::catat('supplier', "Tambah supplier {$supplier->nama}", $supplier->id);

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier {$supplier->nama} berhasil ditambahkan.");
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('purchaseOrders.user');
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:150',
            'kontak_person' => 'nullable|string|max:100',
            'telepon'       => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'alamat'        => 'nullable|string',
            'is_active'     => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $supplier->update($validated);
        ActivityLog::catat('supplier', "Update supplier {$supplier->nama}", $supplier->id);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        ActivityLog::catat('supplier', "Hapus supplier {$supplier->nama}");
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}
