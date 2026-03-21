<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'   => 'required|exists:categories,id',
            'nama'          => 'required|string|max:255',
            'merek'         => 'nullable|string|max:255',
            'deskripsi'     => 'nullable|string',
            'harga_beli'    => 'required|numeric|min:0',
            'harga_jual'    => 'required|numeric|min:0',
            'stok'          => 'required|integer|min:0',
            'stok_minimum'  => 'nullable|integer|min:0',
            'satuan'        => 'nullable|string|max:50',
            'is_active'     => 'nullable|boolean',
            // ↓ INI YANG KURANG — field gambar harus ada di rules
            'gambar'        => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            // field untuk hapus gambar lama (dari edit form)
            'hapus_gambar'  => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'kategori',
            'nama'        => 'nama produk',
            'harga_beli'  => 'harga beli',
            'harga_jual'  => 'harga jual',
            'stok'        => 'stok',
            'gambar'      => 'foto produk',
        ];
    }

    public function messages(): array
    {
        return [
            'gambar.image'    => 'File harus berupa gambar.',
            'gambar.mimes'    => 'Format gambar harus JPG, PNG, atau WEBP.',
            'gambar.max'      => 'Ukuran gambar maksimal 2 MB.',
        ];
    }
}
