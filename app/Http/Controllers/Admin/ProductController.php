<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\{Product, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%")
                  ->orWhere('merek', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stok') && $request->stok === 'menipis') {
            $query->whereColumn('stok', '<=', 'stok_minimum');
        }

        $products   = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = [
            'category_id'  => $request->category_id,
            'nama'         => $request->nama,
            'deskripsi'    => $request->deskripsi,
            'merek'        => $request->merek,
            'harga_beli'   => $request->harga_beli,
            'harga_jual'   => $request->harga_jual,
            'stok'         => $request->stok,
            'stok_minimum' => $request->stok_minimum,
            'satuan'       => $request->satuan,
            'is_active'    => $request->boolean('is_active', true),
            'kode_produk'  => Product::generateKode(),
        ];

        // Handle image upload
        if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
            $data['gambar'] = $request->file('gambar')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', "Produk {$data['nama']} berhasil ditambahkan.");
    }

    public function show(Product $product)
    {
        $product->load('category');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = [
            'category_id'  => $request->category_id,
            'nama'         => $request->nama,
            'deskripsi'    => $request->deskripsi,
            'merek'        => $request->merek,
            'harga_beli'   => $request->harga_beli,
            'harga_jual'   => $request->harga_jual,
            'stok'         => $request->stok,
            'stok_minimum' => $request->stok_minimum,
            'satuan'       => $request->satuan,
            'is_active'    => $request->boolean('is_active', true),
        ];

        // Handle image upload
        if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
            // Delete old image
            if ($product->gambar) {
                Storage::disk('public')->delete($product->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('products', 'public');
        } elseif ($request->boolean('hapus_gambar') && $product->gambar) {
            Storage::disk('public')->delete($product->gambar);
            $data['gambar'] = null;
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->gambar) {
            Storage::disk('public')->delete($product->gambar);
        }
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
