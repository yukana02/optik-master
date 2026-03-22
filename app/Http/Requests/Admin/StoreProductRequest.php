<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'category_id'  => 'required|exists:categories,id',
            'nama'         => 'required|string|max:150',
            'deskripsi'    => 'nullable|string',
            'merek'        => 'nullable|string|max:100',
            'harga_beli'   => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'satuan'       => 'required|string|max:20',
            'gambar'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'    => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak valid.',
            'nama.required'        => 'Nama produk wajib diisi.',
            'harga_beli.required'  => 'Harga beli wajib diisi.',
            'harga_jual.required'  => 'Harga jual wajib diisi.',
            'stok.required'        => 'Stok wajib diisi.',
            'gambar.image'         => 'File gambar tidak valid.',
            'gambar.max'           => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
